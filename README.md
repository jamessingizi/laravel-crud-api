## Laravel CRUD API

### Getting started
The project uses Laravel 10 and PHP 8.2 and MySQL for the database.

Development is done in a docker environment. Download docker [here](https://docs.docker.com/get-docker/)

#### Running the API for development
Most of the commands needed to run and develop the app are available as `make` commands listed in `Makefile`. 
If for some reason `make` is not available on your system, you can still run the docker commands directly.

##### Here is a list of some commands to get started:
1. Build containers for local development

```shell
docker-compose -f docker-compose.yml -f docker-compose.local.yml up -d --build
```

the same can be done using `make` as follows:
```shell
make build
```

2. Seeding the database

```shell
docker-compose exec app php artisan db:seed
```
or simply:
```shell
make seed
```

3. Running tests with code coverage report

```shell
docker-compose exec -e XDEBUG_MODE=coverage app php artisan test --coverage
```
or 
```shell
make coverage
```

Other useful commands include `make lint`,`make fix`, `make tail`, `make db`
