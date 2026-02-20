import sys
import os
import json
import base64
import pandas as pd
from openpyxl import load_workbook
from Crypto.Cipher import AES
from Crypto.Util import Counter

# --- CONFIGURATION SÉCURITÉ ---
# ATTENTION : Cette clé doit être EXACTEMENT la même que celle dans Config.php
# Dans un environnement pro, on utiliserait une variable d'environnement
ENCRYPTION_KEY = "bV9zYjYmNypAIVpLdzJRM3U1eTh4eiYh"

class EncryptionService:
    @staticmethod
    def encrypt(data):
        if not data:
            data = ""
        # AES 256 CTR
        key = ENCRYPTION_KEY.encode('utf-8')
        # Création d'un IV aléatoire de 16 octets
        iv = os.urandom(16)

        # Initialisation du compteur pour le mode CTR
        ctr = Counter.new(128, initial_value=int.from_bytes(iv, byteorder='big'))
        cipher = AES.new(key, AES.MODE_CTR, counter=ctr)

        # Chiffrement des données
        encrypted = cipher.encrypt(data.encode('utf-8'))

        # On concatène IV + données chiffrées (SANS le ":")
        combined = iv + encrypted

        # CETTE LIGNE DOIT ÊTRE ALIGNÉE AVEC "combined" AU-DESSUS (4 ou 8 espaces)
        return base64.b64encode(combined).decode('utf-8')

# --- LOGIQUE DE FICHIERS ---
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
FILE_PATH = os.path.join(BASE_DIR, '..', 'Temp', 'import.xlsx')
JSON_PATH = os.path.join(BASE_DIR, '..', 'Json', 'contacts.json')

def check_Json():
    if os.path.exists(JSON_PATH):
        with open(JSON_PATH, 'r', encoding='utf-8') as file:
            try:
                return json.load(file)
            except json.JSONDecodeError:
                return {}
    return {}

def merged_Cells():
    wb = load_workbook(FILE_PATH)
    ws = wb.active
    for merged_range in list(ws.merged_cells.ranges):
        borne = str(merged_range)
        val = ws.cell(row=merged_range.min_row, column=merged_range.min_col).value
        ws.unmerge_cells(borne)
        for row in ws[borne]:
            for cell in row:
                cell.value = val
    wb.save(FILE_PATH)
    wb.close()

def get_data():
    merged_Cells()
    df = pd.read_excel(FILE_PATH, dtype="str")
    dictContacts = check_Json()
    
    # Gestion des IDs
    ids = [int(k) for k in dictContacts.keys() if k.isdigit()]
    indexContact = max(ids) + 1 if ids else 1

    encryptor = EncryptionService()

    for index, row in df.iterrows():
        # Extraction Nom/Prénom
        nom_complet = str(row.get('NOMS', '')).split()
        nom = " ".join([w for w in nom_complet if w.isupper()])
        prenom = " ".join([w for w in nom_complet if not w.isupper()])

        # Nettoyage des données brutes
        data_row = {
            "nom": nom.strip(),
            "prenom": prenom.strip(),
            "service": str(row.get('SERVICE', '')).replace('nan', ''),
            "fonctions": str(row.get('FONCTIONS', '')).replace('nan', ''),
            "numInterne": str(row.get('Interne', '')).replace('nan', ''),
            "numMobile": str(row.get('TEL MOBILE', '')).replace('nan', ''),
            "numFixe": str(row.get('TEL FIXE', '')).replace('nan', '')
        }

        # Formatage des numéros
        for key in ["numMobile", "numFixe"]:
            val = data_row[key].replace("_x000D_", " ").strip()
            if len(val) == 9: val = "0" + val
            data_row[key] = val

        # CHIFFREMENT AVANT INSERTION
        # Note : On ne peut pas facilement utiliser contact_Existe() ici 
        # car les données dans dictContacts sont déjà chiffrées (illisibles pour Python).
        # Pour simplifier, on insère tout en chiffré.
        
        dictContacts[str(indexContact)] = {
            "id": str(indexContact),
            "nom": encryptor.encrypt(data_row["nom"]),
            "prenom": encryptor.encrypt(data_row["prenom"]),
            "service": encryptor.encrypt(data_row["service"]),
            "fonctions": encryptor.encrypt(data_row["fonctions"]),
            "numInterne": encryptor.encrypt(data_row["numInterne"]),
            "numMobile": encryptor.encrypt(data_row["numMobile"]),
            "numFixe": encryptor.encrypt(data_row["numFixe"])
        }
        indexContact += 1
    
    with open(JSON_PATH, 'w', encoding='utf-8') as file:
        json.dump(dictContacts, file, indent=4, ensure_ascii=False)

    if os.path.exists(FILE_PATH):
        os.remove(FILE_PATH)

if __name__ == "__main__":
    try:
        if len(sys.argv) > 1:
            FILE_PATH = sys.argv[1]
        get_data()
        print("SUCCESS")
    except Exception as e:
        print(f"ERREUR PYTHON : {e}")