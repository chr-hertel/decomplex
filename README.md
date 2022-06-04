Complexity Diff
===============

Slim web interface to diff complexity of code snippets

Setup
-----

**Requirements**

* PHP 8.1
* NPM
* Composer
* Docker Compose
* Symfony CLI

**Steps**

```bash
$ git clone git@github.com:chr-hertel/decomplex.git
$ cd decomplex
$ composer install
$ npm install
$ npm run dev
$ docker-compose up -d
$ symfony console doctrine:database:create
$ symfony console doctrine:database:create --env=test
$ symfony console doctrine:schema:update --force
$ symfony console doctrine:schema:update --force --env=test
$ symfony serve -d
```
