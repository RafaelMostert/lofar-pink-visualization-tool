<!doctype html>
<html>
<!-- Author: Rafael Mostert 2017-2019 -->
<!-- Mail: mostert @ strw.leidenuniv.nl -->

<style>
.jumbotron{
background-image: url(images/test6.png);
    background-position: 0% 25%;
        background-size: cover;
            background-repeat: no-repeat;
            color: white;
                text-shadow: black 0px 1px 10px;}
</style>

<head>
    <title>LOFAR Visualization</title>
	<meta name="robots" content="noindex,nofollow,noarchive,nosnippet,noodp">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Rafael Mostert">
    <!-- Include ASTRON favicon -->
    <link rel="icon" href="images/favicon.ico">
    
<!-- CSS Stylesheets
================================================================= -->
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
</head>

<body>
<!-- Bootstrap navbar
================================================================= -->
    <nav class="navbar navbar-expand-md navbar-light fixed-top" style="background-color: #FFFFFF;">
      <a class="navbar-brand" href="som.php">
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
          <li class="nav-item">
            <a class="nav-link" href="outliers.php">Morphological outliers</a>
          </li>
          <li class="nav-item active">
            <a class="nav-link">Acknowledgements</a>
          </li>
        </ul>
      </div>
     <span style="text-align: right;font-size:10.2pt;color:#009cde;line-height:18px;font-family: Verdana,Arial,Segoe,sans-serif;">
LOFAR-PINK Visualization Tool by Rafa&euml;l Mostert
</span>
    </nav>

    <main role="main">

<!-- Main acknowledgement section
================================================================= -->
      <div class="jumbotron">
        <div class="container">
            <h1 class="display-6">Unveiling the morphologies of the faint radio source population through unsupervised machine learning</h1>
          <p>
This interactive website is developed by Rafa&euml;l Mostert as part of his master thesis and PhD
<br>under supervision of
 Prof.dr. H.J.A. R&ouml;ttgering and Dr. K.J. Duncan at Leiden University.<br><br> 
The Self-Organizing Map is trained using the <i>PINK</i> software.
<br>
The radio data, 120-168Mhz, is taken from the LOFAR Two-metre Sky Survey (<a href="https://lofar-surveys.org/surveys.html">LoTSS wide area</a>).
<br>
<!--The optical data, is taken from the SLOAN Digital Sky Survey (<a href="https://www.sdss.org/">SDSS</a>).
-->
The optical data, is taken from the <a href="https://panstarrs.stsci.edu/">PanSTARRS</a>) survey.
<br><br>
See contact information for access to the master thesis, or more information on the project.<br>
</p>
        </div>
      </div>

<!-- Contact cards
================================================================= -->
      <div class="container">
        <div class="row">
          <div class="col-md-1.5">
    <img src="images/author-small.png" width="100px" height="116px">
          </div>
          <div class="col-md-4">
            <h2><span style="color:#009cde;">Contact information</span></h2>
        <p>Rafa&euml;l Mostert, rafaelmostert@gmail.com<br>
        Astronomy PhD student at the Leiden Observatory, Leiden University.</p>
          </div>
          <div class="col-md-4">
            <h2><span style="color:#009cde;">PINK software license</span></h2>
    <p><i>PINK</i>, Copyright (C) 2015 Kai Lars Polsterer (HITS gGmbH).</p>
          </div>
          <div class="col-md-2">
            <h2><span style="color:#009cde;">Funding</span></h2>
    Funding for this project is provided by ASTRON and Leiden University.
          </div>
        </div>

        <hr>

      </div> 

    </main>
<footer class="container">
<h4 class="card-title"><span style="color:#009cde;">Main references</span></h4>
K. L. Posterer, F. Gieseke, C. Igel, B. Doser, N. Gianniotis.<i> Parallelized rotation and flipping INvariant Kohonen maps (PINK) on GPUs. </i>ESANN 2016.<br>
T. W. Shimwell et all.<i> The LOFAR Two-metre Sky Survey - I. Survey description and preliminary data release. </i>A&A, 598 (2017) A104.<br>
R.I.J. Mostert, K.J. Duncan, H.J.A. Röttgering.<i> Unveiling the morphologies of the faint radio source population through unsupervised machine learning: 
Applying Self-Organizing Maps to the LOFAR Two-meter Sky Survey.</i> Msc thesis, Leiden University (2017).
    </footer>
    
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster-->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>

</body>

</html>

