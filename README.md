# Sandbox Symfony

## Requirement

```
docker
docker-compose
```
## Launch

```
docker-compose up -d
```
## Install

```
docker exec -it symfony-sandbox_php_1 composer install
docker exec -it symfony-sandbox_php_1 bin/console doctrine:migration:migrate -n
```
