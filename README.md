# MyTunesLib

L’application MyTunesLib permet de gérer une bibliothèque musicale à partir d’un compte administrateur.  
Ceci comprend la création de titres avec le/les artiste(s) associé(s), le genre, la durée, la pochette d’album ainsi que le lien YouTube.  
Le mode admin permet aussi l’édition ainsi que la suppression d’un titre.  
MyTunesLib offre également la possibilité de créer des comptes utilisateurs qui pourront alors créer leur playlist avec les titres de leur choix.

L'application utilise le framework Symfony couplé à l'ORM Doctrine et stock ses données via le SGBD MySQL.

# Installation

Pour installer MyTunesLib, suivez les étapes suivantes : 
- clonez ce dépôt dans un dossier dédié
- créez une base de données du nom de votre choix dans votre SGBD MySQL avec un identifiant et un mot de passe d'accès
- renseignez l'adresse de cette base de données ainsi que les identifiants dans le fichier *.env*
- via un terminal, exécutez les commandes *php bin/console make:migration* et *php bin/console doctrine:migrations:migrate* pour créer les tables dans votre base.
- lancez le serveur symfony à l'aide de la commande *symfony server:start*
- rendez-vous sur 127.0.0.1:8000 pour tester le bon fonctionnement de l'application.

# Création d'un compte administrateur

Pour créer un compte administrateur, ouvrez un terminal et tapez la commande suivante :  
*symfony console app:create-admin [nom d'utilisateur] [mot de passe]*

Un message de succès s'affichera pour confirmer la création.  
Vous pouvez à présent essayer de vous connecter sur l'application avec ce compte pour vérifier son bon fonctionnement.

# Documentation technique

La documentation technique de l'application est disponible ici : https://docs.google.com/document/d/1VC7v3-LuduzM02cQunqpPWukmz8blujoTyG1CC0m4ok/edit?usp=sharing
