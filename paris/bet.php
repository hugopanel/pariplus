<?php

session_start();

require_once '../db.php';
global $db;

$username = $_SESSION['username'];
$password = $_SESSION['password'];
$user = $db->query("SELECT * FROM users WHERE username = '$username' AND password = '$password';")->fetch_assoc();
if (!$user) {
    // User info is incorrect
    header('location: ../account/login/');
    die;
}

$user_id = $user['id']; // Get the user id

// Get user bet limit
$betLimit = $user['betlimit'];
$difference = $betLimit - $db->query("SELECT SUM(amount) FROM bets WHERE user = $user_id AND bet_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH);")->fetch_all()[0][0];

if ($difference > 0 && $betLimit !== "0") {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // User submitted the form

        $team1 = mysqli_real_escape_string($db, $_POST['selectTeam1']);
        $team2 = mysqli_real_escape_string($db, $_POST['selectTeam2']);
        $date = mysqli_real_escape_string($db, $_POST['inputDate']);
        $amount = mysqli_real_escape_string($db, $_POST['inputAmount']);

        if (empty($team1)) $errors[] = "team1_empty";
        if (empty($team2)) $errors[] = "team2_empty";
        if (empty($date)) $errors[] = "date_empty";
        if (empty($amount) || $amount < 0) $amount = 0;

        if (isset($errors)) return;

        if ($db->query("SELECT * FROM teams WHERE id = '$team1';")->num_rows == 0) $errors[] = "team1_illegal_value";
        if ($db->query("SELECT * FROM teams WHERE id = '$team2';")->num_rows == 0) $errors[] = "team2_illegal_value";
        if ($team1 == $team2) $errors[] = "teams_same";
        if (!DateTime::createFromFormat("Y-m-d", $date)) $errors[] = "date_illegal_value";

        if (isset($errors)) return;

        if (isset($_POST['buttonScore'])) {
            // Pari score exact 1 ou 2 équipes
            $score1 = mysqli_real_escape_string($db, $_POST['inputScoreTeam1']);
            $score2 = mysqli_real_escape_string($db, $_POST['inputScoreTeam2']);

            // TODO: Check if values are numbers

            $score1_empty = (empty($score1) && $score1 !== "0");
            $score2_empty = (empty($score2) && $score2 !== "0");

            if (!$score1_empty && !$score2_empty) {
                // Pari sur le score complet
                $db->query("INSERT INTO bets (user, type, team1, score1, team2, score2, match_date, bet_date, amount) VALUES ($user_id, 0, $team1, $score1, $team2, $score2, '$date', CURDATE(), $amount);");
            } else if (!$score1_empty && $score2_empty) {
                // Pari sur le score de l'équipe 1
                $db->query("INSERT INTO bets (user, type, team1, score1, team2, match_date, bet_date, amount) VALUES ($user_id, 2, $team1, $score1, $team2, '$date', CURDATE(), $amount);");
            } else if ($score1_empty && !$score2_empty) {
                // Pari sur le score de l'équipe 2
                $db->query("INSERT INTO bets (user, type, team1, team2, score2, match_date, bet_date, amount) VALUES ($user_id, 2, $team1, $team2, $score2, '$date', CURDATE(), $amount);");
            }
        } else if (isset($_POST['buttonVictoireClub'])) {
            // Pari sur la victoir d'un club
            $winner = mysqli_real_escape_string($db, $_POST['radioTeam']);

            if ($winner == "1") {
                $db->query("INSERT INTO bets (user, type, team1, score1, team2, match_date, bet_date, amount) VALUES ($user_id, 1, $team1, 1, $team2, '$date', CURDATE(), $amount);");
            } else if ($winner == "2") {
                $db->query("INSERT INTO bets (user, type, team1, team2, score2, match_date, bet_date, amount) VALUES ($user_id, 1, $team1, $team2, 1, '$date', CURDATE(), $amount);");
            }
        } else if (isset($_POST['buttonButsTotal'])) {
            // Pari sur le nombre total de buts pendant le match
            $butsTotal = mysqli_real_escape_string($db, $_POST['inputButsTotal']);

            $db->query("INSERT INTO bets (user, type, team1, score1, team2, match_date, bet_date, amount) VALUES ($user_id, 3, $team1, $butsTotal, $team2, '$date', CURDATE(), $amount);");
        } else if (isset($_POST['buttonEcart'])) {
            // Pari sur un écart de buts
            $ecart = mysqli_real_escape_string($db, $_POST['inputEcart']);

            $db->query("INSERT INTO bets (user, type, team1, score1, team2, match_date, bet_date, amount) VALUES ($user_id, 4, $team1, $ecart, $team2, '$date', CURDATE(), $amount);");
        }

        // Calculate difference again after new bet
        $difference = $betLimit - $db->query("SELECT SUM(amount) FROM bets WHERE user = $user_id AND bet_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH);")->fetch_all()[0][0];
    }
}
