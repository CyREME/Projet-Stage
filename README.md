# 🚀 Nexus - Portails d'Outils & Annuaire Sécurisé

Nexus est une plateforme web développée dans le cadre de mon **BTS SIO (Services Informatiques aux Organisations)**. Elle regroupe des outils utilitaires pour les administrateurs et un annuaire d'entreprise centralisé avec une gestion avancée de la sécurité des données.

## 🌟 Points Forts du Projet

- **Sécurité "Safe by Design"** : Chiffrement intégral des données sensibles (RGPD compliant).
- **Interopérabilité** : Traitement de données complexe via Python intégré de manière transparente dans une interface Backend PHP.
- **Déploiement Flexible** : Architecture conçue pour être déployée sur n'importe quel serveur web supportant PHP et Python.

## 🛠️ Stack Technique

- **Frontend** : HTML5, CSS3 (Flexbox, Variables), JavaScript (ES6).
- **Backend PHP** : Routage dynamique des modules et gestion de la logique métier.
- **Python** : Moteur de traitement de données (Pandas, OpenPyxl) pour l'importation de fichiers Excel.
- **Base de données** : Stockage structuré au format JSON avec accès sécurisé par directive serveur.
- **Cryptographie** : Bibliothèque `pycryptodome` (Python) et extension `OpenSSL` (PHP).

## 🔐 Focus Sécurité : Chiffrement AES-256

La fonctionnalité phare de ce projet est la sécurisation de l'annuaire. Nexus utilise un algorithme de chiffrement symétrique CTR :

- **Algorithme** : AES-256-CTR.
- **Vecteur d'Initialisation (IV)** : Chaque entrée possède un IV unique, garantissant que deux noms identiques ne produisent pas le même résultat chiffré.
- **Interopérabilité PHP/Python** : Une logique de chiffrement symétrique a été implémentée dans les deux langages. Les données importées et chiffrées par Python (`fonctions.py`) sont instantanément déchiffrables par PHP (`Fonctions.php`) grâce au partage de la clé de sécurité.

## 📂 Structure du Projet

- `/Assets/Fonction` : Logique de chiffrement et configuration (`Config.php`).
- `/Assets/Outils` : Modules applicatifs (Annuaire, Password Checker, etc.).
- `/Assets/Python` : Scripts de traitement automatisé.
- `/Assets/Json` : Stockage des données, protégé par `.htaccess` contre l'accès Web direct.
- `/Assets/Temp` : Répertoire de transit pour les fichiers Excel lors de l'import.

## 🚀 Installation & Configuration

### 1. Prérequis Système
Le projet nécessite un environnement capable d'exécuter simultanément un serveur web et des scripts système :

* **Serveur Web** : Apache (avec `mod_rewrite` activé pour le `.htaccess`) ou Nginx.
* **PHP (7.4 minimum)** : 
    * **Extension OpenSSL** : Indispensable pour les fonctions `openssl_encrypt`.
    * **Extension MBString** : Pour la gestion des caractères spéciaux.
    * **Extension JSON** : L'application repose entièrement sur la lecture et l'écriture de fichiers `.json`.
    * **Fonction** : `shell_exe` doit être activée dans la configuration PHP (`php.ini`), car c'est elle qui permet à PHP de piloter le script Python.
* **Python (3.8 minimum)** : Pour le moteur d'importation.
* **Droits d'écriture** : Le serveur (utilisateur `www-data` ou équivalent) doit pouvoir écrire dans `/Assets/Json` et `/Assets/Temp`.
  
  ```bash
  chmod -R 775 Assets/Json Assets/Temp
  ```

### 2. Installation des dépendances Python
Installez les bibliothèques requises pour le script de traitement :
```bash
pip install pandas openpyxl pycryptodome
```
ou
```bash
pip3 install pandas openpyxl pycryptodome
```

### 3. Configuration de la clé de sécurité
Le système utilise un chiffrement symétrique **AES-256-CTR**. Sans une clé valide, les données de l'annuaire resteront illisibles.
1. Localisez le fichier `Assets/Fonction/Config.php`.
2. Modifiez la constante `ENCRYPTION_KEY` avec une chaîne aléatoire d'exactement **32 caractères**.
   - Note technique : Pour une sécurité optimale, générez une clé robuste (ex: via un gestionnaire de mots de passe).
3. **IMPORTANT** : Cette clé doit rester confidentielle et ne jamais être publiée sur un dépôt public. Si vous perdez cette clé, toutes les données stockées dans `contacts.json` seront définitivement irrécupérables.

```php
// Assets/Fonction/Config.php
define('ENCRYPTION_KEY', 'VOTRE_CLÉ_ALÉATOIRE_32_CHARS_ICI');
```
   - **Note d'interopérabilité** : Cette même clé est automatiquement récupérée par le script Python (`fonctions.py`) pour garantir que les données importées depuis Excel soient chiffrées avec les mêmes paramètres que ceux utilisés par PHP pour l'affichage.

### 4. Vérification de l'installation
- **Accès Web** : Accédez à l'URL de votre serveur (ex: `index.php`).
  
- **Test du Chiffrement** : Créez un contact manuellement dans l'annuaire. Si les informations s'affichent en clair après l'enregistrement (et non sous forme de caractères étranges), le module PHP `openssl` et votre clé sont opérationnels.
  
- **Test d'Interopérabilité** : Importez un fichier `.xlsx` via la zone de dépôt. Si le message **"SUCCESS"** apparaît dans l'interface utilisateur (notification verte), cela confirme que PHP a réussi à appeler le script Python et que les dépendances (`pandas`, `pycryptodome`) sont bien installées.

## 👨‍💻 Auteur
**Emeric Cellier** - Candidat au BTS Services Informatiques aux Organisations.

Session 2026.
