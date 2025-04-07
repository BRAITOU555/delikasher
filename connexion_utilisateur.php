<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion - Delikasher</title>
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
      max-width: 400px;
      margin: 3rem auto;
      background-color: var(--white);
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }

    h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: var(--black);
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    input[type="email"],
    input[type="password"] {
      padding: 0.8rem 1rem;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1rem;
      width: 100%;
    }

    .checkbox-group {
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .checkbox-group label {
      margin-top: 2px;
    }

    .actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.9rem;
      margin-top: -0.5rem;
    }

    .actions a {
      color: var(--green);
      text-decoration: none;
    }

    .actions a:hover {
      text-decoration: underline;
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
  </style>
</head>
<body>

<div class="container">
  <h2>Connexion à Delikasher</h2>

  <form action="traitement_connexion_utilisateur.php" method="POST">
    <input type="email" name="email" placeholder="Adresse email" required>

    <input type="password" name="mot_de_passe" id="mot_de_passe" placeholder="Mot de passe" required>

    <div class="checkbox-group">
      <input type="checkbox" onclick="togglePassword()">
      <label>Afficher le mot de passe</label>
    </div>

    <div class="actions">
      <div class="checkbox-group">
        <input type="checkbox" name="souvenir">
        <label>Se souvenir de moi</label>
      </div>
      <a href="mot_de_passe_oublie.php">Mot de passe oublié ?</a>
    </div>

    <button type="submit" class="submit-btn">Se connecter</button>
  </form>
</div>

<footer>
  &copy; 2025 Delikasher - Tous droits réservés
</footer>

<script>
function togglePassword() {
  var input = document.getElementById("mot_de_passe");
  input.type = input.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
