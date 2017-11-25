# PTUT6_DocApp
PTUT #6 - Système de prise de rendez-vous médicaux en Urgence

Télécharger composer:
https://getcomposer.org/download/ (suivre les consignes, ou checker ![l'archive de Tom](https://github.com/TBG-FR/IUT_PTUT_DocApp/blob/master/ressources/composer_windows_install.zip) (plus simple))

A chaque pull, si le fichier composer.json a été modifier, lancer la commande suivante:
- `composer update`

Mettre à jour le schéma SQL :
- `php bin/console doctrine:schema:update --force`
