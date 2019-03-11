<!doctype html>
<html>
<!-- Author: Rafael Mostert 2017-2019 -->
<!-- Mail: mostert @ strw.leidenuniv.nl -->

<head>
    <title>LOFAR Visualization</title>
	<meta name="robots" content="noindex,nofollow,noarchive,nosnippet,noodp">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
    <!-- Include ASTRON favicon -->
    <link rel="icon" href="images/favicon.ico">


<!-- CSS Stylesheets
================================================================= -->
    <!-- Own stylesheet -->
    <link href="default.css" rel="stylesheet" type="text/css">
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
    <!-- Aladin Lite CSS -->
    <link rel="stylesheet" href="//aladin.u-strasbg.fr/AladinLite/api/v2/latest/aladin.min.css" />

<!-- Javascript functions 
================================================================= -->
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
    $('.chalky-font1').hide();
    $(last_clicked_outlier).css({"border":"none",
        "-webkit-box-shadow":"none",
        "-moz-box-shadow":"none",
            "box-shadow":"none"
    });
    $('#aladin-container-outliers').show(400)
    $('#'+ i).css({"border":"2px solid red",
        "-webkit-box-shadow":"inset 0px 0px 0px 4px red",
        "-moz-box-shadow":"inset 0px 0px 0px 4px red",
            "box-shadow":"inset 0px 0px 0px 4px red"
});
    if (chalk_once == true){
        $('.chalky-font3').delay(1000).show(400);
        $('.chalky-font3').delay(10000).hide(400);
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
        console.log(ra, dec); // Uncomment to write coordinates to console
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
	$('#outliers-header').show();
    $('#outliers_container').show();
}
</script>
</head>

<body>

<!-- Bootstrap navbar 
================================================================= -->
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
<!-- SOM explainer text and arrows 
================================================================= -->
<div id="leftpanel-outliers" >

    <!-- SOM explainer text -->
    <div class="chalky-font1" style="float:left;padding:0px;padding-top:380px;clear:both;">
        Click on one of the outliers to show them in context.
    </div>
    <!-- Arrow pointing at outliers -->
    <div class="chalky-font1" style="float:right;padding:5px;padding-top:0px;padding-left:80px">
        <img src="images/arrow-right.png" width="90px" height="30px">
    </div>  

</div>

<!-- Embedded Aladin snippet 
================================================================= -->
    <div id="aladin-container-outliers" style="display:none;">
        <div id="aladin-lite-div-outliers" ></div>
        <input id="LOFARHETDEX" type="radio" name="survey" value="LOFARHETDEX" checked>
            <label for="LOFARHETDEX">Radio (LOFAR)  <label>
        <input id="SDSS9" type="radio" name="survey" value="P/SDSS9/color">
            <label for="SDSS9">Optical (SDSS9)  <label>
        <input id="allWISE" type="radio" name="survey" value="P/allWISE/color">
            <label for="allWISE">Infra-red (allWISE)  <label>
        <script type="text/javascript" src="//aladin.u-strasbg.fr/AladinLite/api/v2/latest/aladin.min.js" charset="utf-8"></script>
        <script type="text/javascript">
        // INSERT A LINK TO THE PLACE WHERE YOU HOST YOUR SURVEY BELOW TO REPLACE WISE SURVEY    
	var hipsDir = "http://lofar.strw.leidenuniv.nl/hips_lotss_dr1_high"; 
        var aladin = A.aladin("#aladin-lite-div-outliers", 
            {showFullscreenControl: false, // Hide fullscreen controls 
            showGotoControl: false, // Hide go-to controls
            showFrame: false, //Hide frame 'J2000' enzo
            showLayersControl : false,}); // Hide coordinates
            aladin.setImageSurvey(aladin.createImageSurvey('LOFARHETDEX', 'LOFAR (radio)',hipsDir, 'equatorial', 9, {imgFormat: 'png'}));
            $('input[name=survey]').change(function() {
                aladin.setImageSurvey($(this).val());
            });
        </script>
        <!-- Text raising awareness about the Aladin controls-->
        <div class="chalky-font3" style="line-height:1.2;bottom:0px;left:40px;color:black;">   
                Observe the radio source in 
                different frequencies  
        </div>
        
        <div class="chalky-font3" style="top:320px;right:5px;
            width:150px">   
            Feel free to zoom, scroll or pan around <!--&#8594;-->
        </div>
    </div>
    
    <!-- Text explaining Aladin snippet -->
    <div class="chalky-font4" style="">
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
<div id="right-panel">
<!-- Outliers header div -->
<div id="outliers-header" class="imgContainer" >

<!-- On landing page, display welcome message and instructions about the site 
================================================================= -->
<div class="card" id="instructions" style="text-align:left;">
  <div class="card-body">
    <h2 class="card-title"><span style="color:#009cde;">100 morphologically rarest sources</span></h2>
    <p class="card-text" style="font-size:14pt">
    <?php
	echo "The Self-Organizing Map is a condensed representation of the most occurrent
     morphologies present in our dataset.<br> 
    <!--Each radio source from the dataset resembles one of the prototypes in the 
    Self-Organizing Map.--> If a source barely resembles any of the prototypes in the Self-Organizing Map, it 
    is thus a morphological outlier.<br>
    Using this heuristic, we show the $count_outliers most morphologically unique radio sources from the <a href='https://lofar-surveys.org/surveys.html'>LoTSS wide field radio survey</a> below:";
?>
  </div>
</div>

</div>

<!-- Div containing and showing outliers
================================================================= -->
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
<!-- Placed at the end of the document so the pages load faster-->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>

</body>
</html>
