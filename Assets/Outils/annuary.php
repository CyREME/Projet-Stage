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

// --- UPSERT ---
if (isset($_POST['create_contact'])) {
    $nom = strtoupper(trim($_POST['new_nom']));
    $prenom = trim($_POST['new_prenom']);

    if (!empty($nom) && !empty($prenom)) {
        $sql = 'INSERT INTO "Cotrans" 
                ("Nom", "Prenom", "Service", "Fonction", "NumInterne", "NumMobile", "NumFixe")
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON CONFLICT ("Nom", "Prenom") 
                DO UPDATE SET
                    "Service" = EXCLUDED."Service",
                    "Fonction" = EXCLUDED."Fonction",
                    "NumInterne" = EXCLUDED."NumInterne",
                    "NumMobile" = EXCLUDED."NumMobile",
                    "NumFixe" = EXCLUDED."NumFixe"';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nom, $prenom,
            $_POST['new_service'], $_POST['new_fonction'],
            $_POST['new_interne'], $_POST['new_mobile'], $_POST['new_fixe']
        ]);
        $message = "Contact enregistré avec succès !";
    }
}

// --- UPDATE ---
if (isset($_POST['update_id'])) {
  $sql = 'UPDATE "Cotrans" SET 
         "Service" = ?,
         "Fonction" = ?,
         "NumInterne" = ?,
         "NumMobile" = ?,
         "NumFixe" = ?
         WHERE "id" = ?';

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
      $_POST['service'],
      $_POST['fonction'],
      $_POST['num_interne'],
      $_POST['num_mobile'],
      $_POST['num_fixe'],
      $_POST['update_id']
  ]);

  $message = "Les modifications ont été enregistrées avec succès."; 
}

// --- IMPORT ---
if (isset($_POST['import'])) {

  #echo "Fichier importé avec succès.";
  
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
    
    #echo "<h3>Rapport du script Python :</h3>";
    #echo "<pre>" . $output . "</pre>";

} else {
    #echo "Erreur d'upload.";
  }
}

// --- DELETE ---
if (isset($_POST['delete_id'])) {
  $stmt = $pdo->prepare('DELETE FROM "Cotrans" WHERE "id" = ?');
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
    <button type="submit" class="btn-import" id="importBtn" name="import" style="display:none;">Importer</button>
  </form>

  <hr>
  
  <div class="search-bar">
    <input type="text" id="searchInput" placeholder="Rechercher un contact...">
    <button id="searchButton"><i class="fa-solid fa-magnifying-glass"></i></button>
    <div class="add-contact-section">
        <button id="btnOpenModal" class="btn-add-contact">
            <i class="fa-solid fa-user-plus"></i> Ajouter un contact
        </button>
    </div>
  </div>

  
  
  <hr>

  <div class="annuaire" id="annuaireList">
      <?php foreach ($contacts as $contact): ?>
        <div class='contact'>
          <h3><?= htmlspecialchars($contact['Nom']) . " " . htmlspecialchars($contact['Prenom']) ?></h3>
          <div class='contact-infos'>
            <form action="" method="post" class="form-update">
              <input type="hidden" name="update_id" value="<?= $contact['id'] ?>">
              <div class='contact-infos-group'>
                <div class='contact-info-details'>
                  <label>Service :</label>
                  <textarea name="service" rows="1"><?= htmlspecialchars($contact['Service']) ?></textarea>
                </div>
                <div class='contact-info-details'>
                  <label>Fonction :</label>
                  <textarea name="fonction" rows="1"><?= htmlspecialchars($contact['Fonction']) ?></textarea>
                </div>
                <div class='contact-info-details'>
                  <label>Interne :</label>
                  <textarea name="num_interne" class="num-interne" rows="1"><?= htmlspecialchars($contact['NumInterne']) ?></textarea>
                </div>
                <div class='contact-info-details'>
                  <label>Mobile :</label>
                  <textarea name="num_mobile" class="num-tel" rows="1"><?= htmlspecialchars($contact['NumMobile']) ?></textarea>
                </div>
                <div class='contact-info-details'>
                  <label>Fixe :</label>
                  <textarea name="num_fixe" class="num-tel" rows="1"><?= htmlspecialchars($contact['NumFixe']) ?></textarea>
                </div>
              </div>

              <div class="contact-modif">
                  <button type="submit" class="btn-modif-contact">Enregistrer</button>
                  <button type="submit" class="btn-supp-contact" name="delete_id" value="<?= $contact['id'] ?>"
                          onclick="return confirm('Supprimer ce contact ?');">
                      Supprimer
                  </button>
              </div>
            </form> 
           </div>
        </div>
      <?php endforeach; ?>
  </div>

  <div id="modalAddContact" class="modal-overlay">
      <div class="modal-box">
          <h3>Nouveau Contact</h3>
          <p>Veuillez renseigner l'identité de la personne.</p>
          
          <div class="modal-input-group">
              <label>Nom :</label>
              <input type="text" id="modalNom" placeholder="ex: DUPONT">
          </div>
          
          <div class="modal-input-group">
              <label>Prénom :</label>
              <input type="text" id="modalPrenom" placeholder="ex: Jean">
          </div>

          <div class="modal-buttons">
              <button id="modalCancel" class="btn-modal-cancel">Annuler</button>
              <button id="modalConfirm" class="btn-modal-confirm">Créer la fiche</button>
          </div>
      </div>
  </div>

</div>