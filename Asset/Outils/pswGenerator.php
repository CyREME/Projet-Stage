<?php
/*
  Titre : Générateur de mot de passe
  Icon  : fa-solid fa-lock fa-2xl
  Colors: #37000b, #ffcdd2, #c62828
  Ordre : 1
*/
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>

<body id="pswGenBody">
  <div class="container">
    <h1>Générateur de mot de passe</h1>
    <div class="core-container">
      
      <div class="psw-container">
        <div class="psw-input">
          <input type="text" id="pswInput" readonly>
        </div>
        <div class="psw-btn-generate">
          <button id="pswGenerate"><i class="fa-duotone fa-solid fa-rotate fa-2xl" style="--fa-primary-color: #a23fa0; --fa-secondary-color: #ff0088;"></i></button>
        </div>
        <div class="psw-btn-copy">
          <button id="pswCopy">Copier</button>
        </div>
      </div>
    
      <div class="check-container">
        <div class="check-bar-bg">
          <div class="check-bar-fill" id="pswStrength"></div>
        </div>
        <div class="check-dot">
          <span class="dot-checker"></span>
        </div>
      </div>
      
      <div class="options-container">
        <div class="slider-container">
          <div class="slider-value">
            <p>Longueur : <span id="pswLengthValue"></span></p>
          </div>
          <div class="slider-length">
            <input type="range" min="1" max="50" class="slider" id="pswLength">
          </div>
        </div>
        
        <div class="checkbox-container">
          <label class="checkbox-label"> Choix des caractères</label>
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
</body>