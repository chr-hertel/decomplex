deComplex
=========

Slim web interface to diff complexity of two code snippets hosted on [decomplex.me](https://decomplex.me).

![Screenshot](/public/screenshot.png)


Setup
-----

**Requirements**

* PHP 8.2
* NPM
* Composer
* Docker Compose
* Symfony CLI

**Steps**

```bash
git clone git@github.com:chr-hertel/decomplex.git
cd decomplex
composer install
npm install
npm run dev
docker-compose up -d
symfony console doctrine:database:create
symfony console doctrine:migration:migrate --no-interaction
symfony console doctrine:database:create --env=test
symfony console doctrine:migration:migrate --env=test --no-interaction
symfony serve -d
```
