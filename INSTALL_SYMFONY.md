# Tuto Symfony - Installation

Ce tutoriel rapide suppose que vous êtes sous Windows, et que Wamp est installé/fonctionnel

## Étape 1 - PHP

* Aller dans `Panneau de Configuration -> Système -> Paramètres Système Avancés -> Variables d'environnement`
* Ajouter `.PHP;` à la fin de la valeur de `PATHEXT` (en selectionnant cette ligne, puis "Modifier" )

* Aller dans le répertoire de Wamp, puis dans `/bin/php/php_numéro_de_la_version` et copier le chemin d'accès de ce répertoire
* Ouvrir un Invite de commande (cmd.exe) en mode Admin, et taper `set PATH=%PATH%;C:\Programmes\Wamp\bin\php\php7.0.10` (par exemple)

* Si la commande ' php -v ' marche dans l'Invite de commande, c'est bon pour PHP ! (Redémarrer l'ordinateur si besoin)

## Étape 2 - Symfony

* Ouvrir un Invite de commande
* Aller dans le répertoire \bin\php => ' cd c:\wamp\bin\php ' (par exemple)

* Exécuter les commandes suivantes :
`php -r "file_put_contents('symfony', file_get_contents('https://symfony.com/installer'));"`
`(echo @ECHO OFF & echo php "%~dp0symfony" %*) > symfony.bat`
`symfony`

* Si tout marche bien, c'est tout bon pour Symfony !

## Étape 3 - Créer un Projet

* Commande permettant de créer un nouveau projet dans le répertoire www\ de Wamp :
`symfony new ..\..\www\NOM_ET_CHEMIN_DU_DOSSIER_A_CREER`

* Ensuite, suivre les instructions à l'écran (aller dans le dossier créé, configurer puis lancer l'app)

# Liens Utiles

[Symfony Setup](https://symfony.com/doc/current/setup.html) (Ce que vous venez de faire, en gros)  
[Démarrer avec Symfony](https://symfony.com/doc/current/page_creation.html) (Il faut commencer par là)