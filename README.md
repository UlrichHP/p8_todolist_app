# Projet 8 - Améliorez une application existante de ToDo & Co

Ce projet a pour but d'améliorer l'application ToDoList créée par [Saro0h](https://github.com/saro0h/projet8-TodoList) pour OpenClassrooms.

The purpose of this project is to enhance the ToDoList application created by [Saro0h](https://github.com/saro0h/projet8-TodoList) for OpenClassrooms.

## Installation

Clonez le repository GitHub et tapez les commandes suivantes :
- Entrez vos identifiants de connexion à la base de données dans app/config/parameters.yml
- composer install
- php bin/console doctrine:database:create
- php bin/console doctrine:schema:create
- php bin/console doctrine:fixtures:load

Clone the GitHub repository and execute the following commands :
- Enter your database settings in app/config/parameters.yml
- composer install
- php bin/console doctrine:database:create
- php bin/console doctrine:schema:create
- php bin/console doctrine:fixtures:load

## OldTasks Command

La commande pour lier un utilisateur anonyme à une ancienne tâche est :
- php bin/console OldTasks

The Command to link an anonymous user to old tasks is :
- php bin/console OldTasks

## Documentation
