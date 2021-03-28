# atcm
An Air Traffic Control Manager

## Configuring the environment

### PHP

### Web server

### Database

For this challenge, it was used a MySQL database. You can use a database installed on your computer or a Docker container. For use of Docker, create a container running the command bellow:

`docker run --name mysql-atcm -p 3306:3306 -e MYSQL_ROOT_PASSWORD=123456 -d mysql`

This way, the correct environment variables file is already filled with the credentials above. If you do prefer, you can change the name of container, password and port to others. Make sure to edit the `.env` file with new configurations. Example:

`docker run --name mycontainer -p 4306:3306 -e MYSQL_ROOT_PASSWORD=mypassword -d mysql`

```
DB_TYPE="mysql"
DB_HOST="127.0.0.1"
DB_PORT="4306"
DB_NAME="atcm"
DB_USER="root"
DB_PASS="mypassword"
```

In your first run, the program will create the database schema. The name for schema will be **atcm**. If you do want to use another name for schema, edit the `.env` file before running. If you prefer run the database installed directly in your host system or another host, ensure that correct credentials are filled on .env file and database user have permissions to create databases and tables, select, insert, deleted and update statements.