<?php
    include_once "./php_utils/header.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RapidC3 - Accueil</title>
    <link rel="stylesheet" href="<?php echo $css_index ?>">
    <link rel="stylesheet" href="<?php echo $css_style1 ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Google Fonts : Anton -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">


</head>

<body>
    <?php
    include_once $php_navbar;
    ?>
    <section class="position-relative">
        <video autoplay muted loop class="video-bg w-100">
            <source src="<?php echo $media ?>Food_Menu.mp4" type="video/mp4">
        </video>
        
        <div class="hero-content position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center text-white"                       style="background-color: rgba(48, 45, 45, 0.5);">
          <h1 class="display-4 " style="font-size: 10em; font-family: 'Brush Script MT', cursive; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);">Bienvenue chez RapidC3</h1>
        <a href="<?php echo $commande ?>" class="btn btn-warning py-2 px-4 rounded-pill fs-2 fw-bold text-black text-decoration-none"  >
  Commander maintenant </a>
    </div>
    </section>

    <section> 
       <h2 class="anton-regular">Commandez en ligne, cumulez des points, mangez plus vite</h2>
    </section>
    
    <div class="container footer-images">
        <div class="row">
          <div class="col-6 col-md-3 mb-3">
            <img src="<?php echo $media ?>dessert_image.jpg" alt="image1">
          </div>
          <div class="col-6 col-md-3 mb-3">
            <img src="<?php echo $media ?>legume_image.jpg" alt="image2">
          </div>
          <div class="col-6 col-md-3 mb-3">
            <img src="<?php echo $media ?>kebab4_image.jpg" alt="image3">
          </div>
          <div class="col-6 col-md-3 mb-3">
            <img src="<?php echo $media ?>pizza1.jpeg" alt="image4">
          </div>
        </div>
      </div>
      
      <div class="container text-center mb-5">
        <video autoplay muted loop class="img-fluid rounded" style="max-width: 600px; width: 100%; height: auto; object-fit: cover;">
          <source src="<?php echo $media ?>video_rapidc3.mp4" type="video/mp4">
        </video>
      </div>
      

    <?php
      include_once $php_footer;
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
