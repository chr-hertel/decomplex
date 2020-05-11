Complexity Diff
===============

Slim web interface to diff complexity of code snippets

Setup
-----

```bash
$ git clone git@github.com:chr-hertel/complexity-diff.git
$ cd complexity-diff
$ composer install
$ npm install
$ npm run dev
$ docker-compose up -d
$ bin/console doctrine:database:create
$ bin/console doctrine:schema:update --force
$ symfony serve -d
```
