<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outils</title>

    <link rel="stylesheet" href="Asset/css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php
// Inclusion de la barre de navigation
include("Asset/Interface-modules/navBar.php");

// Gestion du routage des pages
if (isset($_GET['page'])) {
    $page = basename($_GET['page']); // Sécurité
    $target = "Asset/Outils/$page";

    if(file_exists($target)){
        include($target);
    } else {
        // Page 404 simple
        echo "<div class='container'><h1>Page introuvable</h1></div>";
    }
} else {
    // Page par défaut
    include("Asset/Outils/pswGenerator.php");
}
?>

<script src="Asset/js/script.js"></script>

</body>
</html>