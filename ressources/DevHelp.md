# DocInTime

## Composer

Télécharger composer:
https://getcomposer.org/download/ (suivre les consignes, ou télécharger ![l'archive de Tom](https://github.com/TBG-FR/IUT_PTUT_DocApp/blob/master/ressources/composer_windows_install.zip) (plus simple))

## Dev Tools

A chaque `pull`, si le fichier composer.json a été modifié, lancer la commande suivante:
- `composer update`

Mettre à jour le schéma SQL :
- `php bin/console doctrine:schema:update --force`
