<?php

require_once "server.php";
global $db;

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

// Get user info
$username = $_SESSION['username'];
$password = $_SESSION['password'];
$result = $db->query("SELECT id, isAdmin FROM users WHERE username = '$username' AND password = '$password';")->fetch_assoc();
if (!$result) {
    // User info is incorrect
    header('location: login/');
    die;
}

$user_id = $result['id'];
$isAdmin = $result['isAdmin'];

$maxBets = $db->query("SELECT betlimit FROM users WHERE id = $user_id;")->fetch_assoc()['betlimit'];

// Get list of 5 last bets
$bets = $db->query("SELECT * FROM bets WHERE user = '$user_id' ORDER BY id DESC LIMIT 5;")->fetch_all();


require_once '../get_ad.php';
$ad_path = get_ad('../');

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon compte - Parimieux</title>
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
            <img src="../assets/img/parimieux_logo_white.svg" alt="Parimieux" height="70" style="max-height: 70px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" style="border: none;">
            <i class="bi bi-list" style="display: inline-block; font-size: 1.5em; color: white;"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav">
                <a class="nav-link" href="../paris/">Parier</a>
                <a class="nav-link" href="../statistiques.php">Statistiques</a>
                <a class="nav-link" href="../predictions.php">Prédictions</a>
                <?php
                if ($isAdmin) {
                    echo "<a class=\"nav-link\" href=\"admin/\">Administration</a>";
                }
                ?>
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
                    Bienvenue <b><?php echo $username; ?></b> !
                    <br><br>Cette page regroupe les informations et paramètres de votre compte.
                    <hr>
                    <form method="POST" action="index.php">
                        <legend>Paramètres du compte</legend>
                        <div class="mb-3">
                            <label for="inputUsername" class="form-label">Nom d'utilisateur</label>
                            <input type="text" id="inputUsername" name="inputUsername" class="form-control" placeholder="<?php echo $username ?>">
                            <?php
                            if (isset($errors))
                                if (in_array("username_taken", $errors))
                                    echo "<p class=\"text-danger\">* Ce nom d'utilisateur est déjà pris.</p>";
                            ?>
                        </div>

                        <div class="mb-3">
                            <label for="inputPassword" class="form-label">Mot de passe</label>
                            <input type="password" id="inputPassword" name="inputPassword" class="form-control">
                            <?php
                            if (isset($errors))
                                if (in_array("password_noMatch", $errors))
                                    echo "<p class=\"text-danger\">* Les mots de passe ne correspondent pas.</p>";
                            ?>
                        </div>
                        <div class="mb-3">
                            <label for="inputPasswordConfirm" class="form-label">Confirmer mot de passe</label>
                            <input type="password" id="inputPasswordConfirm" name="inputPasswordConfirm" class="form-control">
                        </div>
                        <hr>
                        <legend>Santé et bien-être</legend>
                        <div class="mb-3">
                            <label for="inputMaxBets">Limite de paris (EUR)</label>
                            <input type="number" class="form-control" id="inputMaxBets" name="inputMaxBets" placeholder="<?php echo $maxBets; ?>">
                            <p>&bull; Un montant de 0€ ne fixe aucune limite. </p>
                            <?php
                            if (isset($errors)) {
                                if (in_array("maxBets_illegal_value", $errors))
                                    echo "<p class=\"text-danger\">* Le montant est invalide.</p>";
                                if (in_array("maxBets_too_high", $errors))
                                    echo "<p class='text-danger'>* La limite ne peut pas dépasser 500€.</p>";
                            }

                            ?>
                        </div>
                        <button type="submit" class="btn btn-primary" name="buttonSaveSettings">Sauvegarder les modifications</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="bloc">
                <div class="bloc-content">
                    <h4>VOS 5 DERNIERS PARIS</h4>
                    <?php
                    if (count($bets) == 0) {
                        echo "Vous n'avez encore effectué aucun pari.";
                    } else {
                        foreach ($bets as &$bet) {
                            // Get bet info
                            $bet_id = $bet[0];
                            $bet_type = $bet[2];
                            $bet_team1 = $bet[3];
                            $bet_team1 = $db->query("SELECT name FROM teams WHERE id = $bet_team1;")->fetch_assoc()['name'];
                            $bet_score1 = $bet[4];
                            $bet_team2 = $bet[5];
                            $bet_team2 = $db->query("SELECT name FROM teams WHERE id = $bet_team2;")->fetch_assoc()['name'];
                            $bet_score2 = $bet[6];
                            $bet_match_date = $bet[7];
                            $bet_date = $bet[8];
                            $bet_amount = $bet[9];

                            echo "
                            <br>
                            <div class=\"card\">
                                <div class=\"card-body\">
                                    <h5 class=\"card-title\">$bet_team1 - $bet_team2</h5>
                                    <h6 class=\"card-subtitle mb-2 text-body-secondary\">Match du $bet_match_date</h6>
                                    <p class='card-text'>";

                            switch ($bet_type) {
                                case "0":
                                    echo "Pari score exact.<br>$bet_team1 : $bet_score1<br>$bet_team2 : $bet_score2<br>";
                                    break;
                                case "1":
                                    echo "Pari victoire d'un club.<br>Victoire de ";
                                    echo ($bet_score1 == "1") ? $bet_team1 : $bet_team2;
                                    echo ".<br>";
                                    break;
                                case "2":
                                    echo "Pari score d'un club.<br>";
                                    echo (!empty($bet_score1) || $bet_score1 === "0") ? "$bet_team1 : $bet_score1" : "$bet_team2 : $bet_score2";
                                    echo "<br>";
                                    break;
                                case "3":
                                    echo "Pari nombre total de buts.<br>Nombre de buts : $bet_score1.<br>";
                                    break;
                                case "4":
                                    echo "Pari écart de buts.<br>Ecart de buts : $bet_score1.<br>";
                                    break;
                            }

                            echo "Mise : $bet_amount (EUR).</p></div>";


                            // Check bet result
                            $result = $db->query("SELECT * FROM matchs WHERE team1 = '$bet[3]' AND team2 = '$bet[5]' AND date = '$bet_match_date';")->fetch_assoc();

                            if ($result) {
                                $match_team1 = $result['team1'];
                                $match_score1 = $result['score1'];
                                $match_team2 = $result['team2'];
                                $match_score2 = $result['score2'];

                                $won = false;

                                switch ($bet_type) {
                                    case "0":
                                        if ($match_score1 == $bet_score1 && $match_score2 == $bet_score2) {
                                            $won = true;
                                        }
                                        break;
                                    case "1":
                                        if ($bet_score1 == "1" && $match_score1 > $match_score2) {
                                            $won = true;
                                        } else if ($bet_score2 == "1" && $match_score1 < $match_score2) {
                                            $won = true;
                                        }
                                        break;
                                    case "2":
                                        if ($bet_score1 == $match_score1 || $bet_score2 == $match_score2) {
                                            $won = true;
                                        }
                                        break;
                                    case "3":
                                        if ($bet_score1 == ($match_score1 + $match_score2))
                                            $won = true;
                                        break;
                                    case "4":
                                        if ($bet_score1 == abs($match_score1 - $match_score2))
                                            $won = true;
                                        break;
                                }

                                if ($won) {
                                    echo "
                                        <div>
                                            <div class='card-footer text-bg-success'>
                                                Résultat : + $bet_amount (EUR)
                                            </div>
                                        </div>
                                    ";
                                } else {
                                    echo "
                                        <div>
                                            <div class='card-footer text-bg-danger'>
                                                Résultat : - $bet_amount (EUR)
                                            </div>
                                        </div>
                                    ";
                                }
                            } else {
                                echo "
                                <div>
                                    <div class='card-footer text-bg-secondary'>
                                        En attente du match...
                                    </div>
                                </div>";
                            }

                            echo "</div>";
                        }
                    }
                    ?>
                </div>
            </div>
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
                <img src="../assets/img/parimieux_logo_white.svg" alt="Parimieux" height="70">
            </div>
            <div class="col-md-4">
                Parimieux est un site de pari en ligne et de prédiction de rencontres et de résultats responsable.
            </div>
            <div class="col-md-3">
                <div class="row"><a href="../" class="link-secondary">Accueil</a></div>
                <div class="row"><a href="../paris/" class="link-secondary">Parier</a></div>
                <div class="row"><a href="../statistiques.php" class="link-secondary">Statistiques</a></div>
                <div class="row"><a href="../predictions.php" class="link-secondary">Prédictions</a></div>
                <div class="row"><a href="#" class="link-secondary">A propos de Parimieux</a></div>
            </div>
            <div class="col-md-3">
                <div class="row"><a href="#" class="link-secondary">Mon compte</a></div>
            </div>
        </div>
    </div>
</div>
<img class="d-block d-sm-none" src="<?php echo $ad_path; ?>" style="bottom: 0; width: 100vw;">
<img class="d-block d-sm-none" src="<?php echo $ad_path; ?>" style="position: fixed; bottom: 0; width: 100vw;">
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</html>