<?php

require_once "../server.php";
global $db;

require_once "add_match.php";

session_start();

// Check if user is connected
if (!isset($_SESSION['username'])) {
    if (isset($_COOKIE['pput'])) {
        if (!logToken()) {
            header('location: ../logout.php');
        }
    } else {
        header('location: ../login/');
    }
}

// Get user info
$username = $_SESSION['username'];
$password = $_SESSION['password'];
$result = $db->query("SELECT id, isAdmin FROM users WHERE username = '$username' AND password = '$password';")->fetch_assoc();
if (!$result) {
    // User info is incorrect
    header('location: ../login/');
    die;
}

$user_id = $result['id'];
$isAdmin = $result['isAdmin'];

if (!$isAdmin) {
    header('location: ../');
    die;
}

// Check if we wanted to remove a match
if (isset($_GET['remove'])) {
    $id_to_remove = mysqli_real_escape_string($db, $_GET['remove']);
    $db->query("DELETE FROM matchs WHERE id = '$id_to_remove';");

    // TODO: Check if the ID exists?
}

// Get list of teams
$teams = $db->query("SELECT * FROM teams;")->fetch_all();

// Get list of matches
$matches = $db->query("SELECT * FROM matchs ORDER BY date DESC;")->fetch_all();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administration - PariPlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@100;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../pariplus.css">
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
<div class="navbar navbar-expand-md navbar-dark fixed-top" style="background-color: var(--pariplus_red); padding: 0 10px 0 10px;">
    <div class="container">
        <a class="navbar-brand" href="../../">
            <img src="../../assets/img/pariplus_logo_white.svg" alt="PariPlus" height="70" style="max-height: 70px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" style="border: none;">
            <i class="bi bi-list" style="display: inline-block; font-size: 1.5em; color: white;"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav">
                <a class="nav-link" href="../">Mon compte</a>
                <a class="nav-link" href="../logout.php">Déconnexion</a>
            </div>
        </div>
    </div>
</div>
<div class="container" style="margin-top: 80px;">
    <div class="row">
        <div class="col-lg-8">
            <div class="bloc">
                <div class="bloc-content">
                    <h4>Espace administrateur</h4>
                    <form method="POST" action="index.php">
                        <legend>Ajouter un match</legend>
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
                            <label for="inputScoreTeam1" class="form-label">Score équipe 1</label>
                            <input type="number" id="inputScoreTeam1" name="inputScoreTeam1" class="form-control" value="0">
                            <?php
                            if (isset($errors))
                                if (in_array("score1_illegal_value", $errors))
                                    echo "<p class=\"text-danger\">* Le score n'est pas valide.</p>";
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
                            <label for="inputScoreTeam2" class="form-label">Score équipe 2</label>
                            <input type="number" id="inputScoreTeam2" name="inputScoreTeam2" class="form-control" value="0">
                            <?php
                            if (isset($errors))
                                if (in_array("score2_illegal_value", $errors))
                                    echo "<p class=\"text-danger\">* Le score n'est pas valide.</p>";
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
                        <button type="submit" class="btn btn-primary" name="buttonAddMatch">Ajouter le match</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="bloc">
                <div class="bloc-content">
                    <legend>Liste des matchs</legend>
                    <br>
                    <?php
                    if (count($matches) == 0)
                        echo "Aucun match enregistré.";
                    else
                        foreach ($matches as &$match) {
                            $match_id = $match[0];
                            $match_team1 = $match[1];
                            $match_team1 = $db->query("SELECT name FROM teams WHERE id = $match_team1;")->fetch_assoc()['name'];
                            $match_score1 = $match[2];
                            $match_team2 = $match[3];
                            $match_team2 = $db->query("SELECT name FROM teams WHERE id = $match_team2;")->fetch_assoc()['name'];
                            $match_score2 = $match[4];
                            $match_date = $match[5];

                            echo "
                            <br>
                            <div class=\"card\">
                                <div class=\"card-body\">
                                    <h5 class=\"card-title\">$match_team1 - $match_team2</h5>
                                    <h6 class=\"card-subtitle mb-2 text-body-secondary\">$match_date</h6>
                                    <p class=\"card-text\">$match_team1 : $match_score1<br>$match_team2 : $match_score2</p>
                                    <a href=\"index.php?remove=$match_id\" class=\"card-link\">Supprimer</a>
                                </div>
                            </div>
                            ";
                        }

                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="footer text-secondary" style="background-color: var(--pariplus_dark);">
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <img src="../../assets/img/pariplus_logo_white.svg" alt="PariPlus" height="70">
            </div>
            <div class="col-md-4">
                Pariplus est un site de pari en ligne et de prédiction de rencontres et de résultats responsable.
            </div>
            <div class="col-md-3">
                <div class="row"><a href="../../" class="link-secondary">Accueil</a></div>
                <div class="row"><a href="../../paris/" class="link-secondary">Parier</a></div>
                <div class="row"><a href="../../statistiques.html" class="link-secondary">Statistiques</a></div>
                <div class="row"><a href="../../predictions.html" class="link-secondary">Prédictions</a></div>
                <div class="row"><a href="#" class="link-secondary">A propos de Pariplus</a></div>
            </div>
            <div class="col-md-3">
                <div class="row"><a href="../" class="link-secondary">Mon compte</a></div>
            </div>
        </div>
    </div>
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</html>