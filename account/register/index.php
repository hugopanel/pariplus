<?php

require_once dirname(__DIR__) . '/server.php';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inscription - PariPlus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@100;400;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../pariplus.css">
  <link rel="stylesheet" href="../../style.css">
  <link rel="stylesheet" href="../authentication.css">
</head>
<body style="background-color: var(--pariplus_dark);">
<div class="navbar">
  <div class="container">
    <a class="navbar-brand" href="/">
      <img src="/assets/img/pariplus_logo_white.svg" alt="PariPlus" height="70" style="max-height: 70px;">
    </a>
  </div>
</div>
<div class="container">
<div class="row">
  <div class="col-sm-1 col-md-2 col-lg-3 d-none d-sm-block"></div>
  <div class="col-sm-10 col-md-8 col-lg-6">
    <div class="bloc bloc-content">
      <form action="index.php" method="post">
        <div class="mb-3">
          <h1>Inscription</h1>
          <?php
          if (isset($errors))
            if (in_array('query_error', $errors))
              echo '<p class="text-danger">* Une erreur s\'est produite lors de la création de votre compte. Veuillez réessayer plus tard.</p>';
          ?>
        </div>
        <div class="mb-3">
          <label for="inputUsername" class="form-label">Nom d'utilisateur</label>
          <input type="text" class="form-control" id="inputUsername" name="inputUsername">
          <?php
          if (isset($errors)) {
            if (in_array('username_empty', $errors))
              echo '<p class="text-danger">* Vous devez renseigner un nom d\'utilisateur.</p>';
            if (in_array('username_taken', $errors))
              echo '<p class="text-danger">* Ce nom d\'utilisateur est déjà pris.</p>';
          }
          ?>
        </div>
        <div class="mb-3">
          <label for="inputPassword" class="form-label">Mot de passe</label>
          <input type="password" class="form-control" id="inputPassword" name="inputPassword">
          <?php
          if (isset($errors))
            if (in_array('password_empty', $errors))
              echo '<p class="text-danger">* Vous devez renseigner un mot de passe</p>';
          ?>
        </div>
        <div class="mb-3">
          <label for="inputPasswordConfirmation" class="form-label">Confirmer mot de passe</label>
          <input type="password" class="form-control" id="inputPasswordConfirmation" name="inputPasswordConfirmation">
          <?php
          if (isset($errors)) {
            if (in_array('passwordConfirmation_empty', $errors))
              echo '<p class="text-danger">* Vous devez renseigner un mot de passe.</p>';
            if (in_array('passwords_noMatch', $errors))
              echo '<p class="text-danger">* Les mots de passe ne correspondent pas.</p>';
          }
          ?>
        </div>
        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" id="checkboxAcceptConditions" value="accept-conditions" name="checkboxAcceptConditions">
          <label class="form-check-label" for="checkboxAcceptConditions">J'accepte les <a href="#" target="_blank">conditions d'utilisation de Pariplus</a></label>
          <?php
          if (isset($errors))
            if (in_array('checkboxConditions_empty', $errors))
              echo '<p class="text-danger">* Vous devez accepter les conditions d\'utilisation.</p>';
          ?>
        </div>
        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" value="remember-me" id="checkboxRemember" name="checkboxRemember">
          <label class="form-check-label" for="checkboxRemember">Se souvenir de moi</label>
        </div>
        <button type="submit" class="btn btn-primary" name="buttonRegister">S'inscrire</button>
      </form>
      <div style="margin-top: 20px;">
        Déjà un compte ? <a href="../login">Connectez-vous</a> !
      </div>
    </div>
  </div>
  <div class="col-sm-1 col-md-2 col-lg-3 d-none d-sm-block"></div>
</div>
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</html>