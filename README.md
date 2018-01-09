# Projet 8 - Améliorez une application existante de ToDo & Co

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/931b45655ca141aea9f99d5599b6bc13)](https://www.codacy.com/app/Maxxxiimus92/p8_todolist_app?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Maxxxiimus92/p8_todolist_app&amp;utm_campaign=Badge_Grade)
[![Build Status](https://travis-ci.org/Maxxxiimus92/p8_todolist_app.svg?branch=master)](https://travis-ci.org/Maxxxiimus92/p8_todolist_app)

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

## Tests

Avant de lancer les tests, vous devez créer la base de données et ses tables avec les commandes suivantes :

- php bin/console doctrine:database:create --env=test
- php bin/console doctrine:schema:create --env=test

Vous pouvez ensuite lancer les tests avec la commande :

- vendor/bin/phpunit (commande à adapter en fonction de votre installation de PHPUnit)

Le rapport de couverture de code est disponible dans [tests/CodeCoverage/index.html](https://github.com/Maxxxiimus92/p8_todolist_app/blob/master/tests/CodeCoverage/index.html)

Before running the tests, you must create the test database and schema with the following commands :

- php bin/console doctrine:database:create --env=test
- php bin/console doctrine:schema:create --env=test

Then, you can launch the tests with the command :

- vendor/bin/phpunit (command to adapt if you installed PHPUnit differently)

The code coverage report is available in [tests/CodeCoverage/index.html](https://github.com/Maxxxiimus92/p8_todolist_app/blob/master/tests/CodeCoverage/index.html)

## Documentation

- [Authentication : User guide](https://github.com/Maxxxiimus92/p8_todolist_app/blob/master/docs/Authentication.md)
- [Comment Participer / How to Contribute](https://github.com/Maxxxiimus92/p8_todolist_app/blob/master/docs/Contribute.md)
