<?php

require_once 'bet.php';
global $db;
global $user_id;

// Check if user is connected
if (!isset($_SESSION['username'])) {
    if (isset($_COOKIE['pput'])) {
        if (!logToken()) {
            header('location: ../account/logout.php');
        }
    } else {
        header('location: ../account/login/');
    }
}

// Get list of teams
$teams = $db->query("SELECT * FROM teams;")->fetch_all();

// Get list of 5 last bets
$bets = $db->query("SELECT * FROM bets WHERE user = '$user_id' ORDER BY id DESC LIMIT 5;")->fetch_all();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PariPlus</title>
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
                <a class="nav-link" href="../statistiques.html">Statistiques</a>
                <a class="nav-link" href="../predictions.html">Prédictions</a>
                <a class="nav-link" href="../account/">Mon Compte</a>
            </div>
        </div>
    </div>
</div>
<div class="container" style="margin-top: 80px;">
    <div class="row">
        <div class="col-lg-8">
            <div class="bloc">
                <div class="bloc-content">
                    <h1>Effectuer un pari</h1>
                    Rendez-vous sur les pages Statistiques et Prédictions pour bénéficier de nos outils avancés !<br>
                    Si vous gagnez, vous obtenez le montant de votre mise.<br>
                    Commencez par choisir un match puis un type de pari :

                    <br><br>

                    <form action="index.php" method="POST">
                        <div class="mb-3">
                            <label for="selectTeam1" class="form-label">Equipe 1</label>
                            <select id="selectTeam1" name="selectTeam1" class="form-select">
                                <?php
                                foreach ($teams as &$team) {
                                    $teamId = $team[0];
                                    $teamName = $team[1];
                                    echo "<option value=\"$teamId\">$teamName</option>";
                                }
                                ?>
                            </select>
                            <?php
                            if (isset($errors)) {
                                if (in_array("team1_empty", $errors))
                                    echo "<p class=\"text-danger\">* L'équipe ne peut pas être vide.</p>";
                                if (in_array("team1_illegal_value", $errors))
                                    echo "<p class=\"text-danger\">* L'équipe n'est pas valide.</p>";
                                if (in_array("teams_same", $errors))
                                    echo "<p class=\"text-danger\">* Les équipes ne peuvent pas être identiques.</p>";
                            }
                            ?>
                        </div>
                        <div class="mb-3">
                            <label for="selectTeam2" class="form-label">Equipe 2</label>
                            <select id="selectTeam2" name="selectTeam2" class="form-select">
                                <?php
                                foreach ($teams as &$team) {
                                    $teamId = $team[0];
                                    $teamName = $team[1];
                                    echo "<option value=\"$teamId\">$teamName</option>";
                                }
                                ?>
                            </select>
                            <?php
                            if (isset($errors)) {
                                if (in_array("team2_empty", $errors))
                                    echo "<p class=\"text-danger\">* L'équipe ne peut pas être vide.</p>";
                                if (in_array("team2_illegal_value", $errors))
                                    echo "<p class=\"text-danger\">* L'équipe n'est pas valide.</p>";
                            }
                            ?>
                        </div>
                        <div class="mb-3">
                            <label for="inputDate">Date du match</label>
                            <input type="date" class="form-control" id="inputDate" name="inputDate">
                            <?php
                            if (isset($errors)) {
                                if (in_array("date_empty", $errors))
                                    echo "<p class=\"text-danger\">* La date ne peut pas être vide.</p>";
                                if (in_array("date_illegal_value", $errors))
                                    echo "<p class=\"text-danger\">* La date n'est pas valide.</p>";
                            }
                            ?>
                        </div>
                        <div class="mb-3">
                            <label for="inputAmount">Montant de la mise (EUR)</label>
                            <input type="number" class="form-control" id="inputAmount" name="inputAmount" value="10">
                            <?php
                            if (isset($errors)) {
                                if (in_array("amount_illegal_value", $errors))
                                    echo "<p class='text-danger'>* Le montant n'est pas valide.</p>";
                            }
                            ?>
                        </div>

                        <div class="accordion" id="accordionParis">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#score" aria-expanded="true" aria-controls="score">
                                        Score exact
                                    </button>
                                </h2>
                                <div id="score" class="accordion-collapse collapse show" data-bs-parent="#accordionParis">
                                    <div class="accordion-body">
                                        Remplissez les deux champs si vous souhaitez parier un score exact, ou un seul
                                        des deux si vous souhaitez faire un pari sur un seul club.<br><br>
                                        <div class="mb-3">
                                            <label for="inputScoreTeam1" class="form-label">Score équipe 1</label>
                                            <input type="number" id="inputScoreTeam1" name="inputScoreTeam1" class="form-control" placeholder="Ne rien parier...">
                                        </div>
                                        <div class="mb-3">
                                            <label for="inputScoreTeam2" class="form-label">Score équipe 2</label>
                                            <input type="number" id="inputScoreTeam2" name="inputScoreTeam2" class="form-control" placeholder="Ne rien parier...">
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="buttonScore">Parier</button>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#victoireClub" aria-expanded="false" aria-controls="victoireClub">
                                        Victoire d'un club
                                    </button>
                                </h2>
                                <div id="victoireClub" class="accordion-collapse collapse" data-bs-parent="#accordionParis">
                                    <div class="accordion-body">
                                        Sélectionner un club sur lequel parier la victoire : <br><br>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="radioTeam" id="radioTeam1" value="1" checked>
                                                <label class="form-check-label" for="radioTeam1">
                                                    Equipe 1
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="radioTeam" id="radioTeam2" value="2">
                                                <label class="form-check-label" for="radioTeam2">
                                                    Equipe 2
                                                </label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="buttonVictoireClub">Parier</button>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#nbButsTotal" aria-expanded="false" aria-controls="nbButsTotal">
                                        Nombre de buts au total dans le match
                                    </button>
                                </h2>
                                <div id="nbButsTotal" class="accordion-collapse collapse" data-bs-parent="#accordionParis">
                                    <div class="accordion-body">
                                        Sélectionnez un nombre total de but mis pendant le match (toutes équipes confondues) :<br><br>
                                        <div class="mb-3">
                                            <label for="inputButsTotal" class="form-label">Nombre total de buts</label>
                                            <input type="number" id="inputButsTotal" name="inputButsTotal" class="form-control" value="0">
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="buttonButsTotal">Parier</button>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ecartButs" aria-expanded="false" aria-controls="ecartButs">
                                        Ecart de buts entre deux clubs
                                    </button>
                                </h2>
                                <div id="ecartButs" class="accordion-collapse collapse" data-bs-parent="#accordionParis">
                                    <div class="accordion-body">
                                        Pariez sur un écart entre les scores de chaque équipe :<br><br>
                                        <div class="mb-3">
                                            <label for="inputEcart" class="form-label">Ecart de buts</label>
                                            <input type="number" id="inputEcart" name="inputEcart" class="form-control" value="0">
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="buttonEcart">Parier</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="alert alert-danger" style="margin-top: 20px; margin-bottom: 0;">
                        Attention ! Une fois un pari lancé, vous ne pouvez plus l'annuler !
                    </div>
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
                                echo "En attente du match...";
                            }

                            echo "</div>";
                        }
                    }
                    ?>
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
                <div class="row"><a href="../statistiques.html" class="link-secondary">Statistiques</a></div>
                <div class="row"><a href="../predictions.html" class="link-secondary">Prédictions</a></div>
                <div class="row"><a href="#" class="link-secondary">A propos de Pariplus</a></div>
            </div>
            <div class="col-md-3">
                <div class="row"><a href="../account/" class="link-secondary">Mon compte</a></div>
            </div>
        </div>
    </div>
</div>
<img class="d-block d-sm-none" src="../assets/img/Wilhem%20Motors.jpg" style="bottom: 0; width: 100vw;">
<img class="d-block d-sm-none" src="../assets/img/Wilhem%20Motors.jpg" style="position: fixed; bottom: 0; width: 100vw;">
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</html>