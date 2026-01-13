/**
 * FICHIER JAVASCRIPT PRINCIPAL
 * Chemin : Asset/js/script.js
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Barre de navigation (partout)
    initNavBar();

    // 2. Détection de la page active via l'ID du body
    const bodyId = document.body.id;

    if (bodyId === 'pswGenBody') {
        initPasswordGenerator();
    } else if (bodyId === 'pswTestBody') {
        initPasswordTester();
    }
});

// =========================================================================
// MODULE 1 : BARRE DE NAVIGATION
// =========================================================================
function initNavBar() {
    const navbar = document.getElementById('mainNavbar');
    const navHandle = document.getElementById('navHandle');
    const navHandleIcon = navHandle ? navHandle.querySelector('i') : null;
    const icons = document.querySelectorAll('.module-icon');

    // État par défaut
    let defaultState = { bgMenu: '#2c3e50', bgBox: '#ffffff', colorIcon: '#333' };

    function applyState(targetElement, bgMenu, bgBox, colorIcon) {
        if (navbar) navbar.style.backgroundColor = bgMenu;
        if (navHandle) navHandle.style.backgroundColor = bgMenu;
        if (navHandleIcon) navHandleIcon.style.color = bgBox;

        icons.forEach(el => {
            if (el !== targetElement && el.getAttribute('data-active') !== 'true') {
                el.style.backgroundColor = 'rgba(255,255,255,0.05)';
                el.querySelector('i').style.color = '#bdc3c7';
            }
        });

        if (targetElement) {
            targetElement.style.backgroundColor = bgBox;
            targetElement.querySelector('i').style.color = colorIcon;
        }
    }

    const activeEl = document.querySelector('.module-icon[data-active="true"]');
    if (activeEl) {
        defaultState.bgMenu = activeEl.getAttribute('data-bg-menu');
        defaultState.bgBox = activeEl.getAttribute('data-bg-box');
        defaultState.colorIcon = activeEl.getAttribute('data-color-icon');
        applyState(activeEl, defaultState.bgMenu, defaultState.bgBox, defaultState.colorIcon);
    }

    icons.forEach(icon => {
        icon.addEventListener('mouseenter', () => {
            applyState(icon, icon.getAttribute('data-bg-menu'), icon.getAttribute('data-bg-box'), icon.getAttribute('data-color-icon'));
        });
        icon.addEventListener('mouseleave', () => {
            applyState(activeEl, defaultState.bgMenu, defaultState.bgBox, defaultState.colorIcon);
        });
    });
}

// =========================================================================
// FONCTIONS UTILITAIRES PARTAGÉES (Zxcvbn & Temps)
// =========================================================================
function convertSecondsToFrench(seconds) {
        if (seconds < 1) return "Moins d'une seconde";
        
        if (seconds < 60) return Math.round(seconds) + " secondes";
        
        let minutes = seconds / 60;
        if (minutes < 60) return Math.round(minutes) + " minute(s)";
        
        let hours = minutes / 60;
        if (hours < 24) return Math.round(hours) + " heure(s)";
        
        let days = hours / 24;
        if (days < 30) return Math.round(days) + " jour(s)";
        
        // --- LE BLOC MANQUANT (MOIS) ---
        let months = days / 30;
        if (months < 12) return Math.round(months) + " mois";
        
        let years = days / 365;
        if (years >= 100) return "Plus d'un siècle";
        
        return Math.round(years) + " an(s)";
}

function updateUiStrength(password, crackTimeDisplayId = null) {
    const bar = document.getElementById("pswStrength");
    const dot = document.querySelector(".dot-checker");
    const timeDisplay = crackTimeDisplayId ? document.getElementById(crackTimeDisplayId) : null;

    if (!password) {
        if (bar) bar.style.width = "0%";
        if (dot) dot.className = "dot-checker"; 
        if (timeDisplay) timeDisplay.textContent = "";
        return;
    }

    // Si zxcvbn n'est pas encore chargé, on sort sans rien casser
    if (typeof zxcvbn === 'undefined') return;

    let result = zxcvbn(password);
    let score = result.score;

    let colorClass = "dot-faible"; 
    if (score >= 4) colorClass = "dot-fort";
    else if (score >= 2) colorClass = "dot-moyen";

    if (dot) dot.className = "dot-checker " + colorClass;
    if (bar) {
        bar.className = "check-bar-fill " + colorClass.replace('dot-', 'bar-');
        let width = Math.min((result.guesses_log10 / 14) * 100, 100);
        bar.style.width = width + "%";
    }

    if (timeDisplay && result.crack_times_seconds) {
        let seconds = result.crack_times_seconds.offline_slow_hashing_1e4_per_second;
        timeDisplay.innerHTML = "Temps estimé : <strong>" + convertSecondsToFrench(seconds) + "</strong>";
    }
}


// =========================================================================
// MODULE 2 : LE GÉNÉRATEUR (Page pswGenerator.php)
// =========================================================================
function initPasswordGenerator() {
    const pswInput = document.getElementById("pswInput");
    const pswGenerate = document.getElementById("pswGenerate");
    const pswLength = document.getElementById("pswLength");
    const pswCopy = document.getElementById("pswCopy");

    const checkboxes = {
        maj: document.getElementById("pswUppercase"),
        min: document.getElementById("pswLowercase"),
        num: document.getElementById("pswNumbers"),
        sym: document.getElementById("pswSymbols")
    };

    function refreshPsw() {
        if (!pswInput) return;
        let charset = "";
        const chars = {
            maj: "ABCDEFGHIJKLMNOPQRSTUVWXYZ", min: "abcdefghijklmnopqrstuvwxyz",
            num: "0123456789", sym: "!@#$%*-_+=?"
        };

        if (checkboxes.maj?.checked) charset += chars.maj;
        if (checkboxes.min?.checked) charset += chars.min;
        if (checkboxes.num?.checked) charset += chars.num;
        if (checkboxes.sym?.checked) charset += chars.sym;

        if (charset === "") { charset = chars.min; if (checkboxes.min) checkboxes.min.checked = true; }

        let password = "";
        let length = pswLength ? pswLength.value : 12;
        const lengthDisplay = document.getElementById("pswLengthValue");
        if (lengthDisplay) lengthDisplay.textContent = length;

        for (let i = 0; i < length; i++) {
            password += charset[Math.floor(Math.random() * charset.length)];
        }

        pswInput.value = password;
        updateUiStrength(password);
    }

    function updateSliderColor() {
        if (!pswLength) return;
        let val = ((pswLength.value - pswLength.min) / (pswLength.max - pswLength.min)) * 100;
        pswLength.style.background = `linear-gradient(to right, #8e44ad ${val}%, #edf2f7 ${val}%)`;
    }

    if (pswGenerate) pswGenerate.addEventListener("click", refreshPsw);
    if (pswLength) pswLength.addEventListener("input", () => { updateSliderColor(); refreshPsw(); });
    Object.values(checkboxes).forEach(box => box?.addEventListener("change", refreshPsw));

    if (pswCopy) {
        pswCopy.addEventListener("click", () => {
            navigator.clipboard.writeText(pswInput.value);
            let original = pswCopy.innerText;
            pswCopy.innerText = "Copié !";
            setTimeout(() => pswCopy.innerText = original, 2000);
        });
    }

    updateSliderColor();
    refreshPsw();
}


// =========================================================================
// MODULE 3 : LE TESTEUR (Page pswTester.php)
// =========================================================================
function initPasswordTester() {
    const pswInput = document.getElementById("pswInput");
    const pswPaste = document.getElementById("pswPaste");

    function check() {
        if(pswInput) updateUiStrength(pswInput.value, "pswCrackTime");
    }

    if (pswInput) pswInput.addEventListener("input", check);

    if (pswPaste) {
        pswPaste.addEventListener("click", async () => {
            try {
                const text = await navigator.clipboard.readText();
                pswInput.value = text;
                check();
            } catch (err) {
                alert("Erreur presse-papier : " + err);
            }
        });
    }

    // --- CORRECTION MAJEURE ICI : ATTENTE DE LA LIBRAIRIE ---
    setTimeout(async () => {
        try {
            const text = await navigator.clipboard.readText();
            if (text && pswInput) {
                pswInput.value = text;
                console.log("Texte collé, attente de zxcvbn...");

                // On vérifie toutes les 100ms si zxcvbn est chargé
                let attempts = 0;
                let waiter = setInterval(() => {
                    attempts++;
                    if(typeof zxcvbn !== 'undefined') {
                        // Librairie chargée ! On lance le check
                        clearInterval(waiter);
                        check();
                        console.log("Analyse lancée !");
                    } else if (attempts > 50) {
                        // Après 5 secondes, on abandonne pour ne pas tourner en rond
                        clearInterval(waiter);
                    }
                }, 100);
            }
        } catch (err) {
            console.log("Pas de collage auto (navigateur bloqué).");
        }
    }, 100);
}