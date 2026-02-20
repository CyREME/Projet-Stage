# 🚀 Nexus - Portails d'Outils & Annuaire Sécurisé

Nexus est une plateforme web développée dans le cadre de mon **BTS SIO (Services Informatiques aux Organisations)**. Elle regroupe des outils utilitaires pour les administrateurs et un annuaire d'entreprise centralisé avec une gestion avancée de la sécurité des données.

## 🌟 Points Forts du Projet

- **Sécurité "Safe by Design"** : Chiffrement intégral des données sensibles (RGPD compliant).
- **Interopérabilité** : Communication fluide entre PHP (Backend) et Python (Traitement de données).
- **Automatisation CI/CD** : Déploiement automatique via GitHub Actions sur serveur Infomaniak.

## 🛠️ Stack Technique

- **Frontend** : HTML5, CSS3 (Variables modernes, Flexbox), JavaScript.
- **Backend PHP** : Gestion de la logique serveur et du routage.
- **Python** : Scripting pour le traitement automatisé des fichiers Excel (Pandas, OpenPyxl).
- **Base de données** : Stockage JSON sécurisé.
- **Déploiement** : Node.js, GitHub Actions, SSH.

## 🔐 Focus Sécurité : Chiffrement AES-256

La fonctionnalité phare de ce projet est la sécurisation de l'annuaire. Contrairement à un stockage classique, Nexus utilise un algorithme de chiffrement symétrique :

- **Algorithme** : AES-256-CTR.
- **Vecteur d'Initialisation (IV)** : Chaque entrée possède un IV unique, garantissant que deux noms identiques ne produisent pas le même résultat chiffré (protection contre l'analyse de motifs).
- **Interopérabilité PHP/Python** : Une classe de chiffrement personnalisée a été développée dans les deux langages pour garantir que les données importées par Python soient lisibles par PHP et inversement.

## 📂 Structure du Projet

- `/Assets/Fonction` : Logique de chiffrement et configuration.
- `/Assets/Outils` : Modules de l'application (Annuaire, Password Checker, etc.).
- `/Assets/Python` : Scripts de traitement de données Excel.
- `/Assets/Json` : Stockage des données (protégé par `.htaccess`).

## 🚀 Installation & Déploiement

1.  **Clonage du dépôt** :
    ```bash
    git clone [https://github.com/ton-pseudo/projet-stage.git](https://github.com/ton-pseudo/projet-stage.git)
    ```

2.  **Configuration** :
    * Créer un fichier `Assets/Fonction/Config.php` avec une clé `ENCRYPTION_KEY` de 32 caractères.
    * S'assurer que Python dispose des dépendances : 
        ```bash
        pip install pandas openpyxl pycryptodome
        ```

3.  **Déploiement** :
    * Le projet est configuré pour se déployer automatiquement via **GitHub Actions** sur un hébergement **Infomaniak** à chaque `push` sur la branche `main`.

## 👨‍💻 Auteur

**Emeric Cellier** - Candidat au BTS Services Informatiques aux Organisations.  
Session 2026.
