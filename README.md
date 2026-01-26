# Nexus - Portail d'Outils Internes & Annuaire Dynamique

Nexus est une plateforme web modulaire développée dans le cadre d'un stage de 2ème année de BTS SIO (SLAM). Elle regroupe plusieurs outils utilitaires destinés à faciliter la gestion quotidienne des données et à renforcer la cybersécurité au sein de l'organisation.

## 🚀 Fonctionnalités Principales

### 1. Annuaire d'Entreprise Intelligent
* **Affichage dynamique** : Liste des contacts sous forme de cartes interactives.
* **Recherche temps réel** : Filtrage instantané des contacts via JavaScript.
* **Importation Excel (ETL)** : Module d'importation utilisant un moteur Python pour transformer des fichiers Excel complexes en données exploitables.
* **Fusion intelligente** : Gestion automatique des doublons (fusion des services et fonctions pour un même contact).
* **Gestion des cellules fusionnées** : Algorithme capable de traiter les fichiers Excel mal formatés.

### 2. Password Generator
* Génération de mots de passe robustes conformes aux recommandations de l'ANSSI.
* Paramétrage personnalisé (longueur, caractères spéciaux, chiffres).
* Fonction "Copier en un clic".

### 3. Password Checker
* Analyse de l'entropie et de la robustesse des mots de passe saisis.
* Indicateur visuel de force (code couleur dynamique).
* Estimation pédagogique du temps nécessaire pour un craquage par force brute.

## 🛠️ Stack Technique

* **Frontend** : HTML5, CSS3 (Flexbox/Grid), JavaScript (ES6+).
* **Backend** : PHP 8.x.
* **Traitement de Données** : Python 3.x avec la bibliothèque **Pandas** et **Openpyxl**.
* **Stockage** : NoSQL via fichiers structurés JSON.

## 📂 Architecture du Projet

Le projet est conçu de manière modulaire :
```text
├── index.php                # Point d'entrée principal
├── Assets/
│   ├── Outils/             # Modules PHP indépendants (Annuaire, Psw...)
│   ├── Interface-modules/  # Composants UI (NavBar dynamique)
│   ├── Python/             # Scripts de traitement de données
│   ├── Fonction/           # Scripts PHP utilitaires
│   ├── Json/               # Persistance des données (contacts.json)
│   └── css/js/             # Ressources statiques
