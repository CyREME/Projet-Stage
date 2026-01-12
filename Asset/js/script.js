/**
 * FICHIER JAVASCRIPT PRINCIPAL
 * Chemin : Asset/js/script.js
 */

document.addEventListener('DOMContentLoaded', () => {
    // Initialisation du module de génération de mot de passe
    if(document.body.id === 'pswGenBody') {
        initPasswordGenerator();
    }
});

// --- MODULE GÉNÉRATEUR DE MOT DE PASSE ---
function initPasswordGenerator() {
    const pswInput = document.getElementById("pswInput");
    const pswGenerate = document.getElementById("pswGenerate");
    const pswLength = document.getElementById("pswLength");
    const pswCopy = document.getElementById("pswCopy");

    // Checkbox elements
    const pswHasMaj = document.getElementById("pswUppercase");
    const pswHasMin = document.getElementById("pswLowercase");
    const pswHasNum = document.getElementById("pswNumbers");
    const pswHasSymb = document.getElementById("pswSymbols");

    // Event Listeners
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

    // Fonctions internes au module
    function copyPsw() {
        navigator.clipboard.writeText(pswInput.value);

        // Petit effet visuel optionnel pour confirmer la copie
        const originalText = pswCopy.innerText;
        pswCopy.innerText = "Copié !";
        setTimeout(() => {
            pswCopy.innerText = originalText;
        }, 2000);
    }

    function refreshPsw(){
        let charset = "";

        const majuscule = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        const minuscule = "abcdefghijklmnopqrstuvwxyz";
        const chiffre = "0123456789";
        const symbole = "!@#$%*-_+=?";

        if (pswHasMaj.checked) charset += majuscule;
        if (pswHasMin.checked) charset += minuscule;
        if (pswHasNum.checked) charset += chiffre;
        if (pswHasSymb.checked) charset += symbole;

        // Sécurité : si rien n'est coché, on force les minuscules
        if (charset === ""){
            charset = minuscule;
            pswHasMin.checked = true;
        };

        let password = "";
        let passwordLength = pswLength.value;

        document.getElementById("pswLengthValue").textContent = passwordLength;

        for (let i = 0; i < passwordLength; i++){
            let randomIndex = Math.floor(Math.random() * charset.length);
            password += charset[randomIndex];
        }

        pswInput.value = password;

        // Vérification de la force du mot de passe
        // Note: Assure-toi que la librairie zxcvbn est bien chargée dans ton PHP
        if(typeof zxcvbn !== 'undefined') {
            pswChecker();
        } else {
            console.warn("La librairie zxcvbn n'est pas chargée.");
        }
    }

    function progressBar(score, taille){
        let bar = document.getElementById("pswStrength");
        // Ajustement de la largeur max pour éviter le dépassement
        let barWidth = Math.min((taille / 13) * 100, 100); 
        bar.style.width = barWidth + "%";

        // Nettoyage des classes
        bar.classList.remove("bar-faible", "bar-moyen", "bar-fort");

        if (score === 0 || score === 1){
            bar.classList.add("bar-faible");
        } else if (score === 2 || score === 3) {
            bar.classList.add("bar-moyen");
        } else if (score === 4) {
            bar.classList.add("bar-fort");
        }
    }

    function dotColorChange(score){
        let dot = document.querySelector(".dot-checker");

        // Nettoyage des classes
        dot.classList.remove("dot-faible", "dot-moyen", "dot-fort");

        if (score === 0 || score === 1){
            dot.classList.add("dot-faible");
        } else if (score === 2 || score === 3) {
            dot.classList.add("dot-moyen");
        } else if (score === 4) {
            dot.classList.add("dot-fort");
        }
    }

    function pswChecker(){
        let password = pswInput.value;
        // Appel à la librairie externe zxcvbn
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

    // Lancement au chargement
    progressBarLength();
    refreshPsw();
}