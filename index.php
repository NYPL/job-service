<?php
require __DIR__ . '/vendor/autoload.php';

use Slim\Http\Request;
use Slim\Http\Response;
use NYPL\Starter\Service;
use NYPL\Services\Controller;
use NYPL\Starter\Config;
use NYPL\Starter\SwaggerGenerator;
use NYPL\Starter\ErrorHandler;

try {
    Config::initialize(__DIR__ . '/config');

    $service = new Service();

    $service->get("/docs/job", function (Request $request, Response $response) {
        return SwaggerGenerator::generate(
            [__DIR__ . "/src", __DIR__ . "/vendor/nypl/microservice-starter/src"],
            $response
        );
    });

    $service->post("/api/v0.1/jobs", function (Request $request, Response $response) {
        $controller = new Controller\JobController($request, $response);
        return $controller->createJob();
    });

    $service->get("/api/v0.1/jobs/{id}", function (Request $request, Response $response, $parameters) {
        $controller = new Controller\JobController($request, $response);
        return $controller->getJob($parameters["id"]);
    });

    $service->put("/api/v0.1/jobs/{id}/start", function (Request $request, Response $response, $parameters) {
        $controller = new Controller\JobController($request, $response);
        return $controller->startJob($parameters["id"]);
    });

    $service->post("/api/v0.1/jobs/{id}/notices", function (Request $request, Response $response, $parameters) {
        $controller = new Controller\JobController($request, $response);
        return $controller->createJobNotice($parameters["id"]);
    });

    $service->put("/api/v0.1/jobs/{id}/success", function (Request $request, Response $response, $parameters) {
        $controller = new Controller\JobController($request, $response);
        return $controller->setJobSuccess($parameters["id"]);
    });

    $service->put("/api/v0.1/jobs/{id}/failure", function (Request $request, Response $response, $parameters) {
        $controller = new Controller\JobController($request, $response);
        return $controller->setJobFailure($parameters["id"]);
    });


    $service->run();
} catch (Exception $exception) {
    ErrorHandler::processShutdownError($exception->getMessage(), $exception);
}
