<!doctype html>
<html>
<!-- Author: Rafael Mostert 2017-2019 -->
<!-- Mail: rafaelmostert @ gmail.com -->
<!-- Mail: mostert @ strw.leidenuniv.nl -->

<head>
    <title>SOM Visualization</title>
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

var pointing_size = 3.7; //degree
var fov = 30.0/60; //degree
var fov_arr;
var cam_len;
var t = 0;
var move = 0;
var store_t = 0;
var store_ra = 0;
var store_dec = 0;
var start_ra = 0;
var start_dec = 0;
var max_ra = 0;
var max_dec = 0;
var width, height, s_ra, s_dec;
var pointing_number = 0;
console.log($(window).width());
console.log($(window).height());


$(window).resize(function(){
    $('#control-div').css('width', 0.9 * $(window).width() );
    $('#aladin-lite-div').css('width', 0.9 * $(window).width() );
    $('#aladin-lite-div').css('height', 0.75 * $(window).height());
        
});


//$('#aladin-lite-div').css('width', $(window).width());
    //$('#aladin-lite-div').css('height', $(window).height());
// Triggered when clicking a single cut-out
// reveals Aladin Lite snippet and centers this
// snippet on the coordinates of the cut-out
var last_clicked_cutout;
var chalk_once = true;
var store_i;
var a_ra, a_dec;
function cutout_click(i){
    store_i = i;
    $('.fill').hide(100);
    $('.chalky-font1').hide(100);
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
    $('.chalky-font2').delay(800).show(400);
    if (chalk_once == true){
        $('.chalky-font3').delay(6000).show(400);
        $('.chalky-font-black').delay(6000).show(400);
        $('.chalky-font3').delay(16000).hide(400);
        chalk_once = false;};
    last_clicked_cutout = "#cutouts"+i; 
    go_to_aladin(i);
}   
var stepsize = 10/3600; //degree

// Show Aladin snippet and go to middle of the pointing
function pointing_click(key){
    pointing_number = key;
    var pointing_name = document.getElementById("pointingname");
    pointing_name.innerHTML = key;
    console.log('key is', key);
    var RA;
    var DEC;
    //aladin.setFovRange(0.03, 30);
    $('#leftpanel').hide();
	$('#instructions').hide();

    $('#containertje').show();
    $.getJSON("data.json", function(json) {
        console.log('key is', key, typeof(key));
            RA = json[key].ra; // access the array
            DEC = json[key].dec; // access the array


    aladin.setFov(fov)
    aladin.gotoRaDec(RA,DEC);
        
        
    // Calculate startpoint of videotour
    fov_arr = aladin.getFov();
    cam_len = pointing_size - fov_arr[0];
    s_ra = RA - pointing_size/2 + fov_arr[0]/2; 
    s_dec = DEC - pointing_size/2 + fov_arr[1]/2; 
    max_ra = RA + pointing_size/2 - fov_arr[0]/2; 
    max_dec = DEC + pointing_size/2 - fov_arr[1]/2; 

    store_ra = s_ra;
    store_dec = s_dec;
    a_ra = s_ra;
    a_dec = s_dec;
    start_ra = s_ra;
    start_dec = s_dec;
    console.log('Fov en sra en dec enzo:', fov_arr, s_ra, s_dec);
    t = 0;
    setTimeout(function() {aladin.gotoRaDec(RA,DEC);},110);
        });
}

// Go to next pointing
function next_pointing() {
    pointing_number++;
    pointing_click(pointing_number);
}

var sign_ra = 1;
var sign_dec = 0;
var max_t = 2000;
//var time_delay = 1000/60; // milliseconds
var time_delay = 1000/100; // milliseconds
//var stepsize = 0.2/60;
var tmin = 0;
var distance_travelled = 0;
var d = 0;
// delayed loop function
function delayed_loop () {         
setTimeout(function () {  
    console.log('stepsize:', stepsize);
    distance_travelled += stepsize;
    d = distance_travelled % (2*cam_len + 2*fov_arr[1]);
    if (d < cam_len){
    sign_ra = 1;
    sign_dec = 0;
    } else{
        if (cam_len + fov_arr[1] > d && 
            d > cam_len) {
        sign_ra = 0;
        sign_dec = 1;

        } else {
            if (d > 2*cam_len + fov_arr[1]) {
                sign_ra = 0;
            sign_dec = 1}
            else{
    sign_ra = -1;
    sign_dec = 0;}
    }}
    console.log('richting in ra', sign_ra, fov_arr[1] - stepsize, cam_len, distance_travelled, d, store_ra, max_ra, store_dec, max_dec)
    store_ra += stepsize*sign_ra;
    store_dec += stepsize*sign_dec;
    aladin.gotoRaDec(store_ra, store_dec);
    t++;                    
    if (store_ra < max_ra && store_dec < max_dec && move == 1) {            //  if the counter < 10, call the loop function
         delayed_loop();             //  ..  again which will trigger another 
    }else{ 
        if (move ==1) {
        console.log('Einde');
        $('#play_pause_button').text('Replay');}}
}, time_delay)
}


// Speed control slider
$(document).ready(function() { //Make sure to load javascript after html is loaded
var slider = document.getElementById("myRange");
var output = document.getElementById("demo");
output.innerHTML = slider.value;

slider.oninput = function() {
    console.log('jaaaa');
    stepsize = this.value / 3600;
  output.innerHTML = this.value;
}
});


// Pause moving
function pause() {
move = 0;
$('#play_pause_button').text(' Play ');
console.log('Pause...');
}

// Rewind moving
function rewind() {
$('#play_pause_button').text('Replay');
}

//  Play 
function play() {
    $('#play_pause_button').text('Pause');
    console.log('Start playing...');
    move = 1;
    console.log('in play',store_ra, store_dec, stepsize);
    delayed_loop(store_ra, store_dec, stepsize);
}

//Start moving
function play_pause() {
    if (document.getElementById("play_pause_button").innerHTML == ' Play '){
    play();} else {
    if (document.getElementById("play_pause_button").innerHTML == 'Pause'){
        pause();
    }else {
        console.log('Replay...');
        store_ra = start_ra;
        store_dec = start_dec;
        sign_ra = 1;
        sign_dec = 0;
        distance_travelled = 0;
        console.log(distance_travelled, store_ra, store_dec);
        setTimeout(function () {
        t = 0;
        play();
        },150);
    }
    }

}

</script>
</head>




<body style="width:100%;height:100%;max-width:none">

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
            <a class="nav-link" href="">Home<span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="outliers.php">Morphological outliers</a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="inspect-pointing.php">Inspect pointing</a>
          </li>
          <!--<li class="nav-item">
            <a class="nav-link" onclick="load_file();">Downloads</a>
          </li>-->
          <li class="nav-item">
            <a class="nav-link" href="about.php">Acknowledgements</a>
          </li>
        </ul>
      </div>
     <span style="text-align: right;font-size:10.2pt;color:#009cde;line-height:18px;font-family: Verdana,Arial,Segoe,sans-serif;">
SOM Visualization Tool by <a href="https://github.com/RafaelMostert">Rafa&euml;l Mostert</a>
</span>
    </nav>

<div id="content">

<!-- Panel containing clickable SOM 
================================================================= -->
<div id="leftpanel" >

    <div id="som" class="containerdiv">
        <img id="map1" src="images/superterp-redshift.png"  
            border="0" width="400px" height="400px" alt="" class="imgContainer" >

    
<!-- SOM explainer text -->
    <div class="chalky-font1" style="float:right;padding:0px;clear:both;">
       Birds-eye view of the central antennas of the Low Frequency Array (LOFAR) telescope.
        Located in the Netherlands.
    </div>
</div>
</div>

  
<!-- Embedded Aladin snippet 
================================================================= -->
<center>
    <div id="containertje" style="width:100%;height:90%;display:none;">
    
        <!--Radio buttons -->
        <div id="aladin-buttons" >
        <input id="LOFAR_DR1" type="radio" name="survey" value="LOFAR_DR1" >
            <label for="LOFAR_DR1">LoTSS DR1 </label>
        <input id="LOFAR_DR2" type="radio" name="survey" value="LOFAR_DR2" checked>
            <label for="LOFAR_DR2">LoTSS DR2 </label>
        <input id="panstars" type="radio" name="survey" value="P/PanSTARRS/DR1/color-z-zg-g">
            <label for="PanSTARRS">Optical (PanSTARRS) </label>
        <input id="allWISE" type="radio" name="survey" value="P/allWISE/color">
            <label for="allWISE">IR (allWISE)  </label>
        <!--<input id="SDSS9" type="radio" name="survey" value="P/SDSS9/color-alt">
            <label for="SDSS9">Optical (SDSS9)  <label>-->
        </div>

        <div id="aladin-lite-div" ></div>
        <script type="text/javascript">
    $('#control-div').css('width', 0.9 * $(window).width() );
    $('#aladin-lite-div').css('width', 0.9 * $(window).width() );
    $('#aladin-lite-div').css('height', 0.75 * $(window).height());
        </script>
        <script type="text/javascript" src="http://aladin.u-strasbg.fr/AladinLite/api/v2/latest/aladin.min.js" charset="utf-8"></script>
        <script type="text/javascript">
        // INSERT A LINK TO THE PLACE WHERE YOU HOST YOUR SURVEY BELOW TO REPLACE WISE SURVEY    
	var hipsDir = "http://lofar.strw.leidenuniv.nl/hips_lotss_dr1_high"; 
	var hipsDir2 = "http://lofar.strw.leidenuniv.nl/LoTSS_DR2_high_hips_private"; 
        var aladin = A.aladin("#aladin-lite-div", 
            {showFullscreenControl: true, // Hide fullscreen controls 
            showGotoControl: true, // Hide go-to controls
            showShareControl: false, // Show share controls
            showSimbadPointerControl: true, // Show simbad pointer
            showFrame: false, //Hide frame 'J2000' enzo
            target: [210.8,54.3], // initial coordinates M101
            fov: 0.5,
            showLayersControl : false,}); // Hide coordinates

            aladin.setImageSurvey(aladin.createImageSurvey('LOFAR_DR1', 'LOFAR DR1 (radio)',hipsDir, 'equatorial', 9, {imgFormat: 'png'}));

            aladin.setImageSurvey(aladin.createImageSurvey('LOFAR_DR2', 'LOFAR DR2 (radio)',hipsDir2, 'equatorial', 9, {imgFormat: 'png'}));
            $('input[name=survey]').change(function() {
                aladin.setImageSurvey($(this).val());
            });
        </script>



<div id="bottom-control-div">

<!-- Button to go back to overview -->
    <div id="pointing-navigation" >
    <button type="button" class="btn btn-primary" onclick="location.href='inspect-pointing.php';" id="go_back_button">Back to all pointings</button> Current pointing: <span id="pointingname"></span>
<!-- Button to go next pointing -->
    <button type="button" class="btn btn-primary" onclick="pointing-click();" id="next_button">Next</button>
</div>

        
<!-- Button to toggle move -->
<div id="playback-box">
   <button type="button" class="btn btn-primary" onclick="play_pause();" id="play_pause_button"> Play </button>
<!-- Slider for playback speed -->
   Play back speed <span id="demo"></span> arcsec/timestep
  <input type="range" min="1" max="60" value="10" class="slider" id="myRange"></div>
</div>

<!-- Text explaining Aladin snippet 
    <div class="info" style="width:100px;">
    On the left you can see where the radio source you clicked on is
        located on the sky. The source might be accompanied or interacting with other sources or be part of some larger structure!
<br>
<br>
    </div>
-->

  
    </div>
    




<!-- On landing page, display welcome message and instructions about the site 
================================================================= -->
<div class="card" id="instructions" style="width:900px;text-align:left;">
  <div class="card-body">
    <h2 class="card-title"><span style="color:#009cde;" id="instruction_title">Visually inspect a pointing</span></h2>
    <p class="card-text" style="font-size:14pt" id="som_properties">

Each pointing extends 3.7' across the radio (120-168 MHz) sky and has been observed for 8 hours by the LOFAR telescope as part of the <a href='https://lofar-surveys.org/surveys.html'>LoTSS wide area survey</a>.
<br><br>
See the <a href='https://lofar-surveys.org/status.html'>survey status page</a> for a progress overview and the current sky coverage.
<br><br>
Click on one of the pointings below to inspect:<br>

<?php
// Insert links to all pointings

$handle = fopen("pointings.txt", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        // process the line read.
        $line = preg_replace('/\s+/', '', $line);
        echo "<a  id='$line' href='#' onclick='pointing_click(\"$line\")'>$line</a> ";
    }

    fclose($handle);
}

 $jsonurl='data.json';
  $json = file_get_contents($jsonurl,0,null,null);
  $json_output = json_decode($json, JSON_PRETTY_PRINT);
  echo "<br> ".count($json_output)." pointings in total.";


?>

  </div>
</div>

</center>
    
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster-->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>

</body>
</html>

