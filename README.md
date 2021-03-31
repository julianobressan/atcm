# Air Traffic Control Manager
![System architecture](https://github.com/julianobressan/atcm/blob/main/documents/header_atcm.jpg?raw=true)

The proposal is to implement a basic system to management of air traffic control. The software was developed with (almost) pure PHP, using some basic components open source available on http://packagist.org. For persistance of data was chosen MySQL database system. The software was developed
using techniques of clean code and best practices for programming, like PHP Standard Recommendations, Test-driven development and Domain-Driven Design.

## Development environment and resources
The environment of development was composed by VS Code IDE, Insomnia for REST API calls, PHPUnit for testing, XDebug for debugging and Docker for database service.

## Architecture
![System architecture](https://github.com/julianobressan/atcm/blob/main/documents/architecture.png?raw=true)

### Layers

Attending instructions for this challenge, the architecture was implemented with 3 layers. Other architecture patterns were structured in sub-layers.

#### API
It has the implementation of REST API, using Slim Framework and consists in 3 folders:
* **config**:  Configurations about REST API;
* **routes**: It contains all routes of API; and
* **middlewares**: It contains middlewares used for error handling and authentication.

#### Core
This is the logical layer. It contains all classes that have the logical implementation of software, which is the middleware between API and Data layers.
* **Controllers**: These objects are proxies to requests of API, redirecting callings to right services;
* **Helpers**: Some helper classes;
* **Exceptions**: Implementation of custom exceptions for this software;
* **Services**: Contains the business rules for manipulation of data and responses for controllers. These objects really executes the instructions of software; and
* **Interfaces**: Provides interfaces for Core layer objects.

#### Data
This layers contains objects responsible to connect and manipulate the database and represents the entities on software.
* **Enums**: Besides PHP 7.4 do not have enums, here have some classes that enumerates some information used in the domain of software;
* **Interfaces**: Provides interfaces for Data layer objects; 
* **ORM**: Implementing the pattern Object Relational Mapper, it contains the objects to connect with database and perform SQL statements according the main functions related with entities manipulating;
* **Models**: Objects that represents every entity in the software and provides methods to manipulate them, like IModelBase::find(), IModelBase::save(), IModelBase::save(), IModelBase::delete(), among others.

#### Other important files 
* **public/**: This folder contains one only file, index.php, which is the gateway to API REST requests;
* **tests/**: Contais all automated tests of PHPUnit;
* **logs/**: Contains log files for unexpected errors. It could be monitored by a system of log monitoring;
* **system_info/**: Mock of information about the system availability;
* **documents/**: Just some documents of the software;
* **.env**: File with environment variables for software usage;
* **install.php**: Script for installation of the software database; and
* **database.sql**: Initial SQL file.


### Entity Relationship Diagram
![Entity Relationship Diagram](https://github.com/julianobressan/atcm/blob/main/documents/er-diagram.png?raw=true)

## Challenge compliance and considerations

All requirements of exercise were implemented, by some changes were made, according my understanding of the domain. I understood that aircrafts are immutable, like size or other aditional data, like manufacturer, model, weight, etc. The kind of load, proposal of flight or emergency landing need are mutable and can change during the flight. So, I separated aircraft and flight in two entities:
* Aircraft: register an aircraft in the system, with your immutable characteristics. For this software, it is mandatory to provide the size (small or large) and optional to provide a model name;
* Flight: Fligh have an aircraft associated, a schedule, an identification number, a kind/gender and can become an emergency landing need. For this software, it is mandatory to provide a FlightType (emergency, vip, passenger or cargo), an aircraft and optional to provide a flight number;

The queue resumes the flights registered, sorting acording the requirements on exercise description.

System boot, halt or status normally are information that comes from a realtime consulting to Operational System, RPC call or other component. It is inappropriate to store the status of the system on database. So, for this exercise, I created a mock to system status, simulating a verification of status by simply reading a text file placed in system_info folder. Realize that that file is a result of a request for system status that is managed by some complex component.

Operations with flights hypothetically cannot be performed if the system status is not online. To do that, you should boot the system first by calling the due endpoint. Other functions that depends only of database, like creating session, creating or listing aircrafts works even the system is offline, booting or halting.

In the API, all endpoints, except create session, are protected by JWT authentication. So, for use them, you should authenticate first and put the token in the header of request. See more details bellow. Tokens are programmed to expire in 130 minutes, bearing in mind that air traffic controllers can work a maximum of two hours uninterrupted. This way, they have time to log-in until 10 minutes before the beggining of their operation and work for two hours long. If you want to change the expiration time, change the .env file.

For installation of the database, the script checks the checksum of database.sql file. So, do not edit this file before install the database.

Intending to allow auditing, removed objects in fact are not removed, but have a ```deleted_at``` field filled with the date/time of "exclusion". Methods of ORM allows to search deleted objects by passing the argument ```includedDeleted = true```. Also, all entities have ```created_at``` and ```updated_at``` fields, which are filled in the moment of creation and updating, respectively.

### Naming conventions
The names of entities are written always on singular, for models, controllers, routes and database tables. Models and controllers adopts PascalCase convention and database adopts snake_case. The conversion is automatic.

Files that don't use namespacing, in API, are in camelCase. Files and classes that uses namespacing are in PascalCase.

## Third-part packages
It was used the following packages in this software:
* **slim/slim**: A mini-framework to create REST APIs, widely used for this proposal;
* **slim/psr7**: A package required by **slim/slim** to attend PSR-7 and allows to create HTTP message interfaces;
* **monolog/monolog**: An excelent package for logging use by main PHP Frameworks currently;
* **vlucas/phpdotenv**: A simple package to read environment variables placed in a .env file and registering them globally;
* **php-di/php-di**: A package to Dependency Injection, used to create Containers on API REST of **slim/slim**;
* **tuupola/slim-jwt-auth**: A package to allow **slim/slim** to authenticate JWT Bearer tokens;
* **firebase/php-jwt**: With this package the software is able to create JWT tokens; and
* **phpunit/phpunit**: The best framework for testing.

## Configuring the environment

### Requirements
* PHP 7.4 or newer;
* MySQL 8 or newer; and
* Docker (optional, but recommended).

To run this software, follow instructions above or create a similar environment as you wish. It is recommended using a Linux distro for running.

#### PHP
Make sure you have installed and enabled the following modules:
* PHP-PDO;
* mod_rewrite; and
* PHP-MySQL.

#### Web server
You can use the internal web service of PHP, running the command bellow the terminal, running on root project folder:
`php -S localhost:8080 -t public public/index.php`
If you wish, you can change the port to another, but remember of point correctly the API calls in that port.

#### Database

You can use a database installed on your computer or a Docker container. For use of Docker, create a container running the command bellow:

`docker run --name mysql-atcm -p 3306:3306 -e MYSQL_ROOT_PASSWORD=123456 -d mysql`

In next runnings, to up your container before using the software, just run:

`docker start mysql-atcm`

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

Before using the software, you have to clone repo, make some configurations and install the initial database. Follow the instructions:

1. Open your terminal in a desired diretory and lone the repository in your system:
```git clone https://github.com/julianobressan/atcm.git```
2. Create a Docker container to serve the database: `docker run --name mysql-atcm -p 3306:3306 -e MYSQL_ROOT_PASSWORD=123456 -d mysql`
3. Run the installation script: `php install.php`
  - Follow the instructions in your terminal;
  - At the end, you will asked if do you want to delete the install.php script and database.sql file. It is recommended that you do that, but you can skip this step if it is your wish.
4. Start the server, using the internal PHP web server: `php -S localhost:8080 -t public public/index.php`
5. Import in your Insomnia application the [JSON file with endpoints](https://github.com/julianobressan/atcm/blob/de796950eb28a991ba24e6ae3a259bdd8150f158/documents/insomnia_endpoints.json);
6. In Insomnia, run the request **Session/Create session**. Fill the body with login and password you provided in step 3. Copy returned token, click on Development environment then in Manage Environments, or simply press Ctrl+E. Fill the value of token key with copied token. It will be used to authenticate all required requests with the Bearer JWT token;
7. Explore the API.

## REST API documentation
See above all implemented endpoints, verbs, arguments and body when it is necessary. All body formats are in JSON format. The endpoints that need autentication are marked.

### Flights (queue)

- **GET /queue** 
Returns the actual queue of flights for landing. Returns a JSON array, where each position is similiar with result bellow:
  - Header: Bearer token
  - Response: HTTP 200
  ```json
  Example
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

- **POST /queue**
Register a new flight, associated with an aircraft and have a type. The types can be: **emergency**, **vip**, **passenger** or **cargo**.
  - Header: Bearer token
  - Body: JSON
  ```json
  Example
  {
    "aircratId": "9999",
    "type": "cargo"
  }
  ```
  - Response: HTTP 201

* **DELETE /queue/{flightId}**
Dequeues the first flight on the queue, according rules for priorization implemented. It expects the ID of flight. This parameter is required to prevent that an air traffic controller that are seeing an outdated list in your screen command to dequeue thinking in one flight and the system dequeues another. So, for example, if the controller are seeing the flight ID 9999 and command to dequeue expecting that this flight is to be dequeue, the software checks if at the moment of execution those flight still is the first to be dequeue on queue. If, during this time, another controller dequeued those flight, the system will warn that those flight was already dequeued.
  - Header: Bearer token
  - Response: HTTP 204

### Aircrafts

- **GET /aircraft** 
Gets a list of all aircrafts registeres
  - Header: Bearer token
  - Response: HTTP 200
  ```json
  Example
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

- **POST /aircraft**
Creates one aircrafto on software. It was expected that shall be provided information abaout **size** of aircraft, which can be **small** or **large**. Also, can be informed the **model** of aircraft (optional). If omitted model, software will take some random model for it.
  - Header: Bearer token
  - Body: JSON
  ```json
  Example
  {
    "size": "large",
    "model": "Embraer KC390"
  }
  ```
  - Response: HTTP 201
  ```json
  Example
  {
    "size": "large",
    "model": "Embraer KC39",
    "id": 15
  }
  ```

### Session
- POST /session
  - Header: Bearer token
  - Body: JSON
  ```json
  Example
  {
    "login": "admin",
    "password": "123456"
  }
  ```
  - Response: HTTP 201
  ```json
  Example
  {
    "token": "some_token"
  }
  ```
## Next steps
Attending the deadline, some enhancements stayed out. But, if I had time, I would want implement the following:
* A query builder for ORM;
* CRUD of users, roles and permissions;
* CRUD of configurations of the system;
* Auditing table and artifacts, for all actions of users in the system;
* More routes, placing them in different router files, adopting closures for that;
* Catalog of custom error codes for troubleshooting;
* Configure Monolog sending to administrator email critical errors; and
* An user interface for autenticating and management of aircrafts, flights queue and view auditing logs, with different ACLs, like "controller" and "supervisor".

## Author's notes

1st Lieutenant Juliano Bressan is a Brazilian Air Force Former Officer, graduate in information systems and acting as Software Engineer since 2008 and acting in many managerial roles since then. During 8 years, acted in Brazilian Air Force, developing software and managing IT services for Air Force organizations in several places of Brazil. Curiously, between supported organizations, were air traffic control centers (in Brazil, Air Force is the responsible for air traffic control).

![Ten Bressan](https://github.com/julianobressan/julianobressan/blob/master/ten_bressan.jpg?raw=true)
