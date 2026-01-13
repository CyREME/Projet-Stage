<?php

/*
  Titre : Testeur de mot de passe
  Icon  : fa-solid fa-shield-halved
  Colors: #0d47a1, #bbdefb, #1565c0
  Ordre : 2
*/

?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>

<body id="pswTestBody">
  
  <div class="container">
  
    <h1>Testeur de mot de passe</h1>
    
    <div class="core-container">
      
      <div class="psw-container">
        <div class="psw-input">
          <input type="text" id="pswInput">
        </div>
  
        <div class="psw-btn-copy">
          <button id="pswPaste">Coller</button>
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
        
        <div class="checkbox-container">
  
          <p id="pswCrackTime"></p>
            
          </div>
        </div>
      </div>
      
    </div>
  </div>
  
</body>
