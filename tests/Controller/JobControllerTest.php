<?php

namespace NYPL\Services\Test\Controller;

use NYPL\Services\Controller\JobController;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;

class JobControllerTest extends TestCase
{
    public $fakeJobController;

    public function setUp()
    {
        parent::setUp();

        $this->fakeJobController = new class extends JobController
        {
            public function __construct()
            {
            }

            public function createJob()
            {
                $response = new Response();
                $stubReponse = preg_replace(
                    '/\s/',
                    '',
                    file_get_contents(__DIR__ . '/../Stubs/job-create-response.json')
                );
                $response->getBody()->write($stubReponse);

                return $response;
            }

            public function getJob($id)
            {
                $response = new Response();
                $stubResponse = preg_replace(
                  '/\s/',
                  '',
                  file_get_contents(__DIR__ . '/../Stubs/job-get-response.json')
                );
                $response->getBody()->write($stubResponse);

                return $response;
            }

            public function createJobNotice($id)
            {
                $response = new Response();

                return $response->withStatus(204);
            }

            public function startJob($id)
            {
                $response = new Response();

                return $response->withStatus(204);
            }

            public function setJobSuccess($id)
            {
                $response = new Response();

                return $response->withStatus(204);
            }

            public function setJobFailure($id)
            {
                $response = new Response();

                return $response->withStatus(204);
            }

        };
    }

    /**
     * @covers \NYPL\Services\Controller\JobController::createJob
     */
    public function testCreationOfJob()
    {
        $controller = $this->fakeJobController;

        $response = $controller->createJob();

        $requestData = '{
              "data":
              {
                "id": "1559e793b78b2a0",
                "started": false,
                "finished": false,
                "success": false,
                "notices": null,
                "successRedirectUrl": "",
                "startCallbackUrl": "",
                "successCallbackUrl": "",
                "failureCallbackUrl": "",
                "updateCallbackUrl": ""
              }
            }';

        $createResponse = preg_replace('/\s/', '', $requestData);

        $body = $response->getBody();
        self::assertTrue($response->getStatusCode() == 200);
        self::assertSame($createResponse, $body->__toString());
    }

    /**
     * @covers \NYPL\Services\Controller\JobController::getJob
     */
    public function testGetHoldRequest()
    {
        $controller = $this->fakeJobController;

        $response = $controller->getJob("1559e793b78b2a0");

        $responseData = '{
          "data": {
            "id": "1559e793b78b2a0",
            "started": false,
            "finished": false,
            "success": false,
            "notices": null,
            "successRedirectUrl": "",
            "startCallbackUrl": "",
            "successCallbackUrl": "",
            "failureCallbackUrl": "",
            "updateCallbackUrl": ""
          }
        }';

        $jobResponse = preg_replace('/\s/', '', $responseData);

        $body = $response->getBody();
        self::assertTrue($response->getStatusCode() == 200);
        self::assertSame($jobResponse, $body->__toString());
    }

    /**
     * @covers \NYPL\Services\Controller\JobController::createJobNotice()
     */
    public function testCreateJobNotice()
    {
        $controller = $this->fakeJobController;

        $response = $controller->createJobNotice("1559e793b78b2a0");

        $this->assertTrue($response->getStatusCode() == 204);
    }

    /**
     * @covers \NYPL\Services\Controller\JobController::startJob()
     */
    public function testStartJob()
    {
        $controller = $this->fakeJobController;

        $response = $controller->startJob("1559e793b78b2a0");

        $this->assertTrue($response->getStatusCode() == 204);
    }

    /**
     * @covers \NYPL\Services\Controller\JobController::setJobSuccess()
     */
    public function testSetJobSuccess()
    {
        $controller = $this->fakeJobController;

        $response = $controller->setJobSuccess("1559e793b78b2a0");

        $this->assertTrue($response->getStatusCode() == 204);
    }

    /**
     * @covers \NYPL\Services\Controller\JobController::setJobFailure()
     */
    public function testSetJobFailure()
    {
        $controller = $this->fakeJobController;

        $response = $controller->setJobFailure("1559e793b78b2a0");

        $this->assertTrue($response->getStatusCode() == 204);
    }
}
