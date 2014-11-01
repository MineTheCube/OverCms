<?php

$data = json_decode( $data );

$content = '
<link href="' . HEADER . 'assets/animate.css" type="text/css" rel="stylesheet" />
<style>
.carousel .item {
    left: 0 !important;
      -webkit-transition: opacity .8s; /*adjust timing here */
         -moz-transition: opacity .8s;
           -o-transition: opacity .8s;
              transition: opacity .8s;
}
.carousel-control {
}
/* Fade controls with items */
.active.left,
.active.right {
    opacity: 0;
    z-index: 2;
}
.carousel-control {
    z-index: 3;
}
.carousel {
    padding-top: 0px;
    box-shadow: 0 1px 8px rgba(0, 0, 0, 0.2);
}
.carousel .item {
    height: 500px;
    background-size: cover;
}
.carousel-caption {
    margin: 0 auto; 
    height: 320px;
}
.carousel-caption h1 {
    font-size: 60px;
}
.carousel-caption p {
    font-size: 30px;
}
@media (max-width: 767px) {
    .carousel-caption {
        height: 230px;
    }
    .carousel-caption h1 {
        font-size: 30px;
    }
    .carousel-caption p {
        font-size: 18px;
    }
    .carousel .item {
        height: 300px;
    }
}
@media (min-width: 768px) and (max-width: 991px) {
    .carousel-caption {
        height: 230px;
    }
    .carousel-caption h1 {
        font-size: 45px;
    }
    .carousel-caption p {
        font-size: 24px;
    }
    .carousel .item {
        height: 350px;
    }
}
@media (min-width: 992px) and (max-width: 1200px) {
    .carousel-caption {
        height: 260px;
    }
    .carousel-caption h1 {
        font-size: 50px;
    }
    .carousel-caption p {
        font-size: 26px;
    }
    .carousel .item {
        height: 400px;
    }
}
</style>
<div id="carousel-main" class="carousel slide" data-ride="carousel" data-interval="8000">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <li data-target="#carousel-main" data-slide-to="0" class="active"></li>
    <li data-target="#carousel-main" data-slide-to="1"></li>
    <li data-target="#carousel-main" data-slide-to="2"></li>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner">
    <div class="item active" style="background-image: url(\'' . HEADER . 'img/' . '1' .'.jpg' . '\');">
      <div class="carousel-caption">
        <h1 class="animated fadeInDown">Bienvenue sur {NAME} !</h1>
        <p class="animated fadeInUp">Un serveur comportant de nombreux types de jeux..</p>
      </div>
    </div>
    <div class="item" style="background-image: url(\'' . HEADER . 'img/' . '2' .'.jpg' . '\');">
      <div class="carousel-caption">
        <h1 class="animated fadeInLeft">Un slider d\'exception !</h1>
        <p class="animated fadeInRight">Avec des lettres, des mots et même du texte !</p>
      </div>
    </div>
    <div class="item" style="background-image: url(\'' . HEADER . 'img/' . '7' .'.jpg' . '\');">
      <div class="carousel-caption animated fadeInUp">
        <h1>A partir de 12900€</h1>
        <p>Avec condition de reprise, bonus écologique inclus.</p>
      </div>
    </div>
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#carousel-main" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
  </a>
  <a class="right carousel-control" href="#carousel-main" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
  </a>
</div>
';

?>