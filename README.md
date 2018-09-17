# NYPL Job Service

[![Build Status](https://travis-ci.org/NYPL/job-service.svg?branch=master)](https://travis-ci.org/NYPL/job-service)
[![Coverage Status](https://coveralls.io/repos/github/NYPL/job-service/badge.svg?branch=master)](https://coveralls.io/github/NYPL/job-service?branch=master)

This package is intended to be used as a Lambda-based Node.js/PHP Job Service using the 
[NYPL PHP Microservice Starter](https://github.com/NYPL/php-microservice-starter).

This package adheres to [PSR-1](http://www.php-fig.org/psr/psr-1/), 
[PSR-2](http://www.php-fig.org/psr/psr-2/), and [PSR-4](http://www.php-fig.org/psr/psr-4/) 
(using the [Composer](https://getcomposer.org/) autoloader).

## Requirements

* Node.js >=6.0
* PHP >=7.0 
  * [phpredis extension](https://github.com/phpredis/phpredis/#readme)

Homebrew is highly recommended for PHP:
  * `brew install php71`
  * `brew install php71-redis`
  
## Installation

1. Clone the repo.
2. Install required dependencies.
   * Run `npm install` to install Node.js packages.
   * Run `composer install` to install PHP packages.

## Configuration

Various files are used to configure and deploy the Lambda.

 * `.env` - Lambda configuration common across deployments
 * `package.json` - See "scripts" section for environment specific configuration
 * `./config/[environment].env` - Deployment specific environmental variables

## Usage

### Process a Lambda Event

To use `node-lambda` to process the sample API Gateway event in `event.json`, run:

~~~~
npm run test-create-job
~~~~

### Run as a Web Server

To use the PHP internal web server, run:

~~~~
php -S localhost:8888 -t . index.php
~~~~

You can then make a request to the Lambda: `http://localhost:8888/api/v0.1/jobs`.

### Swagger Documentation Generator

Create a Swagger route to generate Swagger specification documentation:

~~~~
$service->get("/swagger", function (Request $request, Response $response) {
    return SwaggerGenerator::generate(__DIR__ . "/src", $response);
});
~~~~

## Deployment

Travis CI is configured to run our build and deployment process on AWS.

Our Travis CI/CD pipeline will execute the following steps for each deployment trigger:

* Run unit test coverage
* Build Lambda deployment packages
* Execute the `deploy` hook only for `development`, `qa` and `master` branches to adhere to our `node-lambda` deployment process
* Developers _do not_ need to manually deploy the application unless an error occurred via Travis
