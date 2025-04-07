<?php
session_start();
session_unset();
session_destroy();

// Redirection vers la page de connexion
header("Location: connexion-restaurant.html");
exit();
