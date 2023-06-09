<?php

require_once "get_ad.php";
$ad_path = get_ad();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PariPlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@100;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pariplus.css">
    <link rel="stylesheet" href="style.css">


</head>
<body>
<div class="navbar navbar-expand-md navbar-dark fixed-top"
     style="background-color: var(--pariplus_red); padding: 0 10px 0 10px;">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="assets/img/pariplus_logo_white.svg" alt="PariPlus" height="70" style="max-height: 70px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" style="border: none;">
            <i class="bi bi-list" style="display: inline-block; font-size: 1.5em; color: white;"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav">
                <a class="nav-link" href="paris/">Parier</a>
                <a class="nav-link" href="#">Statistiques</a>
                <a class="nav-link" href="predictions.php">Prédictions</a>
                <a class="nav-link" href="account/">Mon Compte</a>
            </div>
        </div>
    </div>
</div>
<div class="container" style="margin-top: 80px;">
    <div class="row">
        <div class="col-lg-8">
            <div class="container bloc bloc-content">
                <h1>Liste des équipes</h1><br>
                <div class="row row-cols-auto" id="stat-equipe"
                     style="margin: 0 auto !important; justify-content: center;">

                </div>
                <div class="loadMoreContainer">
                    <button id="loadMoreEquipeBtn">+</button>
                </div>
            </div>

            <div class="container bloc bloc-content">
                <h1>Liste des joueurs</h1><br>
                <div class="row row-cols-auto" id="prediction-joueur"
                     style="margin: 0 auto !important; justify-content: center;">

                </div>
                <div class="loadMoreContainer">
                    <button id="loadMoreJoueurBtn">+</button>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="bloc">
                <img class="bloc-ad d-sm-block d-none" src="<?php echo $ad_path; ?>" alt="Publicité">
            </div>
        </div>
    </div>
</div>


<div class="footer text-secondary" style="background-color: var(--pariplus_dark);">
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <img src="assets/img/pariplus_logo_white.svg" alt="PariPlus" height="70">
            </div>
            <div class="col-md-4">
                Pariplus est un site de pari en ligne et de prédiction de rencontres et de résultats responsable.
            </div>
            <div class="col-md-3">
                <div class="row"><a href="index.php" class="link-secondary">Accueil</a></div>
                <div class="row"><a href="paris/" class="link-secondary">Parier</a></div>
                <div class="row"><a href="#" class="link-secondary">Statistiques</a></div>
                <div class="row"><a href="predictions.php" class="link-secondary">Prédictions</a></div>
                <div class="row"><a href="#" class="link-secondary">A propos de Pariplus</a></div>
            </div>
            <div class="col-md-3">
                <div class="row"><a href="account/" class="link-secondary">Mon compte</a></div>
            </div>
        </div>
    </div>
</div>
<img class="d-block d-sm-none" src="<?php echo $ad_path; ?>" style="bottom: 0; width: 100vw;">
<img class="d-block d-sm-none" src="<?php echo $ad_path; ?>" style="position: fixed; bottom: 0; width: 100vw;">
</body>
<script src="data_joueurs.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
</html>


