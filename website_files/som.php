<!doctype html>
<html>
<!-- Author: Rafael Mostert 2017 -->
<!-- Mail: rafaelmostert @ gmail.com -->
<!-- Mail: mostert @ strw.leidenuniv.nl -->


<!-- jQuery .hover to show prototypes and .click used to retrieve corresponding cutouts -->

<style type="text/css">

@font-face {
  font-family: 'icomoon';
  src: url("../fonts/icomoon.eot?-xb0za8");
  src: url("../fonts/icomoon.eot?#iefix-xb0za8") format("embedded-opentype"), url("../fonts/icomoon.woff?-xb0za8") format("woff"), url("../fonts/icomoon.ttf?-xb0za8") format("truetype"), url("../fonts/icomoon.svg?-xb0za8#icomoon") format("svg");
  font-weight: normal;
  font-style: normal; }

#content, html{
    height: 98%;
}

@font-face {
    font-family:chalk;
    src: url('chalkItUp.ttf');
}


body{background-color:#EEEEEE;margin:20px;color:#004b93;
max-width:1800px;line-height:1.6;font-size:18px;color:#444;padding:0
10px;
font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
}
h1,h2,h3{line-height:1.2}

.imgContainer{
    display:inline-block;
}

.containerdiv {
    border: 0;
    float: left;
    position: relative;
}
.fill {
    margin-top:10px;
    height:30px;/*border-left:40px;border-right:40px;*/
    width:calc(100% - 80px);
    overflow: hidden;
    background-size: 100% 100%;
    background-position: center;
    background-image: url('images/bracket2.png');
}

.chalky_font1{
    font-family:chalk;
    font-size:22pt;
}
.chalky_font2{
    font-family:chalk;
    font-size:22pt;
    display:none;
    float:left;
    padding:10px;
    /*width:350px;*/
    width:calc(100% - 1200px);
    clear:right;
}
.chalky_font3{
    font-family:chalk;
    font-size:14pt;
    color:white;
    display:none;
    position:absolute;
    z-index:19;
}

.heatmap {
    border: 0;
    position: absolute;
    top: 0;
    left: 0;
}

#proto_id, #prototype_clicked_on{
    margin: 20px;
}

#buttons {
    float: left;
    clear: both;
    position:relative;
}

#leftpanel {
    float: left;
    width: 400px;
    height:440px;
    position: relative;
}

#cutouts_container {
    margin: 0px;
    float: left;
    position: relative;
    width: calc(100% - 460px);/*
    clear: right;*/
    box-sizing: border-box;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
}

#som {
width:400px;
position: relative;
}

#containertje{
position: relative;
float: left;
}

#aladin-lite-div{
    width:800px;
    height:400px;
}
</style>

<head>
    <title>SOM Visualization</title>
	<meta name="robots" content="noindex,nofollow,noarchive,nosnippet,noodp">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
    <!-- Include ASTRON favicon -->
    <link rel="icon" href="images/favicon.ico">
    <!-- Popover CSS 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" rel="stylesheet">
    --> 
    <!-- Bootstrap core CSS -->
    <link href="bootstrap-4.0.0-beta.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="jumbotron.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="navbar-top-fixed.css" rel="stylesheet">
    <!-- include Aladin Lite CSS file in the head section of your page -->
    <link rel="stylesheet" href="//aladin.u-strasbg.fr/AladinLite/api/v2/latest/aladin.min.css" />
	<script type="text/javascript">
    var hipsdir = "http://lofar.strw.leidenuniv.nl/hips_lotss_dr1_high";
    var proto_x;
    var proto_y;    
$(document).ready(function() { //Make sure to load javascript after html is loaded
    var div1 = document.getElementById("dom-width");
    var div2 = document.getElementById("dom-height");
    var width = div1.textContent;
    var height = div2.textContent;
	$('area').hover(		
        function() { 
			// Function is activated on hover-in of a SOM sub-area
            $('#hovered_prototype img').attr('src', "website/" + this.id + "/prototype.png");
            $('#hovered_prototype').css('position', 'relative');
            $('#hovered_prototype').css('z-index', 3000);
        },
        function() {
            // Function is activated on hover-out of a SOM sub-area
            $('#hovered_prototype').css('z-index', -3000);
        }
    );
    
	// Function is activated on click of a SOM sub-area
	$('area').click(		
        function() {
    $(last_clicked_cutout).css({"border":"none",
        "-webkit-box-shadow":"none",
        "-moz-box-shadow":"none",
            "box-shadow":"none"
    });
			$('#instructions').hide(400);
			//.style.display = 'block'; 
            $('#prototype_clicked_on img').attr('src', "website/" + this.id + "/prototype.png");
			for (i = 0; i < 10; i++) {
				$('#cutouts' + i + ' img').show();
				$('#cutouts' + i + ' img').attr('src', "website/" + this.id + "/" + i + ".png");
				$('#cutouts' + i + ' img').attr('onerror', "this.style.display='none'"); // Hide img container if file does not exist
            }
            proto_x = this.id.split(/_|e/)[1]
            proto_y = this.id.split(/_|e/)[2]
            changeProtoId(proto_x  , proto_y);
            $('#red_square').animate({"top": height * proto_y + "px", "left": width * proto_x + "px"},200);
            $('#red_square').show();
			$('#prototype_clicked_on').show();
            $('#cutouts_container').show(400);
            $('#cutouts_explainer').delay(400).show(400);
			
        }
    );
});


// Load SOM properties file
function load_file() {
$('#instructions').show();
$('#prototype_clicked_on').hide();
$('#cutouts_container').hide();
$('#cutouts_explainer').hide();
$('#containertje').hide();
$('.chalky_font2').hide();
$('#leftpanel').css({"width": "400px"});
var xhr= new XMLHttpRequest();
xhr.open('GET', 'website/about_som.html', true);
xhr.onreadystatechange= function() {
    if (this.readyState!==4) return;
    if (this.status!==200) return; // or whatever error handling you want
    document.getElementById('som_properties').innerHTML= this.responseText;
    document.getElementById('instruction_title').innerHTML= 'SOM properties and downloads';
};
$('#som_properties').css({"font-size":"14px"});
xhr.send();
}

// Triggered when clicking a single cut-out
// reveals Aladin Lite snippet and centers this
// snippet on the coordinates of the cut-out
var last_clicked_cutout;
var chalk_once = true;
function cutout_click(i){
    $('.fill').hide(100);
    $('.chalky_font1').hide(100);
    $(last_clicked_cutout).css({"border":"none",
        "-webkit-box-shadow":"none",
        "-moz-box-shadow":"none",
            "box-shadow":"none"
    });
    $('#cutouts'+ i).css({"border":"2px solid red",
        "-webkit-box-shadow":"inset 0px 0px 0px 4px red",
        "-moz-box-shadow":"inset 0px 0px 0px 4px red",
            "box-shadow":"inset 0px 0px 0px 4px red"
    });

    $('#leftpanel').css({"width": "100%"});
    $('#cutouts_container').animate({"width": "100%"},400);
    $('#containertje').delay(400).show(400);
    $('.chalky_font2').delay(800).show(400);
    if (chalk_once == true){
        $('.chalky_font3').delay(6000).show(400);
        $('.chalky_font3').delay(16000).hide(400);
        chalk_once = false;};
    last_clicked_cutout = "#cutouts"+i; 
    go_to_aladin(i);
}   

// Set Aladin Lite snippet coordinates
function go_to_aladin(i) {
    // Open loc.txt, parse RA and Dec and goto these coordinates
    // Each prototype has its own directory and each directory contains
    // a file loc.txt that contains the coordinates to the first few best
    // matching sources to this prototype.
    $.get('website/prototype' + proto_x + '_' + proto_y + '_0/loc.txt', function(data) {
        var line = data.split("\n")[i];
        var ra = line.split(';')[0];
        var dec = line.split(';')[1];
        //console.log(ra, dec); // Uncomment to write coordinates to console
        aladin.gotoRaDec(ra,dec);
        aladin.setFov(12/60);
                   }, 'text');  
}

// Updates the prototype coordinate variables in the website text
function changeProtoId(xxx,yyy) {
    document.getElementById("proto_radio_id").innerHTML = "Radio sources from LOFAR survey that resemble the selected prototype (" + xxx + "," + yyy + "):";
}


$(function () {
    $('[data-toggle="popover"]').popover({html:true, content:function() {
                  return $('#owner-popover').html();
                }})
})


// Toggle heatmap visibility
function toggle_heatmap() {
    if (document.getElementById("heatmap_button").innerHTML == 'Show heatmap'){
        $('#heatma').show();
        $('#heatmap_button').text('Hide heatmap');
    } else {
        $('#heatma').hide();
        $('#heatmap_button').text('Show heatmap');
    }
}
</script>

</head>

<body>

<!-- Bootstrap navbar -->
    <!--<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">-->
    <nav class="navbar navbar-expand-md navbar-light fixed-top" style="background-color: #FFFFFF;">
      <a class="navbar-brand" >
    <img src="images/astron-logo.gif" width="105" height="30" class="d-inline-block align-top" alt="">
     <span style="text-align: left;font-size:8.2pt;color:#009cde;line-height:18px;font-family: Verdana,Arial,Segoe,sans-serif;">Netherlands Institute for Radio Astronomy</span>
  </a>  
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="">Home<span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="outliers.php">Morphological outliers</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" onclick="load_file();">Downloads</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="about.php">Acknowledgements</a>
          </li>
        </ul>
      </div>
     <span style="text-align: right;font-size:10.2pt;color:#009cde;line-height:18px;font-family: Verdana,Arial,Segoe,sans-serif;">
SOM Visualization Tool by Rafa&euml;l Mostert
</span>
    </nav>

<div id="content">
<div id="leftpanel" >
    <?php
    $maindirname = "website/";

	// Get number of columns and rows from som filename
	$images = glob($maindirname."*.png");
	preg_match_all('!\d+!', $images[0], $matches);
	$columns = (int) $matches[0][0];
	$rows = (int) $matches[0][1];


    // Div that contains SOM and hovered prototype
    echo '<div id="som" class="containerdiv">';
        // SOM
        $som_width=400;
        $som_height=400;
        $width = $som_width/$columns;
        $height = $som_height/$rows;
        echo '<div id="dom-width" style="display: none;">';
                echo htmlspecialchars($width);
        echo '</div>';
        echo '<div id="dom-height" style="display: none;">';
                echo htmlspecialchars($height);
        echo '</div>';


        echo '<img id="map1" src="'.$images[0].'" usemap="#map1" 
            border="0" width="'.$som_width.'" height="'.$som_height.'" alt="" class="imgContainer" >';
        // Contains heatmap
        echo '<img id="heatma" src="'.$maindirname."heatmap.png".'" usemap="#map1" border="0" width="'.$som_width.'" 
            height="'.$som_height.'" alt="" class="heatmap" style="display:none;">';
        echo '<map name="map1" id="_map1">';
        $title = "test_title";

        for( $x = 0; $x < $columns; $x++ )
        {
           for( $y = 0; $y < $rows; $y++ )
           {
              $href = "?prototype=prototype{$x}_{$y}_0";
              $a = ($x * $width);
              $b = ($y * $height);

              $coords = array( $a, $b, ($a + $width), ($b + $height) );
              echo '<area id="prototype'.$x.'_'.$y.'_0" area shape="rect" coords="'.implode( ',', $coords ).'"  alt="'.$alt.'" title="('.$x.','.$y.')" />';
           }
        }
        echo '</map>';

        // Red square to highlight selected prototype
        echo '<div id="red_square" style="display:none;position:absolute;z-index:500;width:'.$width.'px;height:'.$height.'px;border:2px solid #FF0000;"></div>';
        ?>
    <!-- Button to toggle heatmap -->
    <button type="button" class="btn btn-primary" onclick="toggle_heatmap();" id="heatmap_button">Show heatmap</button>
    <!-- Button to show som properties -->
    <button type="button" class="btn btn-primary" onclick="load_file();" id="heatmap_button">SOM properties</button>

    <!-- Arrow pointing at SOM -->
    <div class="chalky_font1" style="float:right;padding:10px;padding-right:80px">
        <img src="images/arrow-up2.png" width="30px" height="60px">
    </div>  
    <!-- SOM explainer text -->
    <div class="chalky_font1" style="float:right;padding:0px;clear:both;">
        This is a Self-Organizing Map, trained on sources from the LOFAR survey.
        Click on one of these prototypes.
        
    </div>
</div>

    <!-- Embed Aladin snippet -->
    <div id="containertje" style="display:none;">
        <div id="aladin-lite-div" ></div>
        <!--<script type="text/javascript" src="//aladin.u-strasbg.fr/AladinLite/api/v2/latest/aladin.min.js" charset="utf-8"></script>-->
        <script type="text/javascript" src="aladin/AladinLite-2017-08-25/aladin.min.js" charset="utf-8"></script>
        <script type="text/javascript">
            //var aladin = A.aladin('#aladin-lite-div', {survey: "P/DSS2/color", fov:3, target: "168.8126927145544, 53.322134981323224"});
            var hipsDir = "http://lofar.strw.leidenuniv.nl/hips_lotss_dr1_high";
        var aladin = $.aladin("#aladin-lite-div", 
            {showFullscreenControl: false, // Hide fullscreen controls 
            showGotoControl: false, // Hide go-to controls
            showFrame: false}); // Hide coordinates
            aladin.setImageSurvey(aladin.createImageSurvey('LOFARHETDEX', 'LOFAR (radio)',hipsDir, 'equatorial', 9, {imgFormat: 'png'}));
        </script>
        
        <!-- Text raising awareness about the Aladin controls-->
        <div class="chalky_font3" style="line-height:1.2;top:25px;left:40px;">   
                Observe the radio source in 
                different frequencies <br>&#10550; 
        </div>
        
        <div class="chalky_font3" style="top:110px;right:20px;
            width:150px">   
            Feel free to zoom, scroll or pan around <!--&#8594;-->
        </div>
    </div>
    
    <!-- Text explaining Aladin snippet -->
    <div class="chalky_font2" style="">
       On the left you can see where the radio source you clicked on is
        located on the sky. The source might be accompanied or interacting with other sources or be part of some larger structure!
    </div>
    
    <!-- Hovered prototype -->
    <!--
    <div id="hovered_prototype" class="imgContainer" style="display:none;">
    <img src="website/prototype0_0_0/prototype.png" width="400" height="400">
    </div>
    -->

</div>


<center>

<!-- On landing page, display instructions about the site -->
<div class="card" id="instructions" style="width:900px;text-align:left;">
  <div class="card-body">
    <h2 class="card-title"><span style="color:#009cde;" id="instruction_title">About this project</span></h2>
    <p class="card-text" style="font-size:18pt" id="som_properties">
<?php
echo "From the shape or morphology of a radio source we can infer physical properties of the source and its environment.<br><br>
To find out what different morphologies are present in the LOFAR survey, we use a dimensionality reduction technique known as a <i>Self-Organizing Map</i>.<br><br>
This is an unsupervised neural network that projects a 
high-dimensional dataset to a discrete 2-dimensional representation.<br><br>
The map contains $columns x $rows neurons or prototypes, each represents a group of sources.";
?>
  </div>
</div>

<!-- Cutouts container-->
<div id="cutouts_container" class="imgContainer" style="display:none;">
<div id="proto_radio_id">Radio sources from LOFAR survey that resemble the selected prototype</div>
<?php
$dirname = "website/prototype0_0_0/";
$images = glob($dirname."*.png");

//  Display all available images corresponding to the prototype
$cutout_number = 0;
foreach($images as $image) {
	if( $image != $dirname."prototype.png") {
		echo '<div id="cutouts'.$cutout_number++.'" class="imgContainer" style="padding:1px;">';
        echo "<img src=\"$image\" onclick=\"cutout_click($cutout_number - 1);\" width=\"200\" height=\"200\"/><br />";
		echo '</div>';
		}
}
?>

    <!-- Bracket beneath cutouts -->
    <div id="cutouts_explainer" style="display:none;">
        <div class="fill"></div>
        <!-- SOM explainer text -->
        <div class="chalky_font1" style="margin:auto;padding:10px;
            clear:both;">

		<?php           
		echo "Here are $cutout_number of the radio sources that best resemble the prototype 
            you just selected. <br>Click on a source to view it in the sky.";
		?>
        </div>
    </div>
</div>
</div>
</center>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <script>window.jQuery || document.write('<script src="../../../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="bootstrap-4.0.0-beta.2/assets/js/vendor/popper.min.js"></script>
    <script src="bootstrap-4.0.0-beta.2/dist/js/bootstrap.min.js"></script>

</body>

</html>


<!-- Copyright notification related to the use of Bootstrap code snippets:

The MIT License (MIT)

Copyright (c) 2011-2016 Twitter, Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.


-->

