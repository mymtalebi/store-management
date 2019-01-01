# Mini Store Branch management App Based on Lumen PHP Framework


This is a mini app that provides RESTful APIs to support manipulating store branches in a tree data structure. The trees can have infinite level.


## Design decisions
* **Lumen 5.7** PHP micro-framework is used for implementation which is best for blazing fast APIs. Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).
* **MariaDB** database is chosen as it has better query optimisation over MySQL.
* **Nested Set** used to handle heirarical statucture of branches. Nested sets or Nested Set Model is a way to effectively store hierarchical data in a relational table. My assumption was that there would be much more reading store branches data from database than writing to it. Therefore, Nested Set Model is used as it is best for this purpose. In this Model a node with all its children can be retrieved from database in a single query. However, inserting and deleting nodes are expensive as they require updating other nodes as well. More in depth explanatin can be found on [Wikipedia page](https://en.wikipedia.org/wiki/Nested_set_model).


## Provided APIs
1. GET /store
   * View all store branches with all of their children.
2. GET /store/{id}
   * View one specific store branch without any children.
3. GET /store/{id}?branches=1
   * View one specific store branch with all of its children.
4. POST /store
   * Form data: name, parent_id
   * Create a store branch.
5. PUT /store/{id}
   * Form data: name, parent_id
   * Update a store branch. Providing parent_id for this endpoint will move the branch node with all of its children to the given parent node. Parent store cannot be a descendent of the store itself.


## Assumptions
* Branch names are unique.
* Branches can only move to another branch, not to become root.


## Requirements to run the application
Following need to be installed in order to get and run the application on your machine:
1. Git
2. Docker
3. Docker-compose


## Steps to setup
```bash
$ git clone git@github.com:mymtalebi/store-management.git
$ cd store-management
$ docker-compose up
```


> Note that running `docker-compose up` for the first time will take some time as it needs to download docker images and project dependencies. But next times would be quick.


## Sample endpoint calls
```bash
$ curl --data "name=Main Branch" localhost/store
{"data":{"name":"Main Branch","id":1}}

$ curl --data "name=Child Branch 1&parent_id=1" localhost/store
{"data":{"name":"Child Branch 1","id":2}}

$ curl localhost/store/1
{"data":{"id":1,"name":"Main Branch"}}

$ curl "localhost/store/1?branches=1"
{"data":{"id":1,"name":"Main Branch","branches":[{"id":2,"name":"Child Branch 1","branches":[]}]}}
```

## Run tests
Make sure docker-compose is up and then run the following command in terminal:
```bash
$ docker exec -it app ./bin/phpunit
```

> Note that written tests are just some samples to showcase my knowledge of writing tests. They do not provide full covarage for the application due to timing considerations.


## Setup local dev environment
You need `make`, `wget` and `php 7.2` instaled locally for this part.

```bash
$ make setup-local
```
What this command does is to install composer and following pre-commit githooks:
1. PHP lint
2. PHP Mess Detector
3. PHP coding style checkers
4. A hook to run tests

Also there is a `make` target called `format` which fixes most of the coding style mistakes.
```bash
$ make format
```


## application security
The application is secure againt common web security issues such as Mysql Injection as its using Lumen framework features. Moreover, docker is used to setup environment for running the application which separates code from infrastructure.

## Improvements to be made
One of the main requirements of the project was to get a store branch with all of its children or to get all the store branches in one call. However, as the stores can have infinite level, I believe it will be more efficient to limit the level of the children of the store retrieved in each call.

In order to improve the write performance of Nested Set we can use a big gap between left an right values of each node. For instance, instead of increasing by one we can increase by 100. As a result we will need less writes to thr tree on insertion or move unless the gap is filled.

The other improvement that can be made is persisting data. In the composer setup I have set up database to run in a container and it is not linked to a persisted database volume. This means after each `docker-compose down` command the data will be lost. A persisted database can be set up and linked to the application via volume configuration in docker-compose.yml file.

Also, It would be good to have the APIs documented using Open API specs([Swagger](https://swagger.io)).

## License

This app is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
