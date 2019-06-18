#!/usr/bin/env python
# coding: utf-8

# # Generate website content



import pinklib.website as web
import os
from astropy.stats import median_absolute_deviation
import numpy as np
import importlib
import pandas as pd



# INPUT PARAMETERS
# Name of the binary file
cutouts_bin_name = 'cutouts_preprocessed'

# SOM parameters
layout = 'quadratic'
som_label = 'cyclic SOM'

# Outliers parameters
# Can be any number smaller than the total amount of cut-outs/sources in your binary file
number_of_outliers_to_show = 100

# Cutouts to website parameters
max_number_of_images_to_show = 10

# Paths and directories
SOM_directory = 'SOM_directory'
catalogue_path = os.path.join('SOM_directory', 'catalogue_LOTSS_DR1_final.pkl')
data_path = os.path.join('SOM_directory', 'LOTSS_DR1_final.bin')
trained_path = os.path.join('SOM_directory',
                            'resultLOTSS_DR1_final_10x10x67_0.443146905982625_12.533141373155_ID14.bin')
mapping_path = os.path.join('SOM_directory',
                    'mapping_resultLOTSS_DR1_final_10x10x67_0.443146905982625_12.533141373155_ID14.bin')

website_path = 'website'
if not os.path.exists(website_path):
    os.mkdir(website_path)
outliers_path = os.path.join(website_path, 'outliers')
if not os.path.exists(website_path):
    os.makedirs(website_path)



# Create SOM object (a data structure that holds the properties of a SOM)
data_som, number_of_channels, som_width, som_height, som_depth,      neuron_width,  neuron_height = web.unpack_trained_som(trained_path, layout)
my_som = web.SOM(data_som, number_of_channels, som_width, som_height, som_depth, layout, 
               SOM_directory, '', som_label, neuron_width,99)
    
    
# Plot SOM
web.plot_som(my_som,gap=2, save=True, save_dir=website_path, normalize=True, cmap='viridis')


# Spread of the sources over the SOM
# Create list of indexes to retrieve coordinates of the cut-outs
data_map, _, _, _, _ = web.load_som_mapping(mapping_path, my_som, verbose=True)
distance_to_bmu = np.min(data_map, axis=1)
distance_to_bmu_sorted_down_id = np.argsort(distance_to_bmu)[::-1]
closest_prototype_id = np.argmin(data_map, axis=1)
prototype_x = np.array([int(i%my_som.som_width) for i in closest_prototype_id])
prototype_y = np.array([int(i/my_som.som_width) for i in closest_prototype_id])
closest_to_prototype_count = np.bincount(closest_prototype_id)
print('Mean bincount:', np.mean(closest_to_prototype_count))
print('Standard deviation bincount:', np.std(closest_to_prototype_count))
print('median bincount:', np.median(closest_to_prototype_count))
print('Standard deviation bincount:', median_absolute_deviation(closest_to_prototype_count))


# Plot best matching unit heatmap
web.plot_som_bmu_heatmap(my_som, closest_prototype_id, cbar=False, save=True, save_dir=website_path)


# Generate website content
catalogue = pd.read_pickle(catalogue_path)
outliers = web.plot_and_save_outliers(my_som, outliers_path, data_path, 
        number_of_outliers_to_show, catalogue, closest_prototype_id,
        distance_to_bmu_sorted_down_id, clip_threshold=False, plot_border=False,
        debug=False, apply_clipping=False, overwrite=True, save=True)

# Plot outliers histogram
web.plot_distance_to_bmu_histogram(distance_to_bmu, number_of_outliers_to_show, 
                                    outliers_path, xmax=150, save=True)


# Save all max_number_of_images_to_show
web.save_all_prototypes_and_cutouts(my_som, data_path, website_path, max_number_of_images_to_show, 
        catalogue, catalogue, data_map,
        figsize=5, save=True, plot_border=False, plot_cutout_index=False, apply_clipping=False, 
                                     clip_threshold=3, overwrite=True)

