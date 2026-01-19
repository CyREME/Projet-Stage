import sys
import os
import json
import math
import pandas as pd
from openpyxl import load_workbook

# On récupère le dossier où se trouve ce fichier (fonctions.py)
BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# On définit les chemins par rapport à ce dossier
# Par défaut (si pas d'argument), on tape dans Temp/import.xlsx
FILE_PATH = os.path.join(BASE_DIR, '..', 'Temp', 'import.xlsx')
JSON_PATH = os.path.join(BASE_DIR, '..', 'Json', 'contacts.json')

def check_Json():
  if os.path.exists(JSON_PATH):
    with open(JSON_PATH, 'r') as file:
      data = json.load(file)
      return data
  else:
    os.makedirs(os.path.dirname(JSON_PATH), exist_ok=True)
    with open(JSON_PATH, 'w') as file:
      json.dump({}, file)
    return {}
  


def merged_Cells():
  wb = load_workbook(FILE_PATH)
  ws = wb.active
  
  for merged_range in list(ws.merged_cells.ranges):
    
    borne = str(merged_range)

    val = ws.cell(row=merged_range.min_row, column=merged_range.min_col).value

    ws.unmerge_cells(borne)

    cell_range = ws[borne]
    for row in cell_range:
      for cell in row:
        cell.value = val

  wb.save(FILE_PATH)
  wb.close()

def contact_Existe(new_nom, new_prenom, contacts):
  for c in contacts.values():
    existing_nom = c.get("nom", "").lower()
    existing_prenom = c.get("prenom", "").lower()
    
    if existing_nom == new_nom.lower() and existing_prenom == new_prenom.lower() :
      return True
  return False

def get_data():
  merged_Cells()
  # Chemin du fichier JSON
  df = pd.read_excel(FILE_PATH, dtype="str")

  dictContacts = check_Json()
  indexContact = len(dictContacts) + 1

  for index, row in df.iterrows():

    # Partie pour le Nom & Prenom
    nomCompose = row['NOMS'].split()
    nom = ""
    prenom = ""
    
    for word in nomCompose:
      if word.isupper():
        nom += word + " "
      else:
        prenom += word + " "
        
    nom = nom.rstrip()
    prenom = prenom.rstrip()



    # Partie pour le Service
    service = row['SERVICE']
    if pd.isna(service):
      service = ""

    # Partie pour les fonctions
    fonctions = row['FONCTIONS']

    if pd.isna(fonctions):
      fonctions = ""

    # Partie Numéro Interne
    numInterne = row['Interne']

    if pd.isna(numInterne):
      numInterne = ""
    
    # Partie numéro Mobile
    numMobile = row['TEL MOBILE']
    
    if pd.isna(numMobile):
      numMobile = ""

    numMobile = numMobile.replace("_x000D_", " ")

    if len(numMobile) == 9:
      numMobile = "0" + numMobile


    # Partie numéro Fixe
    numFixe = row['TEL FIXE']

    if pd.isna(numFixe):
      numFixe = ""

    numFixe = numFixe.replace("_x000D_", " ")

    if len(numFixe) == 9:
      numFixe = "0" + numFixe


    if contact_Existe(nom, prenom, dictContacts):
      continue
    
    
    dictContacts[indexContact] = {
      "id": str(indexContact),
      "nom": nom,
      "prenom": prenom,
      "service": service,
      "fonctions": fonctions,
      "numInterne": numInterne,
      "numMobile": numMobile,
      "numFixe": numFixe
    }
    indexContact += 1
    
  
  with open(JSON_PATH, 'w') as file:
    json.dump(dictContacts, file, indent=4, ensure_ascii=False)

  os.remove(FILE_PATH)

if __name__ == "__main__":
  try:
      # Si PHP envoie un argument, on l'utilise
      if len(sys.argv) > 1:
          FILE_PATH = sys.argv[1]

      get_data()
  except Exception as e:
      # En cas d'erreur, on l'affiche pour que PHP la récupère
      print(f"ERREUR PYTHON : {e}")