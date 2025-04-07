<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription - Delikasher</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root {
      --black: #1e1e1e;
      --green: #06c167;
      --white: #ffffff;
      --gray-dark: #333333;
      --gray-light: #eeeeee;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: var(--gray-light);
      color: var(--black);
    }

    .container {
      max-width: 450px;
      margin: 3rem auto;
      background-color: var(--white);
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }

    h2 {
      text-align: center;
      color: var(--black);
      margin-bottom: 1rem;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    select {
      padding: 0.8rem 1rem;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1rem;
      width: 100%;
    }

    .row {
      display: flex;
      gap: 1rem;
    }

    .row select, .row input {
      flex: 1;
    }

    .radio-group {
      display: flex;
      gap: 1.5rem;
      align-items: center;
      margin-top: -0.5rem;
    }

    .radio-group label {
      font-weight: normal;
    }

    .checkbox-group {
      font-size: 0.85rem;
      display: flex;
      gap: 0.5rem;
      align-items: flex-start;
    }

    .checkbox-group input {
      margin-top: 0.2rem;
    }

    .submit-btn {
      background-color: var(--green);
      color: var(--white);
      font-weight: bold;
      border: none;
      padding: 0.9rem;
      border-radius: 30px;
      cursor: pointer;
      transition: background-color 0.3s;
      font-size: 1rem;
    }

    .submit-btn:hover {
      background-color: #05a556;
    }

    footer {
      text-align: center;
      font-size: 0.85rem;
      color: #777;
      margin-top: 2rem;
    }

    @media (max-width: 500px) {
      .row {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Créer un compte Delikasher</h2>

  <form action="traitement_inscription.php" method="POST">

    <div class="radio-group">
      <label><input type="radio" name="civilite" value="Mme" required> Mme</label>
      <label><input type="radio" name="civilite" value="M."> M.</label>
    </div>

    <input type="text" name="nom" placeholder="Nom*" required>
    <input type="text" name="prenom" placeholder="Prénom*" required>

    <div class="row">
      <input type="text" name="jour" placeholder="Jour*" required>
      <select name="mois" required>
        <option value="">Mois</option>
        <?php
          $mois = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
          foreach ($mois as $m) {
            echo "<option value=\"$m\">$m</option>";
          }
        ?>
      </select>
      <input type="text" name="annee" placeholder="Année*" required>
    </div>

    <input type="email" name="email" placeholder="Email*" required>
    <input type="password" name="mot_de_passe" placeholder="Mot de passe (8-20 caractères, 1 chiffre, 1 maj)*" required>
    <input type="text" name="parrainage" placeholder="Code de parrainage (optionnel)">

    <div class="checkbox-group">
      <input type="checkbox" name="cgu" required>
      <label>J'ai lu et accepté les <a href="#">CGU</a>, la <a href="#">politique de confidentialité</a> et les <a href="#">cookies</a>.</label>
    </div>

    <div class="checkbox-group">
      <input type="checkbox" name="pubs">
      <label>Je souhaite recevoir les bons plans de Delikasher par email.</label>
    </div>

    <button type="submit" class="submit-btn">Inscription</button>
  </form>
</div>

<footer>
  &copy; 2025 Delikasher - Tous droits réservés
</footer>

</body>
</html>
