import sys
import os
import psycopg2
import pandas as pd
import json
from openpyxl import load_workbook

def connectDB():
  db_host = os.environ.get('DB_HOST')
  db_name = os.environ.get('DB_NAME')
  db_user = os.environ.get('DB_USER')
  db_pass = os.environ.get('DB_PASS')

  try:
    
    conn = psycopg2.connect(
      host=db_host,
      database=db_name,
      user=db_user,
      password=db_pass
    )
    return conn
    
  except Exception as e:
    print(f"Erreur de connexion à la base de données : {e}")



def reparer_cellules_fusionnees(chemin_fichier):
  """
  Cette fonction détecte les cellules fusionnées, récupère la valeur 
  de la première cellule, 'défusionne' le bloc, et remplit toutes 
  les cellules du bloc avec cette valeur.
  """
  wb = load_workbook(chemin_fichier)
  ws = wb.active

  # On récupère la liste des zones fusionnées
  # On convertit en liste pour pouvoir modifier la feuille sans casser la boucle
  merged_ranges = list(ws.merged_cells.ranges)

  for group in merged_ranges:
      # 1. On identifie les limites du groupe (ex: A1:A4)
      min_col, min_row, max_col, max_row = group.min_col, group.min_row, group.max_col, group.max_row

      # 2. On prend la valeur de la cellule en haut à gauche (la "vraie" valeur)
      top_left_cell_value = ws.cell(row=min_row, column=min_col).value

      # 3. On défusionne la zone pour pouvoir écrire dedans
      ws.unmerge_cells(start_row=min_row, start_column=min_col, end_row=max_row, end_column=max_col)

      # 4. On remplit toutes les cellules de la zone avec la valeur récupérée
      for row in range(min_row, max_row + 1):
          for col in range(min_col, max_col + 1):
              cell = ws.cell(row=row, column=col)
              cell.value = top_left_cell_value

  # On sauvegarde le fichier réparé (on écrase ou on crée un temporaire)
  wb.save(chemin_fichier)
  wb.close()




def extractData():

  conn = connectDB()
  if conn is None:
    return

  cur = conn.cursor()
  
  dossier_actuel = os.path.dirname(os.path.abspath(__file__))

  fichier = os.path.join(dossier_actuel, "../Temp/import.xlsx")

  reparer_cellules_fusionnees(fichier)
  
  df = pd.read_excel(fichier, dtype=str)
  df = df.fillna("")

  sql = """
  INSERT INTO "Cotrans" 
  ("Nom", "Prenom", "Service", "Fonction", "NumInterne", "NumMobile", "NumFixe")
  VALUES (%s, %s, %s, %s, %s, %s, %s)
  ON CONFLICT ("Nom", "Prenom") 
  DO UPDATE SET
  "Service" = EXCLUDED."Service",
  "Fonction" = EXCLUDED."Fonction",
  "NumInterne" = EXCLUDED."NumInterne",
  "NumMobile" = EXCLUDED."NumMobile",
  "NumFixe" = EXCLUDED."NumFixe"
  """
  
  for index, row in df.iterrows():

    #### NOM ET PRENOM ####
    nomData = row['NOMS'].split()
    nom = ""
    prenom = ""
    for i in range(len(nomData)):
      if (nomData[i].isupper()):
        nom += nomData[i] + " "
      else:
        prenom += nomData[i] + " "

    ## SERVICE ##
    service = row['SERVICE']
    
    ## FONCTION ##
    fonction = row['FONCTIONS']
    
    ## NUMERO INTERNE ##
    numInterne = str(row['Interne'])
    
    ## NUMERO TEL MOBILE ##
    numMobileData = str(row['TEL MOBILE']).split()
    numMobile = ""
    if (len(numMobileData) > 1):
      for i in range(len(numMobileData)):
        if len(numMobileData[i]) == 9:
          numMobile = "0" + numMobileData[i]
        else:
          numMobile += numMobileData[i] + ";"
          
    elif len(numMobileData) == 0:
      numMobile = ""
    else:
      if len(numMobileData[0]) == 9:
        numMobile = "0" + numMobileData[0]
    
    ## NUMERO TEL FIXE ##
    numFixeData = str(row['TEL FIXE']).split()
    numFixe = ""
    if len(numFixeData) > 1:
      for i in range(len(numFixeData)):
        numFixe += numFixeData[i] + ";"
    elif len(numFixeData) == 0:
      numFixe = ""
    else:
      numFixe = numFixeData[0]


    valeurs = (nom, prenom, service, fonction, numInterne, numMobile, numFixe)

    try: 
      cur.execute(sql, valeurs)
    except Exception as e:
      print(f"Erreur lors de l'insertion des données : {e}")

  conn.commit()
  print("Données insérées avec succès !")

  cur.close()
  conn.close()

  os.remove(fichier)



if  __name__ == "__main__":
  # sys.argv[0] nom du script
  # sys.argv[1] nom de la fonction
  # sys.argv[2] le fichier

  if len(sys.argv) > 1:
    action = sys.argv[1]

    if action == "extractData":
      extractData()

    else:
      print(f"Action '{action}' non reconnue")

else:
  print("Erreur : Aucun action précisé")
    