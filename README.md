# php-graphql-server

This is a working example of a basic [GraphQL](http://graphql.org/) server written in PHP that queries and mutates data stored in a MySQL/MariaDB database. 

It uses the [webonyx/graphql-php](http://webonyx.github.io/graphql-php/) port of Facebook's [Node GraphQL reference implementation](https://github.com/graphql/graphql-js) and the [overblog/dataloader-php](https://github.com/overblog/dataloader-php) port of Facebook's [Node DataLoader](https://github.com/facebook/dataloader).

It includes a `Dockerfile` and `docker-compose.yml` that can be used to run the GraphQL server on your local development machine.

```
git clone https://github.com/jmaxwilson/php-graphql-server.git
cd php-graphql-server
docker-compose up
```

docker-compose will:
1. Build the docker image from the Dockerfile
2. Run composer in the container to pull down all of the following 3rd party dependencies, as well as their related dependencies:
   - webonyx/graphql-php
   - overblog/dataloader-php   
   - ivome/graphql-relay-php
   - guzzlehttp/guzzle
   - phpunit/phpunit
   - mockery/mockery
3. Bring up the Apache web server running the PHP GraphQL web app at http://localhost:8000

To test GraphQL queries and mutations against the GraphQL server, install the [ChromiQL extension for Google Chrome Browser](https://chrome.google.com/webstore/detail/chromeiql/fkkiamalmpiidkljmicmjfbieiclmeij).

Open the ChromiQL extension and set the endpoint to http://localhost:8000/

Using the ChromeiQL extension user interface, try the following query to verify that the server works:

```
query {
  UserList (first: 3)
  {
    id
    username
    date_cr
  }
}
```

Try creating a User with the following mutation:
```
mutation {
  createUser(
    username: "bubiquitous", 
    first_name: "Bob", 
    last_name: "Ubiquitous", 
    email: "bob.ubiquitous@test.com"
  )
  {
    id
  }
}
```

Then re-run the first query to retieve the list of users and see the user you have created.