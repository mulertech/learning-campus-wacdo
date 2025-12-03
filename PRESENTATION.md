# PRÉSENTATION WACDO

## CHECKLIST DE PRÉSENTATION

---

## 0. AUTHENTIFICATION ET SÉCURITÉ
- [x] **Identifiants**
  - Identifiants administrateur :
      - Email: `admin@admin.fr`
      - Mot de passe: `password`
  - Identifiants collaborateur non-admin inscrit manuellement par l'interface :
      - Email: `collaborateur@collaborateur.fr`
      - Mot de passe: `password`
- [x] **Système de connexion administrateur uniquement**
  - Package: `symfony/security-bundle`
  - Protection dans `security.yaml` via `access_control` pour ROLE_ADMIN
  - Routes protégées : `/restaurant`, `/collaborateur`, `/fonction`, `/affectation`
- [ ] **Informations complémentaires** (fichiers Utilisateur.php et Collaborateur.php)
    - Entités: `Utilisateur` (email, password, roles) + `Collaborateur` (prenom, nom, email, administrateur bool)
    - Login avec CSRF: `enable_csrf: true` dans security.yaml:22
    - Tokens CSRF sur toutes les suppressions : `isCsrfTokenValid()` dans controllers

---

## 1. GESTION DES RESTAURANTS

### Liste des restaurants (index)
- [x] **Formulaire de recherche et filtrage**
  - Filtres: nom, code postal, ville
  - Composant du filtre : `RestaurantFiltreForm`
  - Form: `RestaurantFiltreType` (CSRF désactivé pour GET)
  - Repository: `RestaurantRepository::findAllWithFilter()` avec QueryBuilder

- [x] **Bouton créer un nouveau restaurant**
  - Form: `RestaurantType`

- [ ] **Pour information : Pagination**
  - Package: `knplabs/knp-paginator-bundle`
  - 10 résultats par page

### Détail du restaurant (show)
- [x] **Bouton modifier ce restaurant**
    - Edition dans la page show avec formulaire POST

- [x] **Liste des collaborateurs en poste actuel**
  - Filtres : poste (fonction), nom, date de début
  - Composant du filtre : `RestaurantShow`
  - Repository : `RestaurantRepository.php::findCurrentAffectationsWithFilter()`

### Historique des affectations (bouton Modifier)
- [x] **Voir toutes les affectations du restaurant**
  - Filtres : poste (fonction), nom, date de début
  - Composant du filtre : `RestaurantAffectations`
  - Repository: `findAllAffectationsWithFilter()`

- [x] **Affecter un nouveau collaborateur** (bouton Affecter un nouveau collaborateur)
  - Form: `AffectationToRestaurantType` (autocomplete => true pour collaborateur)
  - Entity : `Collaborateur` (__toString() pour le formatage)

---

## 2. GESTION DES COLLABORATEURS

### Liste des collaborateurs (index)
- [x] **Formulaire de recherche et filtrage**
  - Filtres: prénom, nom, email
  - Composant du filtre : `CollaborateurFiltreForm`

- [x] **Bouton créer un nouveau collaborateur**
  - Form: `CollaborateurType`
  - Auto-promotion en ROLE_ADMIN si `administrateur=true`

- [x] **Bouton collaborateurs non affectés**
  - Repository: `findAllWithoutAffectation()`

### Détail du collaborateur (show)
- [x] **Bouton modifier ce collaborateur**
  - Edition dans la page show avec formulaire POST
  - Auto-sync ROLE_ADMIN si `administrateur=true` modifié

- [x] **Affectations en cours**
  - Affiche les affectations actives

- [x] **Historique des affectations**
  - Repository: `findByCollaborateurWithFilter()`
  - Filtres : date de début et fonction
  - Form: `CollaborateurAffectationFiltreType`

- [x] **Affecter ce collaborateur à un nouveau poste**
  - Check chevauchement dates d'affectation dans `AffectationRepository::isCollaborateurAffecte()`
  - Form: `AffectationToCollaborateurType`

- [x] **Modifier affectations en cours**
  - Vérification: uniquement si `dateFin` null ou future

---

## 3. GESTION DES FONCTIONS

### Liste des fonctions (index)
- [x] **Voir toutes les fonctions**
  - Simple liste avec `findAll()`

- [x] **Bouton création fonction**
  - Form: `FonctionType`

- [x] **Chaque fonction éditable dans le show**
  - Form de modification

---

## 4. RECHERCHE DES AFFECTATIONS

### Liste des affectations (index)
- [x] **Formulaire de recherche et filtrage**
  - Filtres : Ville, période (date de début et de fin) et Par poste (fonction)
  - Composant du filtre : `AffectationFiltreForm`
  - Repository : `AffectationRepository::findAllWithFilter()` avec QueryBuilder

---

## TECHNOLOGIES UTILISÉES

### Backend
- **Symfony 7.3** (PHP 8.4)
- **Doctrine ORM** avec PostgreSQL
- **Symfony Security** (authentification avec `access_control`)
- **KnpPaginatorBundle** v6.9 (pagination 10 items/page)
- **Symfony UX Turbo** v2.31 (navigation, modales de suppression)

### Frontend
- **Twig** (templates)
- **TailwindCSS** (via `symfonycasts/tailwind-bundle` v0.11)
- **Symfony UX Live Component** v2.31 (filtres dynamiques)
- **Symfony UX Autocomplete** v2.31 (ajouter un nouveau collaborateur/restaurant)

### Base de données
- **PostgreSQL**
- **Migrations Doctrine**
- **Fixtures** avec Faker (données de test)

### Tests et Qualité
- **PHPUnit** (tests fonctionnels)

---

## MODÈLE DE DONNÉES

### Entités principales
- **Utilisateur** : email, password, roles (pour l'authentification)
- **Collaborateur** : nom, prénom, email, datePremiereEmbauche, administrateur (bool)
- **Restaurant** : nom, adresse, codePostal, ville
- **Fonction** : intitule
- **Affectation** : collaborateur, restaurant, fonction, dateDebut, dateFin (nullable)

### Relations
- Affectation → ManyToOne → Collaborateur (OneToMany inverse)
- Affectation → ManyToOne → Restaurant (OneToMany inverse)
- Affectation → ManyToOne → Fonction (OneToMany inverse)

---

## POINTS TECHNIQUES AVANCÉS

### Validation des données
- Contraintes Symfony Validator sur entités
- Validation des formulaires côté serveur
- Messages d'erreur en français

### UX/UI
- Messages flash de confirmation: `addFlash('success', 'Message en français')`
- Modales de confirmation pour suppressions via routes `*_delete_confirm`
- Navigation avec Symfony UX Turbo (pas de rechargement complet)
- Design responsive avec TailwindCSS (menu nav jaune/vert)
- Formulaires avec bouton submit intégré dans FormType
- Champs date HTML5: `DateType` avec `widget: 'single_text', html5: true`
- Date picker natif du navigateur pour meilleure UX

---

## POINTS CLÉS

- **Séparation Utilisateur/Collaborateur** : auth vs métier
- **Validation métier** : vérification chevauchement dates dans `AffectationRepository::isCollaborateurAffecte()`
- **Messages en français** : flash messages, labels, erreurs
