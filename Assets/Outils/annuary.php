<?php

/*
  Title: Import Annuaire
  Icon: fa-solid fa-file-import
  Colors: #004d40, #b2dfdb, #00695c
  Ordre: 3
*/


include_once __DIR__ . '/../Fonction/Fonctions.php';
$json_path = __DIR__ . '/../Json/contacts.json';


$message = "";
$messageType = "success";

if (isset($_SESSION['flash_message'])) {
  $message = $_SESSION['flash_message'];
  $messageType = $_SESSION['flash_message_type'] ?? 'success';

  unset($_SESSION['flash_message']);
  unset($_SESSION['flash_message_type']);
} 


// Redirection vers la même page
function redirectWithSuccess($msg, $type = 'success') {
  $_SESSION['flash_message'] = $msg;
  $_SESSION['flash_message_type'] = $type;
  
  header("Location: " . $_SERVER['REQUEST_URI']);
  exit();
}

// --- SEARCH ---
if (isset($_SESSION['search_auto'])) {
  $searchValue = $_SESSION['search_auto'];
  unset($_SESSION['search_auto']);
} elseif (isset($_POST['search'])) {
  $searchValue = $_POST['search'];
  
} else {
  $searchValue = "";
}


// --- INSERT ---
if (isset($_POST['create_contact'])) {
    $nom = strtoupper(trim($_POST['new_nom']));
    $prenom = trim($_POST['new_prenom']);

    if (!empty($nom) && !empty($prenom)) {
      $json_path = __DIR__ . '/../Json/contacts.json';

      $contacts = [];
      if (file_exists($json_path)) {
        $json_content = file_get_contents($json_path);
        $contacts = json_decode($json_content, true);
      } else {
        $contacts = [];
      }

      $new_id = 1;
      if (!empty($contacts)) {
        $ids = array_keys($contacts);
        $new_id = max($ids) + 1;
      }

      $newContact = [
        "id" => (string)$new_id,
        "nom" => $nom,
        "prenom" => $prenom,
        "service" => $_POST['new_service'] ?? "",
        "fonctions" => $_POST['new_fonction'] ?? "",
        "numInterne" => $_POST['new_num_interne'] ?? "",
        "numMobile" => $_POST['new_num_mobile'] ?? "",
        "numFixe" => $_POST['new_num_fixe'] ?? ""
      ];

      $contacts[$new_id] = $newContact;

      file_put_contents($json_path, json_encode($contacts, JSON_PRETTY_PRINT));

      $_SESSION['search_auto'] = $nom . " " . $prenom;
      
      redirectWithSuccess("Contact créé avec succès !", "success");
    } else {
      redirectWithSuccess("Erreur : Le nom et le prénom sont obligatoires.", "erreur");
    }
}


// --- DELETE ---
if (isset($_POST['delete_id'])) {

  $delete_id = $_POST['delete_id'];

  $json_path = __DIR__ . '/../Json/contacts.json';

  if (file_exists($json_path)) {
    $json_content = file_get_contents($json_path);
    $contacts = json_decode($json_content, true);

    unset($contacts[$delete_id]);
    file_put_contents($json_path, json_encode($contacts, JSON_PRETTY_PRINT));
  }

  redirectWithSuccess("Contact supprimé avec succès.", "success");
}

// --- DELETE ALL ---
if (isset($_POST['delete_all'])) {

  $json_path = __DIR__ . '/../Json/contacts.json';

  if (!file_exists($json_path)) {
    redirectWithSuccess("Aucun contact à supprimer.", "erreur");
  }

  file_put_contents($json_path, '{}');
  

}

// --- UPDATE ---
if (isset($_POST['update_id'])) {

  $update_id = $_POST['update_id'];

  $json_path = __DIR__ . '/../Json/contacts.json';

  if (file_exists($json_path)) {
    $json_content = file_get_contents($json_path);
    $contacts = json_decode($json_content, true);
  } else {
    $contacts = [];
  }

  if (isset($contacts[$update_id])) {
    $contacts[$update_id]['service'] = $_POST['service'];
    $contacts[$update_id]['fonctions'] = $_POST['fonction'];
    $contacts[$update_id]['numInterne'] = $_POST['num_interne'];
    $contacts[$update_id]['numMobile'] = $_POST['num_mobile'];
    $contacts[$update_id]['numFixe'] = $_POST['num_fixe'];

    file_put_contents($json_path, json_encode($contacts, JSON_PRETTY_PRINT));

    $_SESSION['search_auto'] = $contacts[$update_id]['nom'] . " " . $contacts[$update_id]['prenom'];
    redirectWithSuccess("Contact mis à jour avec succès !", "success");   
  } else {
    redirectWithSuccess("Erreur : Contact non trouvé.", "erreur");
  }
  

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
    $commande = "python3 " . escapeshellarg($script_path) . " " . escapeshellarg($destination) . " 2>&1";
    $output = shell_exec($commande);

    $output = shell_exec($commande);
    // Pour le debug python : var_dump($output); die();

    redirectWithSuccess("Fichier importé avec succès.", "success");

} else {
    redirectWithSuccess("Erreur lors de l'importation du fichier.", "erreur");
  }
}


if (file_exists($json_path)){
  $json_content = file_get_contents($json_path);
  $contacts = json_decode($json_content, true);
} elseif (file_exists($json_path) && empty($json_content)) {
  $contacts = [];
} else {
  $contacts = [];
}

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
    
    <input type="text" id="searchInput" placeholder="Rechercher un contact..." value="<?= htmlspecialchars($searchValue) ?>">
    
    <div class="add-contact-section">
        <button id="btnOpenModal" class="btn-add-contact">
            <i class="fa-solid fa-user-plus"></i> Ajouter un contact
        </button>
    </div>
    
    <div class="delete-all-contacts">
      <button id="btnOpenDeleteAll" class="btn-delete-all" type="button"><i class="fa-solid fa-trash"></i></button>
    </div>
  </div>


  
  
  <hr>

  <div class="annuaire" id="annuaireList">
      <?php foreach ($contacts as $contact): ?>
        <div class='contact'>
          <h3><?= htmlspecialchars($contact['nom']) . " " . htmlspecialchars($contact['prenom']) ?></h3>
          <div class='contact-infos'>
            <form action="" method="post" class="form-update">
              <input type="hidden" name="update_id" value="<?= $contact['id'] ?>">
              <div class='contact-infos-group'>
                <div class='contact-info-details'>
                  <label>Service :</label>
                  <textarea name="service" rows="1"><?= htmlspecialchars($contact['service']) ?></textarea>
                </div>
                <div class='contact-info-details'>
                  <label>Fonction :</label>
                  <textarea name="fonction" rows="1"><?= htmlspecialchars($contact['fonctions']) ?></textarea>
                </div>
                <div class='contact-info-details'>
                  <label>Interne :</label>
                  <textarea name="num_interne" class="num-interne" rows="1"><?= htmlspecialchars($contact['numInterne']) ?></textarea>
                </div>
                <div class='contact-info-details'>
                  <label>Mobile :</label>
                  <textarea name="num_mobile" class="num-tel" rows="1"><?= htmlspecialchars($contact['numMobile']) ?></textarea>
                </div>
                <div class='contact-info-details'>
                  <label>Fixe :</label>
                  <textarea name="num_fixe" class="num-tel" rows="1"><?= htmlspecialchars($contact['numFixe']) ?></textarea>
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

  <div id="modalDeleteAll" class="modal-overlay">
      <div class="modal-box">
          <h3>Attention !</h3>
          <p>Voulez-vous vraiment supprimer <strong>TOUS</strong> les contacts ?</p>
          <p>Cette action est irréversible.</p>

          <div class="modal-buttons">
              <button id="modalCancelDelete" class="btn-modal-cancel">Annuler</button>
              <button id="modalConfirmDelete" class="btn-modal-confirm">Tout Supprimer</button>
          </div>
      </div>
  </div>


  <!--- Partie pour les notifications --->
  <div class="notif-annuaire" id="notifAnnuaire">
    <?php if (!empty($message)): ?>
      <div class="notif-annuaire-message" id="notif-message" data-type="<?= htmlspecialchars($messageType)?>">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif;?>
  </div>
  
  
</div>