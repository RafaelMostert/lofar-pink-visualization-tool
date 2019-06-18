__author__ = "Rafaël Mostert"
__credits__ = ["Rafaël Mostert","Kai Polsterer"]
__email__ = "mostert@strw.leidenuniv.nl"

from astropy import units as u
from astropy.wcs import WCS
from astropy.coordinates import SkyCoord
import astropy.visualization as vis
import matplotlib.pyplot as plt
from matplotlib.patches import Rectangle
import numpy as np
import os
import pandas as pd
import seaborn as sns
import struct
import sys
import warnings
import time
import pickle

"""
Notes on terminology.
    BMU: Best Matching Unit
    SOM: Self-Organizing Map
    unit, node, prototype: A neuron of a SOM
"""

class SOM(object):
    """Class to represent a (trained) SOM object."""

    def __init__(self, data_som_, number_of_channels_, som_width_, som_height_,
            som_depth_, layout_,
            output_directory_, trained_subdirectory_, som_label_, rotated_size_, run_id_):
        self.run_id = run_id_
        self.data_som = data_som_
        self.number_of_channels = number_of_channels_
        self.som_width = som_width_
        self.som_height = som_height_
        self.som_depth = som_depth_
        self.layout = layout_
        self.output_directory = output_directory_
        self.trained_subdirectory = trained_subdirectory_
        self.som_label = som_label_ 
        self.rotated_size = int(round(rotated_size_))
        self.fullsize = int(np.ceil(self.rotated_size*np.sqrt(2)))
        # Training parameters 
        if self.som_width != None or self.som_height != None:
            self.gauss_start = max(self.som_width, self.som_height)/2
        self.learning_constraint = 0.05
        self.epochs_per_epoch = 1
        self.gauss_decrease = 0.95
        self.gauss_end = 0.3
        self.pbc = "True"
        self.learning_constraint_decrease = 0.95
        self.random_seed = 42
        self.init = "random_with_preferred_direction"
        self.pix_angular_res = 1.5
        self.rotated_size_arcsec = self.rotated_size*self.pix_angular_res
        self.fullsize_arcsec = self.fullsize*self.pix_angular_res
        self.training_dataset_name = ''
        self.save()

    def print(self):
        print(f"\nSOM ID{self.run_id} info")
        print(f"Input data dimensions: ({self.fullsize}x{self.fullsize}x{self.number_of_channels})")
        print(f"Neuron/prototype dimensions: ({self.rotated_size}x{self.rotated_size}x{self.number_of_channels})")
        print(f"SOM dimensions: ({self.som_width}x{self.som_height}x{self.som_depth}), layout {self.layout}")
        print(f"Train parameters:")
        print(f"Periodic boundary conditions: {self.pbc}") 
        print(f"learn. constr. {self.learning_constraint}, decrease {self.learning_constraint_decrease}")
        print(f"Neighbourhood function: start {self.gauss_start}, decrease {self.gauss_decrease}," \
                f" stop {self.gauss_end}\n")

    def save(self):
        """save class pkl"""
        with open(os.path.join(self.output_directory, f'SOM_object_id{self.run_id}.pkl'), 'wb') as output:
            pickle.dump(self, output, pickle.HIGHEST_PROTOCOL)


def load_SOM(output_directory, run_id):
    """Load SOM object"""
    with open(os.path.join(output_directory, f'SOM_object_id{run_id}.pkl'), 'rb') as input:
        return pickle.load(input)


def plot_som_bmu_heatmap(som, closest_prototype_id, cbar=False, save=False,
        save_dir=None, save_name='heatmap', fontsize=20):
    """
    Plot heatmap of trained SOM.
    With closest_prototype_id equal to
    data_map, _,_,_,_ = load_som_mapping(mapping_path,som)
    closest_prototype_id = np.argmin(data_map, axis=1)
    """
    # Count how many cut-outs most resemble each prototype
    closest_to_prototype_count = np.bincount(closest_prototype_id)
    # plot and save the heatmap
    fig = plt.figure(figsize=(14,14))
    ax = plt.Axes(fig, [0., 0., 1., 1.])
    ax.set_axis_off()
    fig.add_axes(ax)

    if som.layout == 'hexagonal':
        heatmap_map, heatmap_mask = populate_hex_map(closest_to_prototype_count,
                som.som_width, som.som_height)
        # Note the transposition of closest_to_prototype_count 
        ax = sns.heatmap(heatmap_map.T, annot=True, mask=heatmap_mask.T, fmt="d",
                cbar=cbar, cmap='inferno', annot_kws={"size": fontsize})
        #, cbar_kws={'label':'# of sources assigned to prototype'})#, linewidths=.1)
    else:
        closest_to_prototype_count = closest_to_prototype_count.reshape([som.som_width,
            som.som_height])
        ax = sns.heatmap(closest_to_prototype_count.T, annot=True, fmt="d",
                cbar=cbar, cmap='inferno', square=True, annot_kws={"size": fontsize}, 
                cbar_kws={'shrink':0.8})#, linewidths=.1)
                #cbar_kws={'label':'# of sources assigned to prototype'})#, linewidths=.1)
    if save:
        plt.savefig(os.path.join(save_dir, save_name +'.png'))
    else:
        plt.show()
    plt.close()

def unpack_trained_som(trained_path, layout):
    """Unpacks a trained SOM, returns the SOM with hexagonal layout 
    in a flattened format. Requires the file path to the trained SOM and
    its layout (either quadratic or hexagonal)"""
    with open(trained_path, 'rb') as inputStream:
        failures = 0
        #File structure: (som_width, som_height, som_depth, number_of_channels,
        #neuron_width, neuron_height) float
        number_of_channels = struct.unpack("i", inputStream.read(4))[0]
        som_width = struct.unpack("i", inputStream.read(4))[0]
        som_height = struct.unpack("i", inputStream.read(4))[0]
        som_depth = struct.unpack("i", inputStream.read(4))[0]
        neuron_width = struct.unpack("i", inputStream.read(4))[0]
        neuron_height = struct.unpack("i", inputStream.read(4))[0]

        if layout == 'quadratic':
            data_som = np.ones((som_width, som_height, som_depth,
                number_of_channels*neuron_width*neuron_height))
            for i in range(som_width):
                for ii in range(som_height):
                    for iii in range(som_depth):
                        for iv in range(number_of_channels*neuron_width*
                                neuron_height):
                            try:
                                data_som[i,ii,iii,iv] = struct.unpack_from(
                                        "f", inputStream.read(4))[0]          
                            except:
                                failures += 1.0
        else:
            som_size, _ = get_hex_size(som_width)
            data_som = np.ones((som_size, number_of_channels*neuron_width*
                neuron_height))
            for i in range(som_size):
                        for ii in range(number_of_channels*neuron_width*
                                neuron_height):
                            try:
                                data_som[i,ii] = struct.unpack_from("f",
                                        inputStream.read(4))[0]          
                            except:
                                failures += 1.0
        if failures > 0:
            print('Failures:', int(failures/(number_of_channels*neuron_width*
            neuron_height)))
        return (data_som, number_of_channels, som_width, som_height, som_depth,
            neuron_width, neuron_height)

def plot_cutout(cutout_id, bin_path, save_path, title, rotated_size, figsize=3, save=True,
        plot_border=True, plot_cutout_index=False, apply_clipping=False,
        clip_threshold=3):
    """Plot cutout to screen or save in save_dir"""
    
    fig = plt.figure(figsize=(figsize,figsize))
    ax = plt.Axes(fig, [0., 0., 1., 1.])
    ax.set_axis_off()
    fig.add_axes(ax)

    image = return_cutout(bin_path, cutout_id)
    if apply_clipping:
        image_clip = np.clip(image, clip_threshold*np.std(image), 1e10)
        image = np.hstack((image, image_clip))

    ax.imshow(image, aspect='equal', interpolation="nearest", origin='lower' )

    if plot_border:
        s = rotated_size
        w = int(np.ceil(s*np.sqrt(2))) 
        # plot border around the 128x128 center of the image
        ax.plot( [(w-s)/2,w-(w-s)/2], [(w-s)/2,(w-s)/2],"r")
        ax.plot( [(w-s)/2,(w-s)/2], [(w-s)/2,w-(w-s)/2],"r")
        ax.plot( [w-(w-s)/2,w-(w-s)/2], [(w-s)/2,w-(w-s)/2],"r")
        ax.plot( [(w-s)/2,w-(w-s)/2], [w-(w-s)/2,w-(w-s)/2],"r")

    if plot_cutout_index:
        s = rotated_size
        w = int(np.ceil(s*np.sqrt(2))) 
        # plot index in lower left corner
        ax.text(w-2,2,str(cutout_id),color='white', horizontalalignment='right')
    if save:
        plt.savefig(os.path.join(save_path, title + '.png'))
    else:
        plt.show()
    plt.close()


def load_som_mapping(mapping_path, som, verbose=True):
    '''Load distances of cut-outs to a trained SOM, also returning
    the number of cut-outs mapped to the SOM, and the width, height and depth
    of this SOM. Requires the file path to the mapping binary file and the
    layout of the trained SOM (either quadratic or hexagonal)'''
    # Create list of indexes to retrieve coordinates of the cut-outs
    cut_out_index = []

    # Unpack SOM mapping
    if not os.path.exists(mapping_path):
        print('This file does not exist:', mapping_path)
    with open(mapping_path, 'rb') as inputStream:
        numberOfImages = struct.unpack("i", inputStream.read(4))[0]
        som_width = struct.unpack("i", inputStream.read(4))[0]
        som_height = struct.unpack("i", inputStream.read(4))[0]
        som_depth = struct.unpack("i", inputStream.read(4))[0]
        failed = 0    
        assert som.som_width == som_width
        assert som.som_height == som_height
        assert som.som_depth == som_depth

        if som.layout == 'hexagonal':
            map_size, _ = get_hex_size(som_width)
        else:
            map_size = som_width * som_height * som_depth
            
        data_map = np.ones((numberOfImages, map_size))
        for i in range(numberOfImages):
            for t in range(map_size):
                try:
                    data_map[i,t] = struct.unpack_from("f", inputStream.read(4))[0]
                    if t == 0:
                        cut_out_index.append(i) # add index
                except:
                    failed += 1
        data_map = data_map[:len(cut_out_index)]
        if failed > 0:
            print('Failed:', int(1.0*failed/(map_size)))
    if verbose:
        print('''Loaded distances of {} cut-outs to a SOM with a width, height 
            and depth equal to {},{},{}.'''.format(numberOfImages,
            som_width, som_height, som_depth))
    return data_map, numberOfImages, som_width, som_height, som_depth


def plot_som(som,gap=2, save=False, save_dir=None, save_name="", normalize=False, 
        cmap='viridis', colorbar=False, highlight=[], highlight_colors=[], legend=False,
        legend_list=[], border_color='white', plot_norm_AQE=False, AQE_per_node=None,
        AQE_std_per_node=None ):
    """Simple way to show or save trained quadratic som"""
    
    print(np.shape(som.data_som))
    # the current function can only deal with 1 channel quadratic SOMs
    assert som.number_of_channels == 1
    assert som.layout == 'quadratic'
    if (save_name == "") and (not legend) and (highlight == []):
        print('Changing save output name to \'website\'')
        save_name="website"
    
    # size of single SOM-node
    img_xsize, img_ysize = som.rotated_size, som.rotated_size
    # x/y grid of wells per plate
    nx, ny = int(som.som_width), int(som.som_height) 
    stitched_x, stitched_y = (gap+img_xsize)*nx+int(gap/1), (gap+img_ysize)*ny+int(gap/1)
    mminterval = vis.MinMaxInterval()

    img_stitched = np.zeros((stitched_y, stitched_x))

    def add_img(nxi, nyi, img):
        assert img.shape == (img_ysize, img_xsize)
        xi = nxi*(img_xsize + gap)+gap
        yi = nyi*(img_ysize + gap)+gap
        img_stitched[yi:yi+img_ysize,xi:xi+img_xsize] = img

    fig = plt.figure(figsize=(14,14))
    ax = plt.Axes(fig, [0., 0., 1., 1.], label='_nolegend_')
    ax.set_axis_off()
    fig.add_axes(ax)
    
    for nxi in range(nx):
        ax.plot(((img_xsize+gap)*nxi, (img_xsize+gap)*nxi),(0,(img_ysize+gap)*ny),
                linewidth=gap*2, color=border_color)
        for nyi in range(ny):
            img2 = som.data_som[nxi,nyi,0]
            img = img2.reshape([img_ysize, img_xsize])
            if normalize:
                img = mminterval(img)
            add_img(nxi, nyi, img)
            # Redo the border with chosen color
            ax.plot((0,(img_xsize+gap)*nx),((img_ysize+gap)*nyi, (img_ysize+gap)*nyi),
                linewidth=gap*2, color=border_color)
            # Plot values of AQE
            if plot_norm_AQE:
                ax.text(10+nxi*img_xsize+gap, 10+nyi*img_ysize+gap,
                        f"{AQE_per_node[nxi+nyi]:.5f}pm{AQE_std_per_node[nxi+nyi]:.5f}", color='w') 
    ax.plot(((img_xsize+gap)*nx, (img_xsize+gap)*nx),(0,(img_ysize+gap)*ny),
                linewidth=gap*2, color=border_color)
    ax.plot((0,(img_xsize+gap)*nx),((img_ysize+gap)*ny, (img_ysize+gap)*ny),
                linewidth=gap*2, color=border_color)

    im = ax.imshow(img_stitched, aspect='equal', 
            interpolation='nearest', cmap=cmap)#'_nolegend_')
   
    # Highlight is a list of sublists, every sublist can contain a number of prototypes
    # all prototypes within a sublist are grouped. This way you can manually 
    # color-code your SOM 
    if not highlight == []:
        print('Entering highlight mode')
        appearance_list = []
        if not legend:
            legend_list = np.ones(len(highlight))
        else:
            assert not legend_list == []
        for group_index, (group, col, legend_label) in enumerate(zip(highlight, highlight_colors, legend_list)):
            legend_flag = True
            for h in group:
                ss = 'solid'
                if legend_flag:
                    ax.add_patch(Rectangle((h[0] * (som.rotated_size + gap)+gap/2, h[1] *
                    (som.rotated_size+gap)+gap/2), som.rotated_size,
            som.rotated_size,alpha=1, facecolor='None', edgecolor=col, linewidth=gap*2,
            label=legend_label, zorder=100))#group_index+1))
                    if h in appearance_list:
                        print('To enable dashes move', h, 'to later part in the sequence (or else the \
                            legend will be ugly)')
                    legend_flag = False
                else:
                    if appearance_list.count(h) == 1:
                        # voor 2
                        #print('2-categories appearance', h)
                        ss = (0,(6,6))
                    elif appearance_list.count(h) == 2:
                        # voor 3
                        #print('3-categories appearance', h)
                        ss = (3,(3,6))
                    ax.add_patch(Rectangle((h[0] * (som.rotated_size + gap)+gap/2, h[1] *
                    (som.rotated_size+gap)+gap/2), som.rotated_size,
            som.rotated_size,alpha=1, facecolor='None', edgecolor=col, linewidth=gap*2,
                    label='_nolegend_',linestyle=ss, zorder=100))
                appearance_list.append(h)
        if legend:
            ax.legend(bbox_to_anchor=(1.04, 0.5), loc='center left', ncol=1,
            #ax.legend(bbox_to_anchor=(0.5, -0.3), loc='lower center', ncol=1,
                    prop={'size':24})
    if colorbar:
        fig.colorbar(im, orientation='vertical')
    if save:
        if legend:
            # Tight layout is needed to include the legend in the saved figure 
            print(os.path.join(save_dir, save_name+ '.png'))
            plt.savefig(os.path.join(save_dir, save_name+ '.png'), dpi='figure', bbox_inches='tight')
        else:
            print(os.path.join(save_dir, save_name+ '.png'))
            plt.savefig(os.path.join(save_dir, save_name+ '.png'), dpi='figure', bbox_inches='tight')
            plt.show()
    else:
        plt.show()
    plt.close()



def return_cutout(bin_path, cutout_id):
    """Open bin_path, return cut-out with id=cutout_id"""
    with open(bin_path, 'rb') as file:
        number_of_images, number_of_channels, width, height = struct.unpack('i' * 4, file.read(4 * 4))
        if cutout_id > number_of_images:
            raise Exception('Requested image ID is larger than the number of images.')
        size = width * height
        file.seek((cutout_id*number_of_channels + 0) * size*4, 1)
        array = np.array(struct.unpack('f' * size, file.read(size*4)))
        cutout = np.ndarray([width,height], 'float', array)
    return cutout

def plot_and_save_outliers(som, outliers_path, bin_path,
        number_of_outliers_to_show, pd_catalogue, closest_prototype_id,
        distance_to_bmu_sorted_down_id, clip_threshold=False, plot_border=True,
        debug=False, apply_clipping=False, overwrite=False, save=True, save_full=False,plot_subset=None,
        subset_pickle_path=None, enable_degree_label=False, enable_hmsdms_label=False):
    """Save first number_of_outliers_to_show outliers to outliers_path in 
    separate directories per prototype.
    Also save the outlier coordinates and catalogue entries."""
    # Width of cropped image used in the training
    s = som.rotated_size
    w = som.fullsize # Size needed for cut-out to be able to rotate it

    if plot_subset == None:
        imlist = distance_to_bmu_sorted_down_id[:number_of_outliers_to_show]
    else:
        assert not subset_pickle_path == None
        imlist = distance_to_bmu_sorted_down_id[:number_of_outliers_to_show][plot_subset]
        pd_catalogue_subset = pd_catalogue.iloc[imlist]
        pd_catalogue_subset.to_pickle(subset_pickle_path)
    for i, id in enumerate(imlist):
        if debug:
            if plot_subset == None:
                print(i)
            else:
                print(i, 'outlier number:',plot_subset[i])
            print(pd_catalogue['RA'].iloc[id],pd_catalogue['DEC'].iloc[id] )
            #print(id, pd_catalogue.iloc[id])
        x = int(closest_prototype_id[id]%som.som_width)
        y = int(closest_prototype_id[id]/som.som_height)
        # Make image dir for outliers
        outliers_subpath = os.path.join(outliers_path, str(x)+'_' + str(y))
        if overwrite or not os.path.exists(outliers_subpath):
            if (save or save_full) and not os.path.exists(outliers_subpath):
                os.makedirs(outliers_subpath)
            # plot outlier and save to its outliers subfolder
            fig = plt.figure()
            fig.set_size_inches(3, 3)
            ax = plt.Axes(fig, [0., 0., 1., 1.])
            ax.set_axis_off()
            fig.add_axes(ax)
            image = return_cutout(bin_path, id)
            if apply_clipping:
                image_clip = np.clip(image, clip_threshold*np.std(image), 1e10)
                image = np.hstack((image, image_clip))
            ax.imshow(image, aspect='equal', interpolation="nearest", origin='lower' )
            if enable_hmsdms_label:
                hmsdms_label = SkyCoord(ra=pd_catalogue['RA'].iloc[id],dec=pd_catalogue['DEC'].iloc[id], 
                        unit=u.degree).to_string('hmsdms') 
                ax.text(w-2,2, hmsdms_label, color='white', horizontalalignment='right')
            elif enable_degree_label:
                ax.text(w-2,2,'({ra:.{digits}f}, {dec:.{digits}f})'.format(ra=pd_catalogue['RA'].iloc[id], 
                        dec=pd_catalogue['DEC'].iloc[id], digits=3),color='white', horizontalalignment='right')
            # plot border around the cropped center of the image
            if plot_border:
                ax.plot( [(w-s)/2,w-(w-s)/2], [(w-s)/2,(w-s)/2],"r")
                ax.plot( [(w-s)/2,(w-s)/2], [(w-s)/2,w-(w-s)/2],"r")
                ax.plot( [w-(w-s)/2,w-(w-s)/2], [(w-s)/2,w-(w-s)/2],"r")
                ax.plot( [(w-s)/2,w-(w-s)/2], [w-(w-s)/2,w-(w-s)/2],"r")
            if debug:
                print('Prototype coordinates: ' + str(x) + "," + str(y))
            if save:
                plt.savefig(os.path.join(outliers_subpath, str(i) +'.png'))
            elif save_full:
                plt.savefig(os.path.join(outliers_path, str(i) +'.png'))
            else:
                plt.show()
            plt.close()

    # Write source locations to website text file
    ra = pd_catalogue['RA'].iloc[distance_to_bmu_sorted_down_id[:number_of_outliers_to_show]]
    dec = pd_catalogue['DEC'].iloc[distance_to_bmu_sorted_down_id[:number_of_outliers_to_show]]
    with open(os.path.join(outliers_path, 'loc.txt'), 'w') as loc_f:
        for a,b in zip(ra,dec):
            loc_f.write("{};{}\n".format(a,b))
            
    # Write first 100 outliers to csv
    rows = pd_catalogue.iloc[distance_to_bmu_sorted_down_id[:number_of_outliers_to_show]]
    # Add distances to bmu as separate column
    rows['Distances'] = [min(heatmap) 
            for heatmap in np.array(rows['Heatmap'], dtype=pd.Series)]
    rows.to_csv(os.path.join(outliers_path, 'outliers_catalogue.csv'), index=True)
    return rows


def plot_distance_to_bmu_histogram(distance_to_bmu, number_of_outliers_to_show, outliers_path,
        save=False, xmax=200, run_id='', visualize_sizes=False):
    # Get the maj-size that contains 90% of the sources
    sorted_extremes = np.sort(distance_to_bmu)

    # Linear
    fig = plt.figure()
    fig.set_size_inches(9, 4.5)
    ax = fig.add_subplot(111)
    bins = ax.hist(distance_to_bmu, bins=100, histtype='step', linewidth=3)#'auto')#400)
    height = max(bins[0])
    sections = [0.9, 0.99, 0.999]
    cutoff = [sorted_extremes[int(len(distance_to_bmu)*x)] for x in sections]

    # Plot red line for outliers shown 
    red_x = sorted_extremes[-number_of_outliers_to_show]
    red_y = height*0.7
    plt.text(red_x+5, red_y/4, str(number_of_outliers_to_show)+" most outlying objects",
            color='r', fontsize=12)
    #plt.text(0.5,0.6, str(number_of_outliers_to_show)+" biggest outliers shown below",
    #        color='r', transform=ax.transAxes)
    plt.vlines(red_x, ymax=2*height, ymin=0, color='r', linestyle='dashed')
    plt.arrow(red_x, red_y/2, 10, 0, shape='full', length_includes_head=True,
            head_width=height*0.05, head_length = 5, fc='r', ec='r')


    # Visualize the size-distribution
    if visualize_sizes:
        hh = [height*0.1, height*0.1, height*0.1]
        for c, s, h in zip(cutoff, sections, hh):
            plt.vlines(c, ymax=h*0.95, ymin=0)
            print('Cut-off that includes {0}% of the sources: {1} (= {2} x median)'.format(s, round(c,1), round(c/np.median(distance_to_bmu),1)))
            plt.text(c, h, str(s*100)+'% of sources')
            plt.arrow(c, h*0.5, -10, 0, shape='full', length_includes_head=True, head_width=height*0.01,
                    head_length = 5, fc='k', ec='k')

    if xmax =='':
        xmax = max(bins[1])
    info_x = xmax*0.7
    plt.text(info_x, red_y/15, 
'''SE distances
Median: {}
Mean: {}
Std. dev.: {} 
Max.: {}(={}xmedian)'''.format(str(round(np.median(distance_to_bmu),1)),
        str(round(np.mean(distance_to_bmu),1)), str(round(np.std(distance_to_bmu),1)),
        str(round(max(distance_to_bmu),1)), 
        str(int(max(distance_to_bmu)/np.median(distance_to_bmu)))), fontsize=12)
    #plt.ylim(0,height*1.05)
    plt.xlim(0,xmax)
    plt.yscale('log')
    plt.ylim(0.6,height)
    #plt.title('Histogram of distance to closest prototype')
    plt.xlabel('Summed Euclidian (SE) distance to best matching prototype');
    plt.ylabel('Number of radio-sources per bin');
    plt.tight_layout()
    if save:
        if run_id == '':
            plt.savefig(outliers_path + '/outliers_histogram.png', transparent=True)
        else:
            plt.savefig(outliers_path + '/outliers_histogram_ID{}.png'.format(run_id), transparent=True)
        plt.close()
    else:
        plt.show()


def save_all_prototypes_and_cutouts(som, bin_path, website_path, 
        max_number_of_images_to_show, catalogue, web_catalogue, data_map,
        figsize=5, save=True, plot_border=True,
        plot_cutout_index=False, apply_clipping=False, clip_threshold=3, overwrite=False):
    if som.layout == 'hexagonal':
        print('Hexagonal layout is not implemented yet.')
        return
    for x in range(som.som_width):
        for y in range(som.som_height):
            print(x,y)
            for z in range(som.som_depth):
                # Make image dir for prototype and website paths
                proto_path = os.path.join(website_path, 'prototype' \
                                          + str(x)+'_'+str(y)+'_'+str(z))
                image_paths = [os.path.exists(os.path.join(proto_path, str(i) + 
                    '.png')) for i in 
                    range(max_number_of_images_to_show)]                
                text_path_exists = os.path.exists(os.path.join(proto_path, 
                    'loc.txt'))
                if not os.path.exists(proto_path):
                    os.makedirs(proto_path)

                if not os.path.exists(os.path.join(proto_path, 'prototype.png')) or overwrite:
                    # Plot the choosen prototype
                    proto = som.data_som[x,y,z,:]
                    proto = proto.reshape((som.rotated_size, som.rotated_size))
                    fig = plt.figure()
                    fig.set_size_inches(6, 6)
                    ax = plt.Axes(fig, [0., 0., 1., 1.])
                    ax.set_axis_off()
                    fig.add_axes(ax)
                    ax.imshow(proto, aspect='equal', interpolation="nearest",
                            origin='lower')
                    plt.savefig(proto_path + '/prototype.png')
                    plt.close()

                if not text_path_exists or not all(image_paths) or overwrite:
                    # Sort the euclidian distances to this prototype
                    closest = np.argsort(data_map, axis=1)[:,0]
                    #farthest = np.argsort(data_map, axis=1)[:,-1]
                    #si = (y*som.som_width)+x
                    si = (x*som.som_height)+y

                    closest_index = [i for i, som_index in enumerate(closest) if
                            som_index == si]
                    closest_distances = np.min(data_map, axis=1)[closest_index]
                    sort_index = np.argsort(closest_distances)
                    closest_index = np.array(closest_index)[sort_index]
                    closest_distances = np.array(closest_distances)[sort_index]

                if not all(image_paths) or overwrite:
                    # Plot cut-outs (closest to choosen prototype)
                    for i, cutout_id in enumerate(
                            closest_index[:max_number_of_images_to_show]):
                        plot_cutout(cutout_id, bin_path, proto_path, str(i),
                            som.rotated_size, figsize=figsize, save=save,
                            plot_border=plot_border, 
                            plot_cutout_index=plot_cutout_index,
                            apply_clipping=apply_clipping,
                            clip_threshold=clip_threshold)

                # Write locations to text files for website
                if not text_path_exists or overwrite:
                    ra = catalogue['RA'].iloc[closest_index[:max_number_of_images_to_show]]
                    dec = catalogue['DEC'].iloc[closest_index[:max_number_of_images_to_show]]
                    with open(os.path.join(proto_path, 'loc.txt'), 'w') as loc_f:
                        for a,b in zip(ra,dec):
                            loc_f.write('{};{}\n'.format(a,b))

                # Write catalogue lines to csv files
                proto_path = os.path.join(website_path, 'prototype' \
                                          + str(x)+'_'+str(y)+'_'+str(z))
                if not os.path.exists(proto_path):
                    os.makedirs(proto_path)
                csv_name = som.trained_subdirectory + '_prototype_' + str(x) + '_' + \
                    str(y) + '.csv'
                csv_path_exists = os.path.exists(os.path.join(proto_path,
                    csv_name))
                if not csv_path_exists or overwrite:
                    print(proto_path)
                    condition = (web_catalogue['Closest_prototype_x']
                            == x) & \
                    (web_catalogue['Closest_prototype_y'] == y)
                    pd_to_csv = web_catalogue[condition] 
                    pd_to_csv.to_csv(os.path.join(proto_path, csv_name),
                            index=False)
    print('Done.')




