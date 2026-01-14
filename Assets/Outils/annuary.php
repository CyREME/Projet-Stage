<?php

/*
  Title: Import Annuaire
  Icon: fa-solid fa-file-import
  Colors: #004d40, #b2dfdb, #00695c
  Ordre: 3
*/

include_once __DIR__ . '/../Fonction/Fonctions.php';


$pdo = connectDb();

$message = "";
$rapport_python = "";

if (isset($_POST['import'])) {

  echo "Fichier importé avec succès.";
  
  $templacement_temporaire = $_FILES['csv_file']['tmp_name'];

  $dossier_temp = __DIR__ . '/../Temp/';
  $destination = $dossier_temp . 'import.xlsx';

  if (!is_dir($dossier_temp)) {
    mkdir($dossier_temp, 0777, true);
  }
    
  if (move_uploaded_file($templacement_temporaire, $destination)) {
    $script_path = __DIR__ . '/../Python/fonctions.py';
    $commande = "python3 " . escapeshellarg($script_path) . " extractData 2>&1";
    $output = shell_exec($commande);
    $rapport_python = $output;

   echo "<h3>Rapport du script Python :</h3>";
    echo "<pre>" . $output . "</pre>";

} else {
    echo "Erreur d'upload.";
  }
}

if (isset($_POST['delete_id'])) {
  $stmt = $pdo->prepare("DELETE FROM Cotrans WHERE id = ?");
  $stmt->execute([$_POST['delete_id']]);
  $message = "L'entrée a été supprimée avec succès.";
}

$contacts = getData();
  
?>

<!-- HTML -->

<div id="annuaryBody">
  <form action="" class="form-drop-zone" method="post" enctype="multipart/form-data">
    <div class="drop-zone" id="dropZone">
      <span class="drop-zone__prompt">Glissez votre fichier Excel ici ou cliquez pour upload</span>
      <input type="file" name="csv_file" id="csv_file" class="drop-zone__input" accept=".xlsx">
    </div>
    <button type="submit" name="import">Importer</button>
  </form>

  <?php if($message): ?>
    <div class="alert"><?php echo $message; ?></div>
  <?php endif; ?>

  <?php if($rapport_python): ?>
    <div class="alert"><?php echo $message; ?></div>
  <?php endif; ?>

  <hr>

  <div class="annuaire">

    <?php

    foreach ($contacts as $contact) {
      echo "<div class='contact'>";
      echo "<h3>" . htmlspecialchars($contact['Nom']) . htmlspecialchars($contact['Prenom']) . "</h3>";

      echo "<div class='contact-info'>";
      
      if (!empty($contact['Service'])){
        echo "<p>Service: " . htmlspecialchars($contact['Service']) . "</p>";
      }

      if (!empty($contact['Fonction'])){
        echo "<p>Fonction: " . htmlspecialchars($contact['Fonction']) . "</p>";
      }

      if (!empty($contact['NumInterne'])){
        echo "<p>Numéro interne: " . htmlspecialchars($contact['NumInterne']) . "</p>";
      }
      
      if (!empty($contact['NumMobile'])){
        echo "<p>Téléphone Mobile: " . htmlspecialchars($contact['NumMobile']) . "</p>";
      }
      
      if (!empty($contact['NumFixe'])){
        echo "<p>Téléphone Fixe: " . htmlspecialchars($contact['NumFixe']) . "</p>";
      }
      
      echo "<form action='' method='post'>";
      echo "<input type='hidden' name='delete_id' value='" . $contact['id'] . "'>";
      echo "<button type='submit'>Supprimer</button>";
      echo "</form>";
      
      echo "</div>";
      echo "</div>";
    }
    
    ?>
    
  </div>
  
</div>