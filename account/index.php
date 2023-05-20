<?php

require_once "server.php";

session_start();

// Check if user is connected
if (!isset($_SESSION['username'])) {
    if (isset($_COOKIE['pput'])) {
        if (!logToken()) {
            header('location: ../logout.php');
        }
    } else {
        header('location: login/');
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon compte - PariPlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@100;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../pariplus.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="navbar navbar-expand-md navbar-dark fixed-top" style="background-color: var(--pariplus_red); padding: 0 10px 0 10px;">
    <div class="container">
        <a class="navbar-brand" href="../">
            <img src="../assets/img/pariplus_logo_white.svg" alt="PariPlus" height="70" style="max-height: 70px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" style="border: none;">
            <i class="bi bi-list" style="display: inline-block; font-size: 1.5em; color: white;"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav">
                <a class="nav-link" href="#">Parier</a>
                <a class="nav-link" href="#">Statistiques</a>
                <a class="nav-link" href="#">Prédictions</a>
                <a class="nav-link" href="logout.php">Déconnexion</a>
            </div>
        </div>
    </div>
</div>
<div class="container" style="margin-top: 80px;">
    <div class="row">
        <div class="col-lg-8">
            <div class="bloc">
                <div class="bloc-content">
                    <h4>Mon compte</h4>
                    Bienvenue <b><?php echo $_SESSION['username']; ?></b> !
                    <br><br>Cette page regroupe les informations et paramètres de votre compte.
                    <hr>
                    <form method="POST" action="server.php">
                        <legend>Paramètres du compte</legend>
                        <div class="mb-3">
                            <label for="inputUsername" class="form-label">Nom d'utilisateur</label>
                            <input type="text" id="inputUsername" name="inputUsername" class="form-control" placeholder="Votre nom">
                            <p class="text-danger">* Ce nom d'utilisateur est déjà pris.</p>
                        </div>

                        <div class="mb-3">
                            <label for="inputPassword" class="form-label">Mot de passe</label>
                            <input type="password" id="inputPassword" name="inputPassword" class="form-control">
                            <p class="text-danger">* Les mots de passe ne correspondent pas.</p>
                        </div>
                        <div class="mb-3">
                            <label for="inputPasswordConfirm" class="form-label">Confirmer mot de passe</label>
                            <input type="password" id="inputPasswordConfirm" name="inputPasswordConfirm" class="form-control">
                        </div>
                        <hr>
                        <legend>Santé et bien-être</legend>
                        <div class="mb-3">
                            <label for="inputMaxBets">Limite de paris (EUR)</label>
                            <input type="number" class="form-control" id="inputMaxBets" name="inputMaxBets" placeholder="0">
                            <p>&bull; Un montant de 0€ ne fixe aucune limite. </p>
                            <p class="text-danger">* Le montant est invalide.</p>
                        </div>
                        <button type="submit" class="btn btn-primary" name="buttonSaveSettings">Sauvegarder les modifications</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="bloc">
                <div class="bloc-content">
                    <h4>Historique des paris</h4>
<!--                    Vous n'avez aucun pari enregistré.-->
                    <div class="card text-bg-success" style="margin-top: 10px">
                        <div class="card-header">PSG - FCB | 7 - 0</div>
                        <div class="card-footer">Gagnant : + 370EUR</div>
                    </div>
                    <div class="card text-bg-danger" style="margin-top: 10px">
                        <div class="card-header">PSG - FCB | 0 - 1</div>
                        <div class="card-footer">Perdant : - 1EUR</div>
                    </div>
                    <div class="card text-bg-success" style="margin-top: 10px">
                        <div class="card-header">PSG - FCB | 7 - 0</div>
                        <div class="card-footer">Gagnant : + 370EUR</div>
                    </div>
                </div>
            </div>
            <div class="bloc">
                <img class="bloc-ad d-sm-block d-none" src="../assets/img/Wilhem%20Motors.jpg" alt="Publicité">
            </div>
        </div>
    </div>
</div>
<div class="footer text-secondary" style="background-color: var(--pariplus_dark);">
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <img src="../assets/img/pariplus_logo_white.svg" alt="PariPlus" height="70">
            </div>
            <div class="col-md-4">
                Pariplus est un site de pari en ligne et de prédiction de rencontres et de résultats responsable.
            </div>
            <div class="col-md-3">
                <div class="row"><a href="../" class="link-secondary">Accueil</a></div>
                <div class="row"><a href="#" class="link-secondary">Parier</a></div>
                <div class="row"><a href="#" class="link-secondary">Statistiques</a></div>
                <div class="row"><a href="#" class="link-secondary">Prédictions</a></div>
                <div class="row"><a href="#" class="link-secondary">A propos de Pariplus</a></div>
            </div>
            <div class="col-md-3">
                <div class="row"><a href="#" class="link-secondary">Mon compte</a></div>
            </div>
        </div>
    </div>
</div>
<img class="d-block d-sm-none" src="../assets/img/Wilhem%20Motors.jpg" style="bottom: 0; width: 100vw;">
<img class="d-block d-sm-none" src="../assets/img/Wilhem%20Motors.jpg" style="position: fixed; bottom: 0; width: 100vw;">
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</html>