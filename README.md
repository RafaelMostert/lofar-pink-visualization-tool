# PINK Visualization Tool by Rafael Mostert 2017-2018

### Prerequisites
Train your own Self-organizing map using https://github.com/HITS-AIN/PINK or your own code.
Requires the following github to create the content for the website:
https://github.com/RafaelMostert/LOFAR-PINK-library



### Contents

- chalkItUp.ttf : a chalk font
- A bootstrap folder and jumbotron.css : css files mainly used for the top navbar
- an aladin lite folder : tweaked Aladin lite, mostly removing some unwanted  
    clutter and surveys from the original code
- an images folder : contains a few general images used by the website such as 
    the arrows
- website : an empty folder where your generated SOM-specific content 
    should end up
- som.php : index page
- outliers.php : page for outliers
- about.php : page for acknowledgements

### Local run instructions
1. cd to the git directory of this project 
    (the directory where this README is located)
2. run 'php -S localhost:8000'
3. visit 'http://localhost:8000/website_files/som.php'

### Installation instructions

1. Train a SOM using PINK.
2. Generate website content using the generate website content jupyter notebook
    from https://github.com/RafaelMostert/LOFAR-PINK-library
    [TODO: clean up and make this git available]
3. Change som.php the 'columns' and the 'rows' PHP variables to match the
    width and height of your SOM respectively.
4. Insert the 'website_files' directory in your server root directory.
5. Done.


### TODO

- Remove step 3 in installation procedure by generating the php files in step 2 of the 
installation.
- Bundle own css in one file
