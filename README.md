### Test Technique Symfony / PHP (API Platform)

Stack technique: PHP 8.0.8 / Symfony 5.3.5

Après avoir cloné le projet en local, il faudra exécuter les commandes suivantes:
 - composer install.
Définir le nom de votre base de données dans le fichier .env.local puis
 - php bin/console doctrine:database:create
 - php bin/console doctrine:schema:update --dump-sql
 - php bin/console doctrine:schema:update --force

Pour la création des clés JWT:
 - php bin/console lexik:jwt:generate-keypair 

### POSTMAN

Pour commencer le test technique, j'ai voulu créer un micro service permettant l'upload d'un fichier CSV via postman.
L'URL est disponible à cette adresse: http://127.0.0.1:8000/upload.
Deux vérifications seront effectuées:
 - si aucun fichier n'a été uploadé
 - seuls les fichiers CSV pourront être uploadés.
Dès que le fichier est uploadé, celui-ci est renommé avec la date du jour au format Y-m-d et est stocké dans /public/uploads/CSV

### CRON

Ensuite, pour la création du cron, j'ai voulu faire en sorte d'insérer la liste des pokémons comme si on recevait un fichier hebdomadaire afin de mettre à jour leurs différentes caractéristiques.
La configuration du path du fichier CSV se trouve directement dans la configuration du projet: je trouve çà plus propre personnellement.
Tout d'abord, lorsque le cron est lancé, il y aura une vérification afin de savoir si le fichier existe.
Puis pour faire le traitement, j'ai créer un service FileConvert qui permet tout simplement de lire le fichier CSV et de convertir les données dans un tableau.
Ensuite, à partir d'une boucle foreach, et avant l'insertion de pokémon dans la base de données, j'effectue une vérification des données par le biais de la méthode validatorData:
 - si une erreur est dectectée, un message Invalid data sera affiché avec le champ concerné à corriger.
 - si pas d'erreur, l'insertion ou la mise à jour du pokémon s'effectuera dans la base.
Pour lancer le cron, il faudra taper la commande suivante:
``$ php bin/console import:csv

## Actions sur l'API:
### Inscription:

Pour procéder à l'inscription d'un utilisateur sur l'API, j'ai tout d'abord créer une entité user par le biais de la commande suivante:
``$ php bin/console make:user.
Grâce à cette commande, l'entité User aura les propriétés suivantes par défaut:
- email
- roles
- password.

Ensuite, afin de respecter les différentes contraintes liées à l'inscription d'un utilisateur à l'API, j'ai réussi à mettre en place la gestion des erreurs suivantes:
 - Email doit être un champ unique via une annotation dans l'entité User
 - le champ Email doit être valide grâce à l'annotation @Assert\Email
 - un message d'erreur: Your password must contain at least one uppercase, one lowercase, a special character and be at least 8 characters long grâce à la mise en place d'une regex
 - les champs email et password sont requis.
L'URL est disponible à cette adresse: http://127.0.0.1:8000/api/registration .
Dernière chose, lors de l'inscription, le mot de passe est bien évidemment encodé dans la base de données.

### Connexion:

Grâce aux tutos de Grafikart, j'ai pu mettre en place l'authentification JWT et, une fois connecté, un **token** est généré pour le reste de l'utilisation de l'API.
J'ai également fait en sorte que lorsque le token est généré, qu'il n'y ait pas de query effectuée en base, parce que techniquement un JWT va contenir les informations directement encodées.
Ajout de MeController, qui retournera les informations de l'utilisateur authentifié.
Cette route /api/me sera sécurisée:
 - security: 'is_granted("ROLE_USER")', accessible via bearerAuth.
 
### Show:

Dans l'entité Pokemon, j'ai déclaré l'attribut #[ApiResource()] disant que Pokémon est une ApiResource.
Grâce à çà, automatiquement, les routes CRUD sont créées.
Pour Show, j'ai précisé par le biais du groupe read:item que je souhaite récupérer tous les attributs de l'entité Pokémon.
La route: http://127.0.0.1:8000/api/pokemon/{id} est publique.

### Index:

A l'inverse, pour Index, j'ai précisé par le biais du groupe read:collection que je ne souhaite récupérer que les attributs de l'entité Pokémon faisant partie de ce groupe.
Soit l'id, name, type1, type2 et legendary.
La pagination a été activée côté client afin de pouvoir décider du nombre d'items à afficher (renvoie la liste de Pokémon 50 par 50) par page ainsi que la possibilité de changer de page.
De plus, le filtre permettant de rechercher un pokémon par: 
 - son nom
 - son type
 - sa génération
 - légendaire
fonctionne parfaitement. La aussi, on peut décider du nombre d'items à afficher par page.

### Delete:

La route pour supprimer un pokémon est uniquement accessible que si l'utilisateur est authentifié sur l'API grâce au token.
A partir de l'id, j'effectue d'abord une requête afin de savoir si c'est un pokémon légendaire:
 - Si oui, la suppression est interdite !
 - si non, on supprime le pokémon de la base de données.
 
### Update:

Pour l'update, on ne peut pas updater un pokémon légendaire.
Dans le cas contraire, l'édition fonctionne et on ne peut pas éditer un type qui n'existe pas en base de données.

Que ce soit pour l'Update ou le Delete d'un pokémon, j'ai mis en place un DataPersister et pour ce qui est de la vérification à savoir est que le pokémon que je souhaite updaté / supprimé est-il légendaire?
C'est le UpdatePokemonSubscriber qui s'en occupe en écoutant l'évent PRE_WRITE.

## Axes d'amélioration:

- PHP 8 (maitriser la nouvelle syntaxe)
- API Platform (en cours d'auto-formation)