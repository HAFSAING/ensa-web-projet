<?php
session_start();

session_unset();

session_destroy();

header("Location: index.php");
exit();
//
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