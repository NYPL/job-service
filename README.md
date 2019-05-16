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
   * If you have not already installed `node-lambda` as a global package, run `npm install -g node-lambda`.
3. Setup [configuration files](#configuration).
   * Copy the `.env.sample` file to `.env`.
   * Copy `config/var_env.sample` to `config/var_development.env`.
4. Replace sample values in `.env` and `config/var_development.env`.

## Configuration

Various files are used to configure and deploy the Lambda.

### .env

`.env` is used *locally* for two purposes:

1. By `node-lambda` for deploying to and configuring Lambda in *all* environments. 
   * You should use this file to configure the common settings for the Lambda 
   (e.g. timeout, Node version). 
2. To set local environment variables so the Lambda can be run and tested in a local environment.
   These parameters are ultimately set by the [var environment files](#var_environment) when the Lambda is deployed.

### package.json

Configures `npm run` commands for each environment for deployment and testing. Deployment commands may also set
the proper AWS Lambda VPC, security group, and role.
 
~~~~
"scripts": {
    "deploy-dev": "./node_modules/node-lambda/bin/node-lambda deploy -n JobService -e development -f config/var_development.env -S config/event_sources_dev.json -o arn:aws:iam::224280085904:role/lambda_basic_execution -b subnet-f4fe56af -g sg-1d544067 -P nypl-sandbox",
    "deploy-production": "./node_modules/node-lambda/bin/node-lambda deploy -e production -f config/var_production.env -S config/event_sources_production.json -o 'arn:aws:iam::946183545209:role/lambda-full-access' -b subnet-5deecd15,subnet-59bcdd03 -g sg-116eeb60 -P nypl-digital-dev",
    "test-create-job": "./node_modules/node-lambda/bin/node-lambda run -f config/var_app -j tests/events/create-job.json -x tests/events/context.json"
  },
~~~~

### config/var_app

Configures environment variables common to *all* environments.

### config/var_*environment*.env

Configures environment variables specific to each environment.

### config/event_sources_*environment*

Configures Lambda event sources (triggers) specific to each environment.

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

Deployments (AWS account `nypl-digital-dev`):
 * Production: Lambda > Functions > JobService-production
 * QA: Lambda > Functions > JobService-qa

Our Travis CI/CD pipeline will execute the following steps for each deployment trigger:

* Run unit test coverage
* Build Lambda deployment packages
* Execute the `deploy` hook only for `development`, `qa` and `master` branches to adhere to our `node-lambda` deployment process
* Developers _do not_ need to manually deploy the application unless an error occurred via Travis
