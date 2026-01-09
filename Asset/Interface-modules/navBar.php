<div class="container-navbar">
  
  <div class="nav-handle">
    <i class="fa-solid fa-bars"></i>
  </div>

  <div class="nav-content">
    <?php

    foreach (glob("Asset/Interface-page/*.php") as $filename) {
      $file = fopen($filename, "r");

      if ($file) {
        $title = '';
        $icon = '';
        $ordre = '';

        $i = 0;
        
        while (($line = fgets($file)) !== false && $i < 9) {

          $line = trim($line);
          
          if (str_contains($line, 'Titre') && str_contains($line, ':')) {
            $title = trim(substr($line, strpos($line, ':') + 1));  
          } 
          elseif (str_contains($line, 'Icon') && str_contains($line, ':')) {
            $icon = trim(substr($line, strpos($line, ':') + 1));  
          } 
          elseif (str_contains($line, 'Ordre') && str_contains($line, ':')) {
            $ordre = trim(substr($line, strpos($line, ':') + 1));  
          }
          
          $i++;
          
        }
        fclose($file);

        $modules[] = array(
          'title' => $title,
          'icon' => $icon,
          'ordre' => $ordre,
          'file' => basename($filename)
        );      
      }      
    }

    usort($modules, function($a, $b) {
      return $a['ordre'] - $b['ordre'];
    });


    $currentPage = isset($_GET['page']) ? $_GET['page'] : 'pswGenerator.php';

    foreach ($modules as $module) {
      
      $activeClass = ($currentPage === $module['file']) ? 'active' : '';
      
      echo '<div class="module-icon ' . $activeClass . '" title="' . htmlspecialchars($module['title']) . '">';
      echo '<a href="?page=' . $module['file'] . '">';
      echo '<i class="' . $module['icon'] . '"></i>';
      echo '</a>';
      echo '</div>';
      
    }
    
    ?>
  </div>
  
</div>