<!doctype html>
<html>
<!-- Author: Rafael yyMostert 2017 -->
<!-- Mail: mostert @ strw.leidenuniv.nl -->

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
    /*width:calc(100% - 440px);
    float:left;
    position:relative;*/
    display:inline-block;
}

.imgContainer2{
    /*float:left;*/
    clear:right;
    width:100%;
}
.imgContainer3{
    width: 100%;
    float:left;
}
.containerdiv {
    border: 0;
    float: left;
    position: relative;
}

.chalky1{
    font-family:chalk;
    font-size:22pt;
}
.chalky2{
    font-family:chalk;
    font-size:22pt;
    display:none;
    float:left;
    padding:10px;
    width:350px;
    clear:right;
}
.chalky3{
    font-family:chalk;
    font-size:14pt;
    color:white;
    display:none;
    position:absolute;
    z-index:19;
    text-shadow: 1px 1px 4px black;
}

.heatmap {
    border: 0;
    position: absolute;
    top: 0;
    left: 0;
}
#proto_id, #outliers_title, #prototype_clicked_on{
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
    height:100%;
    position: relative;
}

#rightPanel{
    margin:0px;
    float:right;
    width:calc(100% - 440px);
    height:100%;
}

#outliers_header{
    margin:0px;
    float:left;
    width: 100%;
    clear:right; 
}

#outliers_container {
    width:98%;
    height: 100%;
    overflow: auto;
}

#som {
width:400px;
position: fixed;
/*clear: both;*/
}

#containertje{
position: fixed;
width:440px;
}

#aladin-lite-div{
    width:440px;
    height:800px;
    -webkit-box-shadow:0 6px 20px 0 rgba(0, 0, 0, 0.19);
    -moz-box-shadow:0 6px 20px 0 rgba(0, 0, 0, 0.19);
    box-shadow: 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}
</style>

<head>
    <title>LOFAR Visualization</title>
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
    //var width = div1.textContent;
    //var height = div2.textContent;
});

// Make Aladin Lite snippet appear and direct it to the coordinates
// of the clicked outlier
var last_clicked_outlier;
var chalk_once = true;
function outlier_click(i){
    $('#som').hide();
    $('.chalky1').hide();
    $(last_clicked_outlier).css({"border":"none",
        "-webkit-box-shadow":"none",
        "-moz-box-shadow":"none",
            "box-shadow":"none"
    });
    $('#containertje').show(400)
    $('#'+ i).css({"border":"2px solid red",
        "-webkit-box-shadow":"inset 0px 0px 0px 4px red",
        "-moz-box-shadow":"inset 0px 0px 0px 4px red",
            "box-shadow":"inset 0px 0px 0px 4px red"
});
    if (chalk_once == true){
        $('.chalky3').delay(1000).show(400);
        $('.chalky3').delay(10000).hide(400);
        chalk_once = false;};
    last_clicked_outlier = "#"+i; 
    go_to_aladin_outliers(i);
}   


function go_to_aladin_outliers(i) {
    // Open loc.txt, parse RA and Dec and goto these coordinates
    $.get('website/outliers/loc.txt', function(data) {
        var line = data.split("\n")[i];
        var ra = line.split(';')[0];
        var dec = line.split(';')[1];
        //console.log(ra, dec); // Uncomment to write coordinates to console
        aladin.gotoRaDec(ra,dec);
        aladin.setFov(12/60);
                   }, 'text');  
}

function changeProtoId(xxx,yyy) {
    // Update prototype coordinates to be (xxx,yyy)
    document.getElementById("proto_radio_id").innerHTML = "Radio sources from LOFAR survey that resemble the selected prototype (" + xxx + "," + yyy + "):";
}

$(function () {
    $('[data-toggle="popover"]').popover({html:true, content:function() {
                  return $('#owner-popover').html();
                }})
})


function show_outliers() {
    // Show outliers div, hide most others
	$('#red_square').show();
}
function show_outliers() {
    // Show outliers div, hide most others
	$('#prototype_clicked_on').hide();
	$('#outliers_header').show();
    $('#outliers_container').show();
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
          <li class="nav-item">
            <a class="nav-link" href="som.php">Home<span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" >Morphological outliers</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="about.php">Acknowledgements</a>
          </li>
        </ul>
      </div>
     <span style="text-align: right;font-size:10.2pt;color:#009cde;line-height:18px;font-family: Verdana,Arial,Segoe,sans-serif;">
LOFAR-PINK Visualization Tool by Rafa&euml;l Mostert
</span>
    </nav>

<div id="content">
<div id="leftpanel" >

    <!-- SOM explainer text -->
    <div class="chalky1" style="float:left;padding:0px;padding-top:380px;clear:both;">
        Click on one of the outliers to show them in context.
    </div>
    <!-- Arrow pointing at outliers -->
    <div class="chalky1" style="float:right;padding:5px;padding-top:0px;padding-left:80px">
        <img src="images/arrow-right.png" width="90px" height="30px">
    </div>  

</div>

    <!-- Embed Aladin snippet -->
    <div id="containertje" style="display:none;">
        <div id="aladin-lite-div" ></div>
        <script type="text/javascript" src="aladin/AladinLite-2017-08-25/aladin.min.js" charset="utf-8"></script>
        <script type="text/javascript">
            //var aladin = A.aladin('#aladin-lite-div', {survey: "P/DSS2/color", fov:3, target: "168.8126927145544, 53.322134981323224"});
        // INSERT A LINK TO THE PLACE WHERE YOU HOST YOUR SURVEY BELOW TO REPLACE WISE SURVEY    
	var hipsDir = "http://axel.u-strasbg.fr/HiPSCatService/II/328/allwise/";
        var aladin = $.aladin("#aladin-lite-div", 
            {showFullscreenControl: false, // Hide fullscreen controls 
            showGotoControl: false, // Hide go-to controls
            showFrame: false}); // Hide coordinates
            aladin.setImageSurvey(aladin.createImageSurvey('LOFARHETDEX', 'LOFAR (radio)',hipsDir, 'equatorial', 9, {imgFormat: 'png'}));
        </script>
        
        <!-- Text raising awareness about the Aladin controls-->
        <div class="chalky3" style="line-height:1.2;top:25px;left:40px;">   
                Observe the radio source in 
                different frequencies <br>&#10550; 
        </div>
        
        <div class="chalky3" style="top:420px;right:5px;
            width:150px">   
            Feel free to zoom, scroll or pan around <!--&#8594;-->
        </div>
    </div>
    
    <!-- Text explaining Aladin snippet -->
    <div class="chalky2" style="">
       On the left you can see where the radio source you clicked on is
        located on the sky. The source might be accompanied or interacting with other sources or be part of some larger structure!
    </div>
    

</div>

</div>
<center>

<?php
// Retrieve total number of outliers
$outliers_dirname = "website/outliers/";
$outliers_subdirnames = glob($outliers_dirname.'*' , GLOB_ONLYDIR);

$count_outliers = 0;
// Loop over all subdirectories
foreach($outliers_subdirnames  as $outliers_subdirname) {
    $count_outliers += count(glob($outliers_subdirname."/*.png"));
}
?>

<!-- right panel-->
<div id="rightPanel">
<!-- Outliers header div -->
<div id="outliers_header" class="imgContainer" >

<!-- On landing page, display instructions about the site -->
<div class="card" id="instructions" style="text-align:left;">
  <div class="card-body">
    <h2 class="card-title"><span style="color:#009cde;">100 morphologically rarest sources</span></h2>
    <p class="card-text" style="font-size:18pt">
    <?php
	echo "The Self-Organizing Map is a condensed representation of the most occurrent
     morphologies present in our dataset.<br> 
    <!--Each radio source from the dataset resembles one of the prototypes in the 
    Self-Organizing Map.--> If a source barely resembles any of the prototypes in the Self-Organizing Map, it 
    is thus a morphological outlier.<br>
    Using this heuristic, we show the $count_outliers most morphologically unique radio sources below:";
?>
  </div>
</div>

</div>
<!-- Div containing and showing outliers -->
<div id="outliers_container"> <!-- class="imgContainer" >-->



<?php
$outliers_dirname = "website/outliers/";
$outliers_subdirnames = glob($outliers_dirname.'*' , GLOB_ONLYDIR);

// Loop over all subdirectories
foreach($outliers_subdirnames  as $outliers_subdirname) {
    echo '<div id="'.$outliers_subdirname.'" class="imgContainer2">';
	list($maindir, $outlierdir, $prototype) = explode('/', $outliers_subdirname);
	list($x, $y) = explode('_', $prototype);

    echo 'Best resembling prototype ('.$x.','.$y.'): <br>';
     
    $images = glob($outliers_subdirname."/*.png");
    foreach($images as $image) {
        
        $parts = explode('/', $image);
        $outlier_number = (int)explode('.', $parts[3])[0];
        echo "<div id='$outlier_number' class='imgContainer' style='padding:1px;'>";
        //print_r($parts);        
        //print_r($outlier_number);        
        echo "<img src=\"$image\" onclick=\"outlier_click($outlier_number);\" width=\"200\" height=\"200\"/><br>";
        echo '</div>';
    }
}

?>

<div id="outliers_histogram" class="imgContainer3">
<img src="website/outliers/outliers_histogram.png">
<br>
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
