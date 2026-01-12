<title>Outils</title>
<link rel="stylesheet" href="Asset/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="Asset/js/script.js"></script>



<?php
include("Asset/Interface-modules/navBar.php");


if (isset($_GET['page'])) {
  $page = $_GET['page'];
  include("Asset/Outils/$page");
} else {
  include("Asset/Outils/pswGenerator.php");
}


?>