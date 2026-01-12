<?php

/*
  Title: Import Annuaire
  Icon: fa-solid fa-file-import
  Colors: #004d40, #b2dfdb, #00695c
  Ordre: 3
*/

$results = null;
$error = null;

// On ne lance le traitement QUE si un formulaire a été soumis (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {

    // 1. Vérification d'erreur d'upload
    if ($_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {

        $tempPath = $_FILES['pdf_file']['tmp_name'];

        // 2. CORRECTION DU CHEMIN PYTHON
        $pythonScriptPath = __DIR__ . '/../Python/parser.py';

        // --- FIX : DÉFINITION DU CHEMIN DES LIBRAIRIES UTILISATEUR ---
        // Sur Replit, pip install --user met les fichiers ici :
        $libPath = "/home/runner/.local/lib/python3.10/site-packages";

        // 3. Construction de la commande AVEC LE PYTHONPATH
        // On injecte la variable d'environnement PYTHONPATH juste avant la commande
        $command = "PYTHONPATH=" . $libPath . " python3 " . escapeshellarg($pythonScriptPath) . " " . escapeshellarg($tempPath) . " 2>&1";

        // 4. Exécution
        $output = shell_exec($command);

        // 5. Décodage
        $data = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Si le JSON est invalide, c'est souvent que Python a crashé et renvoyé du texte brut
            $error = "Erreur brute Python : " . htmlspecialchars($output);
        } elseif (isset($data['error'])) {
            $error = "Erreur du script : " . htmlspecialchars($data['error']);
        } else {
            $results = $data;
        }

    } else {
        $error = "Erreur lors de l'upload du fichier.";
    }
}
?>

<div class="core-container">
    <h1>Extraction d'Annuaire (Python)</h1>

    <form action="" method="post" enctype="multipart/form-data" class="upload-box" style="text-align:center; padding: 40px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">

        <div style="margin-bottom: 20px;">
            <label for="pdfInput" style="cursor: pointer; display: inline-block; padding: 20px; border: 2px dashed #b2dfdb; border-radius: 10px; width: 100%; box-sizing: border-box;">
                <i class="fa-solid fa-cloud-arrow-up fa-3x" style="color: #00695c;"></i>
                <br><br>
                <span style="color: #555;">Cliquez pour choisir un PDF</span>
            </label>
            <input type="file" name="pdf_file" id="pdfInput" accept=".pdf" style="display: none;" onchange="document.querySelector('span').innerText = this.files[0].name">
        </div>

        <button type="submit" style="background-color: #00695c; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-size: 1rem;">
            <i class="fa-solid fa-gears"></i> Lancer l'extraction
        </button>
    </form>

    <?php if ($error): ?>
        <div style="margin-top: 20px; padding: 15px; background-color: #ffebee; color: #c62828; border-radius: 8px; border: 1px solid #ffcdd2;">
            <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($results): ?>
        <div class="results-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 40px;">
            <?php foreach ($results as $contact): ?>
                <div class="contact-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 4px solid #00695c;">
                    <h3 style="margin: 0 0 10px 0; color: #2c3e50; font-size: 1.1rem;">
                        <i class="fa-solid fa-user" style="color: #b2dfdb;"></i> 
                        <?php echo htmlspecialchars($contact['nom']); ?>
                    </h3>
                    <div style="background: #f4f7f6; padding: 10px; border-radius: 8px;">
                        <i class="fa-solid fa-phone" style="color: #00695c;"></i> 
                        <strong style="color: #333;"><?php echo htmlspecialchars($contact['numeros']); ?></strong>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>