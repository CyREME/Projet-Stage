import pdfplumber
import sys
import json
import re

# --- CONFIGURATION ESSENTIELLE ---
sys.path.append("/home/runner/.local/lib/python3.10/site-packages")

# LISTE DE MOTS À SUPPRIMER (Casse-insensible)
# Si un nom contient ces mots, on les efface. Si le nom ne contient QUE ça, on jette le contact.
BAD_WORDS = [
    "DIRECTION", "SERVICE", "COMMERCIAL", "COMPTABILITE", "ADMINISTRATION", 
    "ATELIER", "RECEPTION", "MAGASIN", "PIECES", "SAV", "S.A.V", "APRES-VENTE",
    "MERCEDES", "AUDI", "VW", "VOLKSWAGEN", "SKODA", "SUZUKI", "MITSUBISHI", "SMART",
    "COTRANS", "AUTOMOBILES", "SAINT", "DENIS", "PIERRE", "PORT", "LA", "DE",
    "FAX", "INTERNE", "FIXE", "GSM", "FONCTION", "NOM", "PRENOM", "TEL", "SITE",
    "DIRECTEUR", "RESPONSABLE", "ASSISTANT", "ASSISTANTE", "CHEF", "VENTES", "VENTS",
    "CONSEILLER", "GESTIONNAIRE", "FONCTIONS", "NOMS", "GENERAL", "MARKETING",
    "FINANCIER", "ADMINISTRATIF", "RH", "RESSOURCES", "HUMAINES", "ACCUEIL",
    "SECRETAIRE", "MOYENS", "GENERAUX", "QUALITE", "LOGISTIQUE", "PREPARATEUR",
    "LIVREUR", "LIVRAISON", "CARROSSERIE", "PEINTURE", "MECANIQUE", "TECH", "SUPPORT",
    "INFORMATIQUE", "GARANTIE", "METHODES", "ITINERANT", "SECTEUR", "ZONE"
]

def clean_phone(text):
    """Extrait et formate les numéros de téléphone."""
    # On cherche les motifs de numéros Réunion (06 92... 02 62...) ou internes (3-4 chiffres)
    # Regex : cherche 06/02 suivi de chiffres, ou juste 3-4 chiffres isolés
    found = []

    # 1. Mobiles et Fixes (ex: 06 92 12 34 56 ou 0262...)
    matches_long = re.findall(r'(?:06|02)\s*[\d\s\.]{8,}', text)
    for m in matches_long:
        clean = m.replace(" ", "").replace(".", "")
        if len(clean) >= 10:
            found.append(clean)

    # 2. Internes (3 ou 4 chiffres, mais on évite les années 2023 etc si possible)
    # On ne prend les internes que si on a déjà un nom valide, pour éviter les faux positifs
    matches_short = re.findall(r'\b\d{3,4}\b', text)
    for m in matches_short:
        # On évite les codes postaux (974..) ou années (20..)
        if not m.startswith("97") and not m.startswith("20"):
            found.append(m)

    return list(set(found)) # Dédoublonnage

def clean_name(text):
    """Nettoie le nom en retirant les titres et le bruit."""
    if not text: return ""

    # Remplacer les caractères non-lettres par des espaces
    text = re.sub(r'[^\w\s]', ' ', text)
    words = text.split()

    kept_words = []
    for w in words:
        # Si le mot (en majuscule) n'est pas dans la liste noire
        # Et qu'il fait plus de 1 lettre
        if w.upper() not in BAD_WORDS and len(w) > 1 and not w.isdigit():
            kept_words.append(w)

    if not kept_words: return ""

    # On reforme le nom (ex: "MERCEDES FELIX Olivier" -> "FELIX Olivier")
    return " ".join(kept_words).title()

def process_pdf(file_path):
    all_contacts = []

    try:
        with pdfplumber.open(file_path) as pdf:
            for page in pdf.pages:
                # STRATÉGIE : Extraction VISUELLE (Layout)
                # Cela garde les espaces entre les colonnes
                text = page.extract_text(layout=True, x_tolerance=2, y_tolerance=2)

                if not text: continue

                lines = text.split('\n')

                for line in lines:
                    # On ignore les lignes trop courtes
                    if len(line.strip()) < 5: continue

                    # DÉTECTION DE COLONNES PAR ESPACES
                    # Si on a plus de 3 espaces consécutifs, c'est probablement une séparation de colonne
                    cols = re.split(r'\s{3,}', line)

                    for col_text in cols:
                        col_text = col_text.strip()
                        if not col_text: continue

                        # 1. Extraction des numéros
                        phones = clean_phone(col_text)

                        # RÈGLE D'OR : Pas de numéro = Pas de contact
                        if not phones:
                            continue

                        # 2. Si on a un numéro, on essaie de trouver le nom DANS LE MÊME BLOC
                        # On enlève les numéros du texte pour voir ce qu'il reste (le nom)
                        text_without_phones = col_text
                        for p in phones:
                            # On essaie de retirer le numéro (format brut ou formaté)
                            # C'est approximatif, mais on veut juste voir s'il reste du texte
                            pass 

                        # Plus simple : on nettoie tout le bloc avec la Blacklist
                        name_candidate = clean_name(col_text)

                        # 3. Validation Finale
                        # Le nom doit faire au moins 3 lettres
                        if len(name_candidate) > 2:
                            all_contacts.append({
                                "nom": name_candidate,
                                "numeros": phones
                            })

        # --- DÉDOUBLONNAGE ET FUSION ---
        unique_contacts = {}
        for c in all_contacts:
            key = c['nom'].upper()

            # Formatage propre des numéros
            nums_clean = [n for n in c['numeros'] if len(n) > 2]

            if not nums_clean: continue # Sécurité supplémentaire

            if key in unique_contacts:
                # Fusion des numéros
                existing_nums = unique_contacts[key]['numeros']
                new_nums = list(set(existing_nums + nums_clean)) # Set pour éviter doublons
                unique_contacts[key]['numeros'] = new_nums
            else:
                unique_contacts[key] = {"nom": c['nom'], "numeros": nums_clean}

        # Conversion finale pour JSON (liste d'objets)
        final_list = []
        for key, val in unique_contacts.items():
            final_list.append({
                "nom": val['nom'],
                "numeros": " / ".join(val['numeros'])
            })

        print(json.dumps(final_list))

    except Exception as e:
        print(json.dumps({"error": f"Erreur Python: {str(e)}"}))

if __name__ == "__main__":
    if len(sys.argv) > 1:
        process_pdf(sys.argv[1])
    else:
        print(json.dumps({"error": "Fichier manquant"}))