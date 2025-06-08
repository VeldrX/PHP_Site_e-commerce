# Projet PHP E-Commerce - php\_exam

Bienvenue sur notre projet de site e-commerce réalisé en PHP natif dans le cadre de notre évaluation finale du module PHP. Ce site permet aux utilisateurs de vendre, acheter, et gérer des articles en ligne.

## 🎓 Membres du groupe

* Mathis Vassy
* Anthony Pereira
* Nicola Galmiche

## 📚 Fonctionnalités principales

Le site contient les pages et fonctionnalités suivantes :

### Authentification

* `/register` : inscription avec mail et username unique. Connexion automatique après inscription.
* `/login` : connexion et redirection vers la page d'accueil.

### Pages principales

* `/` : page d'accueil affichant tous les articles en vente (les plus récents en premier).
* `/sell` : ajout d'un nouvel article en vente.
* `/detail?id=ARTICLE_ID` : détails d'un article avec bouton "ajouter au panier".
* `/cart` : affichage du panier de l'utilisateur avec possibilité de modifier les quantités ou de supprimer des articles.
* `/cart/validate` : validation de la commande (vérification du solde, saisie des infos de facturation, création de facture).
* `/edit?id=ARTICLE_ID` : modification ou suppression d'un article (seul l'auteur ou un admin peut le faire).
* `/account` : page compte de l'utilisateur connecté, permettant :

  * d'afficher ses articles
  * d'afficher ses achats et factures
  * de modifier ses infos (email, mot de passe)
  * d'ajouter de l'argent à son solde
* `/account?id=USER_ID` : page publique d'un utilisateur (affichage de ses articles).

### Espace administrateur

* `/admin` : accessible uniquement aux admins, permet de :

  * Voir et modifier tous les articles
  * Voir et modifier tous les utilisateurs

### Accès restreint

* Seules les pages `/` et `/detail` sont accessibles sans être connecté.
* Les autres pages redirigent automatiquement vers `/login` si l'utilisateur n'est pas authentifié.

## 📊 Base de données

Nom de la base : `php_exam_db`

Tables principales :

* `user` : id, username, password (bcrypt), email, solde, photo, rôle
* `article` : id, nom, description, prix, date, auteur\_id, image
* `stock` : id, article\_id, quantite
* `cart` : id, user\_id, article\_id, quantite
* `invoice` : id, user\_id, date, montant, adresse, ville, code\_postal

Un export SQL est fourni dans le fichier `php_exam_db.sql`


## 💪 Technologies utilisées

* PHP
* MySQL via PhpMyAdmin
* HTML / CSS (style simple, axé sur la fonctionnalité)
* Serveur local via XAMPP / MAMP / LAMP

## 🌐 Installation locale

1. Installer XAMPP (ou MAMP/LAMP selon votre OS)
2. Démarrer Apache et MySQL
3. Cloner ce repo dans `htdocs` (ou le dossier web de votre stack)

```bash
git clone https://github.com/VeldrX/PHP_Site_e-commerce php_exam
```

4. Importer la base de données via PhpMyAdmin :

   * Aller sur `localhost/phpmyadmin`
   * Créer une base `php_exam_db`
   * Importer le fichier `php_exam_db.sql`
5. Configurer la connexion à la BDD  :

```php
$mysqli = new mysqli("localhost", "root", "root", "php_exam_db");
```

(Si besoin, remplacez le mot de passe par "" si vous n'avez pas mis de mot de passe MySQL)
6. Accéder à l'application via [http://localhost/php\_exam](http://localhost/php_exam)

## 🚨 Remarques importantes

* Aucun framework n’a été utilisé, le projet est 100% PHP natif
* Le fichier SQL est inclus : `php_exam_db.sql`



Merci de votre attention et bon test du site 😊
