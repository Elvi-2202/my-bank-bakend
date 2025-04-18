# my-bank-backend

## Deploy localy
Install the dependances
```
composer install
```

Create the database and make the migrations
```
symfony console doctrine:database:create
symfony console doctrine:migration:migrate
```

Try locally
```
symfony serve
```

## Deploy with Docker

Create a network
```
docker network create symfony-network
```

If needed deploy a myslq container
```
docker run --name symfony-mysql --network symfony-network -p 3306:3306 -e MYSQL_ROOT_PASSWORD=root mysql
```

Change the connection string in the .env line 27 with the container name of mysql container

Build the image and deploy as container
```
docker build . -t my-bank-backend
docker run --name my-bank-backend_container --network symfony-network -p 8085:80 my-bank-backend
```

Create database in mysql container and make the migration
```
docker exec -it my-bank-backend_container php bin/console doctrine:database:create
docker exec -it my-bank-backend_container php bin/console doctrine:migration:migrate
```

## Deploy with Jenkins

If not already done start an instance of jenkins_master
```
docker run --name jenkins -p <choose_a_port>:8080 jenkins/jenkins
```

Then build and start an instance of a jenkins_agent
If your are on Windows, execute this command in Powershell or cmd
```
cd Jenkins-agent
docker build -t jenkins-agent-with-docker-and-composer-my-bank-backend .
docker run --init --name jenkins_agent_composer -v /var/run/docker.sock:/var/run/docker.sock jenkins-agent-with-docker-and-composer-my-bank-backend -url http://172.17.0.2:8080 76cb5e741f24cd78a082be906f29e0f12d125e4f3667bbc2c0dbc6ed8d077968 my-bank-backend
```

Want to try the entire CICD on your own repository and registry ?