<?php
// Démarrer la session
session_start();

// Détruire toutes les variables de session
session_unset();

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil
header("Location: index.php");
exit();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion - MediStatView</title>
</head>
<body>
    <p>Vous avez été déconnecté. Redirection vers la page d'accueil...</p>
    <p>Si vous n'êtes pas redirigé, <a href="index.php">cliquez ici</a>.</p>
</body>
</html>