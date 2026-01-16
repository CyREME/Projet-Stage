/**
 * FICHIER JAVASCRIPT PRINCIPAL
 * Chemin : Asset/js/script.js
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Barre de navigation (partout)
    initNavBar();

    // 2. Détection de la page active via l'ID du body

    if (document.body.id === 'pswGenBody') {
        initPasswordGenerator();
    } 
    else if (document.body.id === 'pswTestBody') {
        initPasswordTester();
    }
    // AJOUT : On détecte si la div principale de l'annuaire existe
    else if (document.getElementById('annuaryBody')) {
        initAnnuaire();
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
                pswInput.value = text.substring(0, 50);
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








// =========================================================================
// MODULE 3 : L'ANNUAIRE (Page annuary.php)
// =========================================================================


// Gestion du Drag & Drop et Input File
function initAnnuaire() {

    // --- 1. FONCTIONS UTILES (Accessibles partout dans initAnnuaire) ---

    // Fonction pour gérer l'ouverture/fermeture accordéon
    const setupAccordion = (element) => {
        element.addEventListener('click', function(e) {
            // Si on clique dans un champ de formulaire ou un bouton, on ne ferme pas
            if (e.target.closest('button') || e.target.closest('form') || e.target.closest('input') || e.target.closest('label') || e.target.closest('textarea')) {
                return;
            }
            this.classList.toggle('active');
        });
    };

    // Fonction auto-resize textarea
    const autoResize = (el) => {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    };

    const setupTextarea = (element) => {
        autoResize(element);
        element.addEventListener('input', function() { autoResize(this); });
    };


    // --- 2. INITIALISATION SUR LES ELEMENTS EXISTANTS ---
    document.querySelectorAll('.contact').forEach(el => setupAccordion(el));
    document.querySelectorAll('.contact-infos textarea').forEach(el => setupTextarea(el));


    // --- 3. GESTION DRAG & DROP ---
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('csv_file');
    const importBtn = document.getElementById('importBtn');

    if (dropZone && fileInput) {
        const promptTxt = dropZone.querySelector('.drop-zone__prompt');

        dropZone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => { if(fileInput.files.length) updateThumbnail(fileInput.files[0]); });

        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('dragover'); });
        dropZone.addEventListener('dragleave', (e) => { e.preventDefault(); dropZone.classList.remove('dragover'); });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateThumbnail(e.dataTransfer.files[0]);
            }
        });

        function updateThumbnail(file) {
            if (promptTxt) promptTxt.textContent = "Fichier prêt : " + file.name;
            dropZone.style.borderColor = "#004d40";
            dropZone.style.backgroundColor = "#e0f2f1";
            if (importBtn) importBtn.style.display = "block";
        }
    }


    // --- 4. MODAL & CREATION DYNAMIQUE DE CONTACT ---
    const btnOpenModal = document.getElementById('btnOpenModal');
    const modalOverlay = document.getElementById('modalAddContact');
    const modalCancel = document.getElementById('modalCancel');
    const modalConfirm = document.getElementById('modalConfirm');
    const modalNom = document.getElementById('modalNom');
    const modalPrenom = document.getElementById('modalPrenom');
    const annuaireList = document.querySelector('.annuaire'); // Le conteneur de la liste

    // Ouvrir la modal
    if(btnOpenModal) {
        btnOpenModal.addEventListener('click', () => {
            modalOverlay.classList.add('open');
            modalNom.value = "";
            modalPrenom.value = "";
            modalNom.focus();
        });
    }

    // Fermer la modal
    if(modalCancel) {
        modalCancel.addEventListener('click', () => {
            modalOverlay.classList.remove('open');
        });
    }


    // Fermer la modal en dehors de la boite
    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                modalOverlay.classList.remove('open');
            }
        });
    }
    
    
    

    // Convertir NOM en majuscule en direct
    if(modalNom) {
        modalNom.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }

    if(modalPrenom) {
        modalPrenom.addEventListener('input', function() {
            this.value = this.value.replace(/(?:^|[\s-])\w/g, function(match) {
                return match.toUpperCase();
            });
        })
    }

    // CONFIRMER LA CREATION
    if(modalConfirm) {
        modalConfirm.addEventListener('click', () => {
            const nom = modalNom.value.trim();
            const prenom = modalPrenom.value.trim();

            if(nom === "" || prenom === "") {
                alert("Merci de remplir le Nom et le Prénom.");
                return;
            }

            // 1. Fermer la modal
            modalOverlay.classList.remove('open');

            // 2. Créer le HTML de la nouvelle fiche
            // Note : On met name="create_contact" sur le bouton submit
            // et on pré-remplit des inputs hidden pour nom et prénom
            const newContactHTML = `
            <div class='contact active' style="animation: fadeIn 0.5s;">
                <h3>${nom} ${prenom} <span style="font-size:0.8em; color:#004d40;">(Nouveau)</span></h3>

                <div class='contact-infos'>
                    <form action="" method="post" class="form-update">
                        <input type="hidden" name="new_nom" value="${nom}">
                        <input type="hidden" name="new_prenom" value="${prenom}">

                        <div class='contact-infos-group'>
                            <div class='contact-info-details'>
                                <label>Service :</label>
                                <textarea name="new_service" rows="1" placeholder="Service..."></textarea>
                            </div>
                            <div class='contact-info-details'>
                                <label>Fonction :</label>
                                <textarea name="new_fonction" rows="1" placeholder="Fonction..."></textarea>
                            </div>
                            <div class='contact-info-details'>
                                <label>Interne :</label>
                                <textarea name="new_interne" class="num-interne" rows="1"></textarea>
                            </div>
                            <div class='contact-info-details'>
                                <label>Mobile :</label>
                                <textarea name="new_mobile" class="num-tel" rows="1"></textarea>
                            </div>
                            <div class='contact-info-details'>
                                <label>Fixe :</label>
                                <textarea name="new_fixe" class="num-tel" rows="1"></textarea>
                            </div>
                        </div>

                        <div class="contact-modif">
                            <button type="submit" name="create_contact" class="btn-modif-contact">Valider la création</button>
                            <button type="button" class="btn-supp-contact" onclick="this.closest('.contact').remove()">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
            `;

            // 3. Insérer au début de la liste (prepend)
            // On crée un element temporaire pour le transformer en noeud DOM
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = newContactHTML.trim();
            const newElement = tempDiv.firstChild;

            // Insérer tout en haut
            annuaireList.insertBefore(newElement, annuaireList.firstChild);

            // 4. Activer les scripts (Accordéon + AutoResize) sur ce nouvel élément
            setupAccordion(newElement);
            newElement.querySelectorAll('textarea').forEach(el => setupTextarea(el));

            // 5. Scroll vers l'élément
            newElement.scrollIntoView({ behavior: 'smooth', block: 'center' });

            const firstInput = newElement.querySelector('textarea[name="new_service"]');
            if (firstInput) firstInput.focus();
        });
    }


    // --- 5. BARRE DE RECHERCHE (Ton code existant) ---
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    let searchTimeout = null;

    const filterContacts = () => {
        const filterValue = searchInput.value.toLowerCase().trim();
        const allContacts = document.querySelectorAll('.contact');

        allContacts.forEach(contact => {
            const nameText = contact.querySelector('h3').innerText.toLowerCase();
            let fieldsText = "";
            contact.querySelectorAll('textarea').forEach(t => fieldsText += " " + t.value.toLowerCase());

            if ((nameText + fieldsText).includes(filterValue)) {
                contact.style.display = ""; 
            } else {
                contact.style.display = "none";
            }
        });
    };

    // Scanner immédiatement quand la page charge si la barre à du texte
    if (searchInput && searchInput.value.trim() !== "") {
        filterContacts();
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            if (searchTimeout) clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => filterContacts(), 300);
        });
    }

    if (searchButton) {
        searchButton.addEventListener('click', (e) => {
            e.preventDefault();
            if (searchTimeout) clearTimeout(searchTimeout);
            filterContacts();
        });
    }

    window.addEventListener('pageshow', () => {
        if (searchInput && searchInput.value.trim() !== "") {
            filterContacts();
        }
    });
    
    // --- 4. MODAL & SUPPRESSION DE TOUS LES CONTACTES ---
    const btnOpenModalDelete = document.getElementById('btnOpenDeleteAll');
    const modalDeleteAll = document.getElementById('modalDeleteAll');
    const btnCancelModalDelete = document.getElementById('modalCancelDelete');
    const btnConfirmModalDelete = document.getElementById('modalConfirmDelete');


    // Ouvrir la modal
    if (btnOpenModalDelete && modalDeleteAll) {
        btnOpenModalDelete.addEventListener('click', () => {
            modalDeleteAll.classList.add('open');
        });
    } 

    // Fermer la modal
    if (btnCancelModalDelete && modalDeleteAll) {
        btnCancelModalDelete.addEventListener('click', () => {
            modalDeleteAll.classList.remove('open');
        });
    }

    // Fermer la modal en dehors de la boite
    if (modalDeleteAll) {
        modalDeleteAll.addEventListener('click', (e) => {
            if (e.target === modalDeleteAll) {
                modalDeleteAll.classList.remove('open');
            }
        });
    }


    // CONFIRMER LA SUPPRESSION
    if (btnConfirmModalDelete) {
        btnConfirmModalDelete.addEventListener('click', () => {
            modalDeleteAll.classList.remove('open');

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_all';
            input.value = 'true';

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
            
        })
    }



    // --- 5. NOTIFICATIONS ---
    
    // Fonction pour afficher une notification
    function showNotification(message) {
        const notif = document.getElementById('notifAnnuaire');
        const type = document.getElementById('notif-message').getAttribute('data-type');
        
        if (type === 'erreur' || type === 'error') {
            notif.style.backgroundColor = '#ef5350';
        } else {
            notif.style.backgroundColor = '#004d40';
        }
        
        if (notif) {
            notif.classList.add('show');
        }
    
        setTimeout(() => {
            if (notif) {
                notif.classList.remove('show');
            }
        }, 3000);
    }
    
    // Afficher la notification si le message existe
    const notifMessage = document.getElementById('notif-message');
    if (notifMessage && notifMessage.textContent.trim() !== "") {
        showNotification(notifMessage.textContent);
    }

}