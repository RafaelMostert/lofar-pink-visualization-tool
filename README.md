# PINK Visualization Tool by Rafael Mostert

Interactive web visualization for the paper [Mostert et al. 2020](https://www.aanda.org/articles/aa/abs/2021/01/aa38500-20/aa38500-20.html): "Unveiling the rarest morphologies of the LOFAR Two-metre Sky Survey radio source population with self-organised maps"

See https://rafaelmostert.com/lofar/som.php for a live demonstration.

Note that the visualization tool is designed for desktop use only.

### Prerequisites
Train your own Self-organizing map using https://github.com/HITS-AIN/PINK or your own code.


### Contents of website_files directory
- chalkItUp.ttf : a chalk font
- default.css : css file for som.php and outliers.php
- an images folder : contains a few general images used by the website such as 
    the arrows
- website : a folder with pngs that can be replaced by your own SOM-specific generated content
- som.php : index page
- outliers.php : page for outliers
- about.php : page for acknowledgements


### Demo instructions (in case you have access to a webserver)

1. git clone this project
2. Upload the project to your server.
3. Visit your server.


### Local run instructions (e.a. if you do not have access to a webserver)
1. cd to the git directory of this project 
    (the directory where this README is located)
2. run 'php -S localhost:8000'
3. visit 'http://localhost:8000/website_files/som.php'


### Adapt installation to your own Self-Organised Map
1. Train a SOM using PINK.
2. Generate website content using either the 'Generate website content' python script or the jupyter notebook.
3. Replace the generated content in the directory 'website_files/website'.
4. Upload the 'website' directory to your server.
5. Done.


