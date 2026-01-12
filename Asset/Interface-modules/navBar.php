<style>
    /* --- 1. LE CONTENEUR PRINCIPAL (Barre complète) --- */
    #mainNavbar {
        transition: background-color 0.4s ease-in-out !important;
        /* CORRECTION ICI : On met la couleur rouge par défaut directement */
        background-color: #37000b; 
    }

    /* --- 2. LES BOUTONS (Carrés des outils) --- */
    .module-icon {
        transition: background-color 0.4s ease-in-out, transform 0.2s ease !important;
        background-color: rgba(255, 255, 255, 0.05);
        color: #bdc3c7;
        cursor: pointer;
    }

    /* --- 3. LES ICONES (Symboles FontAwesome) --- */
    .module-icon i {
        transition: color 0.4s ease-in-out !important;
    }

    /* --- 4. LE BOUTON HAMBURGER (Menu mobile) --- */
    #navHandle {
        transition: background-color 0.4s ease-in-out, border-color 0.4s ease !important;
        /* CORRECTION ICI : Fond rouge par défaut */
        background-color: #37000b; 
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* --- 5. L'ICONE DU HAMBURGER --- */
    #navHandle i {
        transition: color 0.4s ease-in-out !important;
        /* Optionnel : couleur par défaut de l'icone hamburger */
        color: #ffcdd2; 
    }

    /* --- EFFETS AU SURVOL --- */
    .module-icon.active {
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transform: scale(1.02);
    }

    .module-icon:hover {
        transform: translateY(-2px);
    }
</style>

<div class="container-navbar" id="mainNavbar">

  <div class="nav-handle" id="navHandle">
    <i class="fa-solid fa-bars"></i>
  </div>

  <div class="nav-content">
    <?php
    $modules = []; 

    foreach (glob("Asset/Outils/*.php") as $filename) {
      $file = fopen($filename, "r");

      if ($file) {
        $title = 'Sans titre'; 
        $icon = 'fa-solid fa-question';

        // --- CORRECTION PHP : DÉFAUTS MIS À JOUR ---
        // Si un fichier n'a pas de config, il prendra ces couleurs
        $bgMenu = '#37000b'; 
        $bgBox = '#ffcdd2';
        $iconColor = '#c62828';
        $ordre = 99; 

        $i = 0;

        while (($line = fgets($file)) !== false && $i < 20) { 
          $line = trim($line);
          $lineLower = strtolower($line);

          if (str_contains($lineLower, 'title') || str_contains($lineLower, 'titre')) {
            if (str_contains($line, ':')) $title = trim(substr($line, strpos($line, ':') + 1));
          } 
          elseif (str_contains($lineLower, 'icon')) {
             if (str_contains($line, ':')) $icon = trim(substr($line, strpos($line, ':') + 1));
          }
          elseif (str_contains($lineLower, 'colors') || str_contains($lineLower, 'couleurs')) {
             if (str_contains($line, ':')) {
               $rawColors = trim(substr($line, strpos($line, ':') + 1));
               $rawColors = str_replace(['[', ']'], '', $rawColors);
               $colorArray = explode(',', $rawColors);

               if(isset($colorArray[0])) $bgMenu = trim($colorArray[0]);
               if(isset($colorArray[1])) $bgBox = trim($colorArray[1]);
               if(isset($colorArray[2])) $iconColor = trim($colorArray[2]);
             }
          } 
          elseif (str_contains($lineLower, 'ordre') || str_contains($lineLower, 'order')) {
             if (str_contains($line, ':')) {
               $val = trim(substr($line, strpos($line, ':') + 1));
               $ordre = (int)$val; 
             }
          }
          $i++;
        }
        fclose($file);

        $modules[] = [
          'title' => $title,
          'icon' => $icon,
          'bg_menu' => $bgMenu,
          'bg_box' => $bgBox,
          'color_icon' => $iconColor,
          'ordre' => $ordre,
          'file' => basename($filename)
        ];      
      }      
    }

    // Tri par ordre
    usort($modules, function($a, $b) {
      return $a['ordre'] <=> $b['ordre'];
    });

    // Définition de la page active
    if (isset($_GET['page']) && !empty($_GET['page'])) {
        $currentPage = $_GET['page'];
    } else {
        $currentPage = !empty($modules) ? $modules[0]['file'] : '';
    }

    foreach ($modules as $module) {
      $isActive = ($currentPage === $module['file']);
      $activeClass = $isActive ? 'active' : '';

      echo '<div class="module-icon ' . $activeClass . '" 
                 title="' . htmlspecialchars($module['title']) . '"
                 data-bg-menu="' . htmlspecialchars($module['bg_menu']) . '"
                 data-bg-box="' . htmlspecialchars($module['bg_box']) . '"
                 data-color-icon="' . htmlspecialchars($module['color_icon']) . '"
                 data-active="' . ($isActive ? 'true' : 'false') . '">';

      echo '  <a href="?page=' . $module['file'] . '">';
      $styleIcon = $isActive ? 'style="color:'.$module['color_icon'].'"' : '';
      echo '    <i class="' . $module['icon'] . '" '.$styleIcon.'></i>';
      echo '  </a>';
      echo '</div>';
    }
    ?>
  </div>
</div>