<?php

require_once "../../db.php";
global $db;

if (isset($_POST['buttonAddMatch'])) {
    // User clicked on "Ajouter le match"

    // Escape strings
    $team1 = mysqli_real_escape_string($db, $_POST['selectTeam1']);
    $score1 = mysqli_real_escape_string($db, $_POST['inputScoreTeam1']);
    $team2 = mysqli_real_escape_string($db, $_POST['selectTeam2']);
    $score2 = mysqli_real_escape_string($db, $_POST['inputScoreTeam2']);
    $date = mysqli_real_escape_string($db, $_POST['inputDate']);

    // Check if anything is empty
    if (empty($team1)) $errors[] = "team1_empty";
    if (empty($score1)) $score1 = "0";
    if (empty($team2)) $errors[] = "team2_empty";
    if (empty($score2)) $score2 = "0";
    if (empty($date)) $errors[] = "date_empty";

    if (isset($errors)) return; // Stop this script in case of errors

    // Check if values are correct
    if (!is_numeric($score1)) $errors[] = "score1_illegal_value";
    if (!is_numeric($score2)) $errors[] = "score2_illegal_value";
    if ($db->query("SELECT * FROM teams WHERE id = '$team1';")->num_rows == 0) $errors[] = "team1_illegal_value";
    if ($db->query("SELECT * FROM teams WHERE id = '$team2';")->num_rows == 0) $errors[] = "team2_illegal_value";
    if ($team1 == $team2) $errors[] = "teams_same";
    if (!DateTime::createFromFormat("Y-m-d", $date)) $errors[] = "date_illegal_value";

    if (isset($errors)) return;

    if ($score1 < 0) $score1 = 0;
    if ($score2 < 0) $score2 = 0;

    // Insert the new match into the database
    $db->query("INSERT INTO matchs (team1, score1, team2, score2, date) VALUES ($team1, $score1, $team2, $score2, '$date');");
}
