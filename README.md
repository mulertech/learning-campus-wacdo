# Projet Wacdo

## Description

### Contexte du projet
Vous êtes sollicité(e) pour développer l’application de **Wacdo** afin de gérer les **affectations des collaborateurs** dans les différents restaurants.

### Technologies et outils :
- **Backend** : Utilisation du **Framework Symfony** pour le développement de l’application.
- **Frontend** : Utilisation de **twig**, **TandwindCSS** et Symfony UX
- **Base de Données** : Utilisation d’une base de données **SQL** (par exemple, **MySQL**, **PostgreSQL**).
- Gestion des entités avec un **ORM** (ex : **Doctrine**).

### Modèle de données :
Le modèle de données, que vous pouvez enrichir si nécessaire, comporte les objets suivants :

- **Collaborateur** (nom, prénom, email, date de première embauche, administrateur (**true** / **false**), mot de passe (pour les administrateurs)
- **Restaurant** (nom, adresse, code postal, ville)
- **Fonction** (les postes existants chez Wacdo)
Intitulé du poste (exemple : équipier polyvalent, manager, etc.…)
- **Affectation** (affectation des équipiers sur les postes et les restaurants)
- collaborateur (objet collaborateur)

  restaurant (objet restaurant où le collaborateur est affecté)

  poste (objet poste)

  début (date de début d'affectation)

  fin (date de fin d'affectation ou vide si l'affectation est active)

### Fonctionnalités de l'Application :
L'application est utilisable uniquement si l’utilisateur est **identifié** via un compte **collaborateur** ayant le droit **administrateur** (et un **mot de passe**).

Le menu principal comporte les options suivantes :

- Gestion des restaurants
- Gestion des collaborateurs
- Gestion des fonctions
- Recherche des affectations

#### Gestion des restaurants :

- On arrive sur la **liste des restaurants**, avec un formulaire pour rechercher et filtrer (par nom, par code postal, par ville.
- On a un bouton pour créer un restaurant.
- Les éléments de la liste sont cliquables, pour avoir le détail du restaurant,

  incluant la liste des collaborateurs en poste dans ce restaurant (poste en cours).

  Cette liste est filtrable par poste, par nom, par date de début d'affectation.
- Sur le détail, un bouton “modifier”, permet de voir l'historique des affectations (filtrable) et d’affecter un nouveau collaborateur.

#### Gestion des fonctions :

- Permet de voir la liste des différentes fonctions
- Un bouton permet de créer une fonction et chaque fonction est éditable

#### Gestion des collaborateurs :

- Dirige sur la vue comportant la liste des collaborateurs, avec un formulaire pour rechercher et filtrer (par nom, prénom, email)
- Un bouton permet de créer un collaborateur, et un bouton permet de rechercher les collaborateurs non affectés.
- Les éléments de la liste sont cliquables, pour avoir le détail du collaborateur, incluant la ou les affections en cours, et l'historique des affectations.

  Cette liste est filtrable par poste, par date de début d'affectation.
- Sur le détail, un bouton permet de modifier le collaborateur, pour l'affecter à un nouveau poste.
- Les affectations en cours sont modifiables.

#### Recherche des affectations :

- Permet d’afficher la liste des affectations
- Avec un formulaire pour rechercher et filtrer par poste, par date de début et de fin, par ville.

### Tests et Validation :
- **Validation des données** :
    - Une étape ne peut pas être finalisée s’il manque des informations essentielles.
    - Les **champs de formulaires** doivent être vérifiés (téléphone, adresse, nom, prénom).
- **Tests** :
    - Avant le déploiement de l'application, une série de **tests** doit être effectuée pour s'assurer
      
  que l'application répond aux spécifications mentionnées, y compris des tests d'interface utilisateur,

  des **tests fonctionnels** et des tests de **sécurité**.

## Déploiement
### 1. Template docker Symfony

Template optimisé pour les applications Symfony avec Apache et connexion à une base PostgreSQL partagée.

**Structure :**
- Apache avec PHP
- Connexion à PostgreSQL partagé
- Intégration Traefik pour le reverse proxy
- Support des certificats SSL

**Configuration requise :**

A. Script automatisé `prepare_project.sh` (recommandé) :
1. **Copier les fichiers de base puis modifier les variables du script** :
    - Copier le contenu du template vers votre projet
    - Modifier les variables `SUBDOMAIN`, `GIT_PROJECT`, `CONTAINER_NAME`, `DB_NAME` dans `prepare_project.sh`


2. **Rendre le script exécutable puis l'exécuter** :
   ```bash
   chmod +x prepare_project.sh
   ./prepare_project.sh
   ```

3. **Lancer le déploiement** :
   ```bash
   ./deploy.sh
   ```

B. Configuration manuelle (étapes détaillées) :
1. **Copier et configurer les fichiers de base** :
    - Copier le contenu du template vers votre projet
    - Éditer le fichier `.env` avec vos paramètres (ne pas oublier les id de l'utilisateur et du groupe)
    - Configurer Apache (fichier `www/000-default.conf`)


2. **Générer les clés SSH** (nom obligatoire "id_ed25519") dans le dossier `www/` (commande à exécuter à la racine du projet) :
   ```bash
   ssh-keygen -t ed25519 -f www/id_ed25519 -q -N "" -C "sebastien.muler@mulertech.net"
   ```

3. **Ajouter la clé publique aux Deploy Keys GitHub** :
   ```bash
   cat www/id_ed25519.pub
   ```
    - Aller dans votre repository > Settings > Deploy keys > Add deploy key
    - Coller le contenu de la clé publique
    - Donner un titre et autoriser l'accès en écriture si nécessaire

4. **Vérifier que symfony/apache-pack est installé dans le projet** :
   ```bash
   composer require symfony/apache-pack
   ```

5. **Configurer le mot de passe de la base de données** :
    - Modifier le fichier `secrets/database_password.txt` en indiquant uniquement le mot de passe de la base de données.
      Attention : ce mot de passe doit être conforme URL-encoded si nécessaire (ex: `@` devient `%40`, `$` devient `%24`, etc.)

   ```bash

6. **Créer la base de données et le compte utilisateur sur le serveur PostgreSQL** :
   ```bash
   # Depuis le répertoire du projet (qui contient .env et secrets/)
   bash ../postgres/create_postgresql_user.sh
   ```
   Le script détecte automatiquement les variables `DATABASE_USER` et `DATABASE_NAME` depuis le fichier `.env` et récupère le mot de passe depuis `secrets/database_password.txt`.

7. **Rendre le script de déploiement exécutable puis l'exécuter** :
   ```bash
   chmod +x deploy.sh
   ./deploy.sh
   ```
