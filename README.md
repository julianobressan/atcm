# atcm
An Air Traffic Control Manager

The proposal is to implement a basic system to management of air traffic control. The software was developed with pure PHP, using some basic components open source available on http://packagist.org. For persistance of date was chosen MySQL database system. The software was developed
using techniques of clean code and best practices for programming, like PHP Standard Recommendations, Test-driven development and Domain-Driven Design.

## Requirements

* PHP 7.4 or newer
* MySQL 8
* Docker (recommended, but optional)

## Third-part packages

It was used the following packages in this software:
* **slim/slim**: A mini-framework to create REST APIs, widely used for this proposal 
* **slim/psr7**: A package required by **slim/slim** to attend PSR-7 and allows to create HTTP message interfaces
* **monolog/monolog**: An excelent package for logging use by main PHP Frameworks currently
* **vlucas/phpdotenv**: A simple package to read environment variables placed in a .env file and registering them globally
* **php-di/php-di**: A package to Dependency Injection, used to create Containers on API REST of **slim/slim**
* **tuupola/slim-basic-auth**:
* **tuupola/slim-jwt-auth**: A package to allow **slim/slim** to authenticate JWT Bearer tokens
* **firebase/php-jwt**: With this package the software is able to create JWT tokens

## Configuring the environment

To run this software, follow instructions above or create a similar environment as you wish. It is recommended using a Linux distro for running.
### PHP
Make sure you have installed and enabled the following modules:
* PDO
* Rewrite
* PHP-MySQL
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

## REST API

### Queue

* GET /queue
* POST /queue/
    {
        "aircratId": "9999"
    }
* DELETE /queue/{aicraftId}

### Aircraft

* GET /aircraft
* GET /aircraft/{id}
* POST /aircraft/
    {
        "type": "{string}",
        "size": "{string}",
        "model": "{string}",
        "flightNumber": "{string}"
    }
* DELETE /aircraft/{aicraftId}

### User

* GET /user
* GET /user/{id}
* POST /user/
    {
        "login": "{string}",
        "name": "{string}",
        "password": "{string}"
    }
* DELETE /user/{aicraftId}

### Session
* POST /session
{
    "login": "{string}",
    "password": "{string}"
}
* DELETE /session/{token}

