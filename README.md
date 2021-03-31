# Air Traffic Control Manager
![System architecture](https://github.com/julianobressan/atcm/blob/main/documents/header_atcm.jpg?raw=true)

The proposal is to implement a basic system to management of air traffic control. The software was developed with pure PHP, using some basic components open source available on http://packagist.org. For persistance of data was chosen MySQL database system. The software was developed
using techniques of clean code and best practices for programming, like PHP Standard Recommendations, Test-driven development and Domain-Driven Design.

## Development environment and resources
For codgin was used IDE VS Code, Insomnia for REST API calls, PHPUnit for testing, XDebug for debugging and Docker for database service.

## Architecture
![System architecture](https://github.com/julianobressan/atcm/blob/main/documents/architecture.png?raw=true)

### Layers

Attending instructions for this challenge, the architecture was implemented with 3 layers.
#### API
It has the implementation of REST API, using Slim Framework and consists in 3 folders:
* **config**:  Configurations about REST API;
* **routes**: It contains all routes of API; and
* **middlewares**: It contains middlewares used for error handling and authentication.

#### Core
This is the logical layer. It contains all classes that have the logical implementation of software, which is the middleware between API and Data layers.
* **Controllers**: These objects are proxies to requests of API, redirecting callings to right services;
* **Helpers**: Some helper classes
* **Exceptions**: Implementation of custom exceptions for this software
* **Services**: Contains the business rules for manipulation of data and responses for controllers. These objects really executes the instructions of software;

#### Data


#### Other files 


### Entity Relationship Diagram
![Entity Relationship Diagram](https://github.com/julianobressan/atcm/blob/main/documents/er-diagram.png?raw=true)

## Third-part packages
It was used the following packages in this software:
* **slim/slim**: A mini-framework to create REST APIs, widely used for this proposal 
* **slim/psr7**: A package required by **slim/slim** to attend PSR-7 and allows to create HTTP message interfaces
* **monolog/monolog**: An excelent package for logging use by main PHP Frameworks currently
* **vlucas/phpdotenv**: A simple package to read environment variables placed in a .env file and registering them globally
* **php-di/php-di**: A package to Dependency Injection, used to create Containers on API REST of **slim/slim**
* **tuupola/slim-jwt-auth**: A package to allow **slim/slim** to authenticate JWT Bearer tokens
* **firebase/php-jwt**: With this package the software is able to create JWT tokens
* **phpunit/phpunit**: The best framework for testing

## Configuring the environment

### Requirements
* PHP 7.4 or newer
* MySQL 8 or newer
* Docker (optional, but recommended)

To run this software, follow instructions above or create a similar environment as you wish. It is recommended using a Linux distro for running.

#### PHP
Make sure you have installed and enabled the following modules:
* PHP-PDO
* mod_rewrite
* PHP-MySQL

#### Web server
You can use the internal web service of PHP, running the command bellow the terminal, running on root project folder:
`php -S localhost:8080 -t public public/index.php`
If you wish, you can change the port to another, but remember of point correctly the API calls in that port.

#### Database

You can use a database installed on your computer or a Docker container. For use of Docker, create a container running the command bellow:

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

## First run (software installation)

## REST API documentation
See above all implemented endpoints, verbs, arguments and body when it is necessary. All body formats are in JSON format. The endpoints that need autentication are marked.

### Flights (queue)

- **GET /queue** 
Returns the actual queue of flights for landing. Returns a JSON array, where each position is similiar with result bellow:
    - Header: Bearer token
    - Response: HTTP 200
```json
[
    {
        "flight": {
            "id": "5",
            "aircraftId": "3",
            "flightType": "passenger",
            "flightNumber": "XF 8619",
            "createdAt": "2021-03-31 00:28:49",
            "updatedAt": null,
            "deletedAt": null
        },
        "aircraft": {
            "id": "3",
            "model": "Antonov An-124",
            "size": "large",
            "createdAt": "2021-03-30 21:09:31",
            "updatedAt": null,
            "deletedAt": null
        }
    }
]
```

* **POST /queue**
Register a new flight, associated with an aircraft and have a type. The types can be: **emergency**, **vip**, **passenger** or **cargo**.
 * Header: Bearer token
 * Body: JSON
```json
{
    "aircratId": "9999",
    "type": "cargo"
}
```
 * Response: HTTP 201

* **DELETE /queue/{flightId}**
Dequeues the first flight on the queue, according rules for priorization implemented. If expects the ID of flight. This parameter is required to avoid that a air traffic controller that are seeing an outdated list in your screen command to dequeue thinking in one flight and the system dequeues another. So, for example, if the controller are seeing the flight ID 9999 and command to dequeue expecting that this flight is to be dequeue, the software checks if at the moment of execution those flight still is the first to be dequeue on queue. If, during this time, another controller dequeued those flight, the system will warn that those flight was already dequeued.
 * Header: Bearer token
 * Response: HTTP 204

### Aircrafts

* **GET /aircraft** 
Gets a list of all aircrafts registeres
 * Header: Bearer token
 * Response: HTTP 200
```json
[
    {
        "id": "1",
        "model": "Antonov An-225 Mriya",
        "size": "large",
        "createdAt": "2021-03-30 21:09:29",
        "updatedAt": null,
        "deletedAt": null
    }
]
```

* **POST /aircraft**
Creates one aircrafto on software. It was expected that shall be provided information abaout **size** of aircraft, which can be **small** or **large**. Also, can be informed the **model** of aircraft (optional). If omitted model, software will take some random model for it.
 * Header: Bearer token
 * Body: JSON
 ```json
 {
    "size": "large",
    "model": "Embraer KC390"
 }

 * Response: HTTP 201
 ```json
 {
    "size": "large",
    "model": "Embraer KC39",
    "id": 15
 }
 ```


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
```json
```
{
    "login": "{string}",
    "password": "{string}"
}
```json
```

## Author's notes

1st Lieutenant Juliano Bressan - Brazilian Air Force Former Officer

![Ten Bressan](https://github.com/julianobressan/julianobressan/blob/master/ten_bressan.jpg?raw=true)