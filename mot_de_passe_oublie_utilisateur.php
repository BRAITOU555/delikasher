<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mot de passe oublié - Delikasher</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root {
      --black: #1e1e1e;
      --green: #06c167;
      --white: #ffffff;
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
      margin: 4rem auto;
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

    input[type="email"] {
      padding: 0.8rem 1rem;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1rem;
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

    .message {
      margin-top: 1rem;
      text-align: center;
      font-size: 0.95rem;
      color: #333;
    }

    .back-link {
      margin-top: 2rem;
      text-align: center;
    }

    .back-link a {
      color: var(--green);
      text-decoration: none;
    }

    .back-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Mot de passe oublié</h2>

  <form method="POST" action="">
    <input type="email" name="email" placeholder="Entrez votre adresse email" required>
    <button type="submit" class="submit-btn">Envoyer le lien</button>
  </form>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
      // Simulation du traitement
      echo "<div class='message'>📩 Si cet email existe, un lien de réinitialisation a été envoyé.</div>";
  }
  ?>

  <div class="back-link">
    <a href="connexion_utilisateur.php">← Retour à la connexion</a>
  </div>
</div>

</body>
</html>
