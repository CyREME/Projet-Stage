<div class="container-navbar" id="mainNavbar">

  <div class="nav-handle" id="navHandle">
    <i class="fa-solid fa-bars"></i>
  </div>

  <div class="nav-content">
    <?php
    $modules = []; 

    // On scanne le dossier Interface-page comme configuré dans votre index
    foreach (glob("Assets/Outils/*.php") as $filename) {
      $file = fopen($filename, "r");

      if ($file) {
        $title = 'Sans titre'; 
        $icon = 'fa-solid fa-question';

        // --- COULEURS PAR DÉFAUT ---
        $bgMenu = '#2c3e50'; 
        $bgBox = 'rgba(255,255,255,0.1)';
        $iconColor = '#ecf0f1';
        $ordre = 99; 

        $i = 0;

        // Lecture des 20 premières lignes
        while (($line = fgets($file)) !== false && $i < 20) { 
          $line = trim($line);
          $lineLower = strtolower($line);

          if (str_contains($lineLower, 'titre') || str_contains($lineLower, 'title')) {
            if (str_contains($line, ':')) $title = trim(substr($line, strpos($line, ':') + 1));
          } 
          elseif (str_contains($lineLower, 'icon')) {
             if (str_contains($line, ':')) $icon = trim(substr($line, strpos($line, ':') + 1));
          }
          // --- RECUPERATION DES COULEURS ---
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

    // Tri
    usort($modules, function($a, $b) {
      return $a['ordre'] <=> $b['ordre'];
    });

    // Page active
    if (isset($_GET['page']) && !empty($_GET['page'])) {
        $currentPage = $_GET['page'];
    } else {
        $currentPage = !empty($modules) ? $modules[0]['file'] : '';
    }

    foreach ($modules as $module) {
      $isActive = ($currentPage === $module['file']);
      $activeClass = $isActive ? 'active' : '';

      // Injection des attributs data- pour le JS
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