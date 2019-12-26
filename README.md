# TP noté Banque API

Ce tp a été réalisé à l'aide du framework Laravel.
Vous trouverez la documentation swagger dans ressources/swagger.yaml.

# Quelques informations supplémentaire.

Pour lancer le projet, il faut executer à la racine du projet la commande :
php -S 127.0.0.1:8000 -t ./public

Le controller qui gère les différentes action lié au service de la banque est le BankOpsController dans le dossier app/Htpp/Controllers.
Pour les informations et la connexion utilisateurs, les actions se trouvent principalement dans le AuthController et le UserController (voir aussi dans Middleware/Authenticate).
Les routes d'accès aux différentes ressources se trouvent dans routes/web.php (Détails disponible dans la doc swagger).

Les controllers et fonctions appelé se trouvent dans ./app/Http/Controllers.
Les informations relative à la base de données se trouvent dans le .env, que j'ai volontairement laissé en clair dans le git pour éviter les soucis de configuration.
D'aiileurs la base est en ligne hébergée par alwaysdata avec quelque données pour effectuer des tests.
Les comptes de tests :
        MYBAN5879559B, mdp = passtoto
        MYBAN6896286B, mdp = passtiti
Attention, les requêtes sont former pour prendre les valeurs en params, et non pas en body, par soucis de simplicité pour les tests.
La table user à été fabriquer à l'aide d'artisan et personnalisée comme souhaité, vous trouverez sa description dans database/migrations/2019_11_28_135145_create_user_account_table.php
Le modèle d'un compte utilisateur se trouve dans le fichier app/UserAccount.php.
La configuration de l'authentification se trouve en partie dans config/auth.php. Le reste se situe dans le middleware dans app/Http.
Les packages utilisés sont décrits dans le fichier composer.json à la racine.