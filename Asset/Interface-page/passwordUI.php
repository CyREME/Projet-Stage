<?php
?>

<link rel="stylesheet" href="../../Asset/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>

<div class="container">
  
  <div class="core-container">
    
    <div class="psw-container">
      <div class="psw-input">
        <input type="text" id="pswInput" placeholder="Mot de passe">
      </div>

      <div class="psw-btn-generate">
        <button id="pswGenerate"><i class="fa-duotone fa-solid fa-rotate"></i></button>
      </div>

      <div class="pws-btn-copy">
        <button id="pswCopy">Copier</button>
      </div>
      
    </div>
  
    <div class="check-container">
      <p>Check</p>
    </div>
    
    <div class="options-container">
      <div class="slider-container">
        <div class="slider-value">
          <p>Longueur : <span id="pswLengthValue">50</span></p>
        </div>
        
        <div class="slider-length">
          <input type="range" min="1" max="50" value="50" class="slider" id="pswLength">
        </div>
      </div>
      
      <div class="checkbox-container">

        <label>Choix des caractères</label>

        <div class="list-checkbox">
          <div class="psw-checkbox">
            <input type="checkbox" id="pswUppercase" name="pswUppercase" checked>
            <label for="pswUppercase">Majuscules</label>          
          </div>
  
          <div class="psw-checkbox">
            <input type="checkbox" id="pswLowercase" name="pswLowercase" checked>
            <label for="pswLowercase">Minuscules</label>        
          </div>
  
          <div class="psw-checkbox">
            <input type="checkbox" id="pswNumbers" name="pswNumbers" checked>
            <label for="pswNumbers">Nombres</label>        
          </div>
  
          <div class="psw-checkbox">
            <input type="checkbox" id="pswSymbols" name="pswSymbols" checked>
            <label for="pswSymbols">Symboles</label>         
          </div>
        </div>
      </div>
    </div>
    
  </div>
</div>