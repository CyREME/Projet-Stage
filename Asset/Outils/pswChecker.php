<?php

/*
  Titre : Testeur de mot de passe
  Icon  : fa-solid fa-shield-halved
  Colors: #0d47a1, #bbdefb, #1565c0
  Ordre : 2
*/

?>

<link rel="stylesheet" href="../../Asset/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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






<script>
  
  const pswInput = document.getElementById("pswInput");
  const pswGenerate = document.getElementById("pswGenerate");
  const pswLength = document.getElementById("pswLength");
  const pswHasMaj = document.getElementById("pswUppercase");
  const pswHasMin = document.getElementById("pswLowercase");
  const pswHasNum = document.getElementById("pswNumbers");
  const pswHasSymb = document.getElementById("pswSymbols");

  pswGenerate.addEventListener("click", refreshPsw);
  pswLength.addEventListener("input", function(){
    progressBarLength();
    refreshPsw();
  });
  pswHasMaj.addEventListener("change", refreshPsw);
  pswHasMin.addEventListener("change", refreshPsw);
  pswHasNum.addEventListener("change", refreshPsw);
  pswHasSymb.addEventListener("change", refreshPsw);
  pswCopy.addEventListener("click", copyPsw);



  function copyPsw() {
    navigator.clipboard.writeText(pswInput.value);
  }
  

  
  
  function refreshPsw(){
    let charset = "";

    const majuscule = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const minuscule = "abcdefghijklmnopqrstuvwxyz";
    const chiffre = "0123456789";
    const symbole = "!@#$%*-_+=?";

    if (document.getElementById("pswUppercase").checked) charset += majuscule;
    if (document.getElementById("pswLowercase").checked) charset += minuscule;
    if (document.getElementById("pswNumbers").checked) charset += chiffre;
    if (document.getElementById("pswSymbols").checked) charset += symbole;

    if (charset === ""){
      charset = minuscule;
      document.getElementById("pswLowercase").checked = true;
    };

    let password = "";
    let passwordLength = pswLength.value;

    document.getElementById("pswLengthValue").textContent = passwordLength;

    

    for (let i = 0; i < passwordLength; i++){
      let randomIndex = Math.floor(Math.random() * charset.length);
      password += charset[randomIndex];
    }

    pswInput.value = password;

    pswChecker();
  }

  function progressBar(score, taille){
    let bar = document.getElementById("pswStrength");
    let barWidth = (taille / 13) * 100;
    bar.style.width = barWidth + "%";

    if (score === 0 || score === 1){
      bar.classList.remove("bar-moyen", "bar-fort");
      bar.classList.add("bar-faible");
    } else if (score === 2 || score === 3) {
      bar.classList.remove("bar-faible", "bar-fort");
      bar.classList.add("bar-moyen");
    } else if (score === 4) {
      bar.classList.remove("bar-moyen", "bar-faible");
      bar.classList.add("bar-fort");
    }
    
  }
  
  function dotColorChange(score){
    let dot = document.querySelector(".dot-checker");

    if (score === 0 || score === 1){
      dot.classList.remove("dot-moyen", "dot-fort");
      dot.classList.add("dot-faible");
    } else if (score === 2 || score === 3) {
      dot.classList.remove("dot-faible", "dot-fort");
      dot.classList.add("dot-moyen");
    } else if (score === 4) {
      dot.classList.remove("dot-moyen", "dot-faible");
      dot.classList.add("dot-fort");
    }
    
  }

  function pswChecker(){
    let password = pswInput.value;
    let result = zxcvbn(password);

    dotColorChange(result.score);
    progressBar(result.score, result.guesses_log10);
  }

  function progressBarLength(){
    let bar = document.getElementById("pswLength");

    let value = bar.value;
    let min = bar.min;
    let max = bar.max;

    let percentage = ((value - min) / (max - min)) * 100;

    bar.style.background = `linear-gradient(to right, #8e44ad ${percentage}%, #edf2f7 ${percentage}%)`;
  }



  progressBarLength();
  refreshPsw();
</script>