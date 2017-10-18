<?php
namespace NYPL\Services\Controller;

use NYPL\Starter\CacheModel\BaseJob\Job;
use NYPL\Starter\CacheModel\JobNotice\JobNoticeCreated;
use NYPL\Starter\CacheModel\JobStatus;
use NYPL\Starter\CacheModel\JobStatus\JobSuccessStatus;
use NYPL\Services\Model\Response\SuccessResponse\JobResponse;
use NYPL\Starter\APIException;
use NYPL\Starter\Controller;

class JobController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/v0.1/jobs",
     *     summary="Create a new Job",
     *     tags={"jobs"},
     *     operationId="createJob",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="NewJob",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(ref="#/definitions/NewJob"),
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/JobResponse")
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Generic server error",
     *         @SWG\Schema(ref="#/definitions/ErrorResponse")
     *     )
     * )
     */
    public function createJob()
    {
        $job = new Job($this->getRequest()->getParsedBody());

        $job->create();

        return $this->getResponse()->withJson(
            new JobResponse($job)
        );
    }

    /**
     * @SWG\Get(
     *     path="/v0.1/jobs/{id}",
     *     summary="Get a Job",
     *     tags={"jobs"},
     *     operationId="getJob",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of Job",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *         format="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/JobResponse")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not found",
     *         @SWG\Schema(ref="#/definitions/ErrorResponse")
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Error",
     *         @SWG\Schema(
     *            type="array",
     *            @SWG\Items(ref="#/definitions/ErrorResponse")
     *         ),
     *     )
     * )
     */
    public function getJob($id)
    {
        $job = new Job();

        $job->read($id);

        return $this->getResponse()->withJson(
            new JobResponse($job)
        );
    }

    /**
     * @SWG\Post(
     *     path="/v0.1/jobs/{id}/notices",
     *     summary="Create a new Job Notice",
     *     tags={"jobs"},
     *     operationId="createJobNotice",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of Job",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *         format="string"
     *     ),
     *     @SWG\Parameter(
     *         name="JobNotice",
     *         in="body",
     *         required=true,
     *         description="",
     *         @SWG\Schema(ref="#/definitions/JobNotice"),
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Successful operation"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Error"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not found",
     *         @SWG\Schema(ref="#/definitions/ErrorResponse")
     *     )
     * )
     */
    public function createJobNotice($id)
    {
        $jobNotice = new JobNoticeCreated($this->getRequest()->getParsedBody(), false, true);

        $job = new Job();
        $job->read($id);

        $job->addNotice($jobNotice);

        $job->update();

        return $this->getResponse()->withStatus(204);
    }

    /**
     * @SWG\Put(
     *     path="/v0.1/jobs/{id}/start",
     *     summary="Start a Job",
     *     tags={"jobs"},
     *     operationId="startJob",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of Job",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *         format="string"
     *     ),
     *     @SWG\Parameter(
     *         name="JobStatus",
     *         in="body",
     *         description="",
     *         @SWG\Schema(ref="#/definitions/JobStatus"),
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Successful operation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not found",
     *         @SWG\Schema(ref="#/definitions/ErrorResponse")
     *     ),
     *     @SWG\Response(
     *         response="409",
     *         description="Already started"
     *     )
     * )
     */
    public function startJob($id)
    {
        $jobStatus = new JobStatus($this->getRequest()->getParsedBody(), false, true);

        $job = new Job();
        $job->read($id);

        if ($job->lock($id, $jobStatus)) {
            if ($jobStatus->getCallBackUrl()) {
                $job->setStartCallbackUrl($jobStatus->getCallBackUrl());
            }

            $job->setStarted(true);

            $job->update();

            return $this->getResponse()->withStatus(204);
        }

        throw new APIException('Job (' . $job->getId() . ') has already started', [], 0, null, 409);
    }

    /**
     * @SWG\Put(
     *     path="/v0.1/jobs/{id}/success",
     *     summary="Set a Job to success",
     *     tags={"jobs"},
     *     operationId="setJobSuccess",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of Job",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *         format="string"
     *     ),
     *     @SWG\Parameter(
     *         name="JobSuccessStatus",
     *         in="body",
     *         description="",
     *         @SWG\Schema(ref="#/definitions/JobSuccessStatus"),
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Successful operation"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Error"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not found",
     *         @SWG\Schema(ref="#/definitions/ErrorResponse")
     *     )
     * )
     */
    public function setJobSuccess($id)
    {
        $jobStatus = new JobSuccessStatus($this->getRequest()->getParsedBody(), false, true);

        $job = new Job();
        $job->read($id);

        if ($job->unlock($id, $jobStatus)) {
            if ($jobStatus->getSuccessRedirectUrl()) {
                $job->setSuccessRedirectUrl($jobStatus->getSuccessRedirectUrl());
            }

            if ($jobStatus->getCallBackUrl()) {
                $job->setStartCallbackUrl($jobStatus->getCallBackUrl());
            }

            $job->setFinished(true);
            $job->setSuccess(true);

            $job->update();

            return $this->getResponse()->withStatus(204);
        }

        if ($job->isFinished()) {
            throw new APIException('Job (' . $job->getId() . ') has already finished', [], 0, null, 400);
        }

        throw new APIException('Job (' . $job->getId() . ') has not been started', [], 0, null, 400);
    }

    /**
     * @SWG\Put(
     *     path="/v0.1/jobs/{id}/failure",
     *     summary="Set a Job to failure",
     *     tags={"jobs"},
     *     operationId="setJobFailure",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of Job",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *         format="string"
     *     ),
     *     @SWG\Parameter(
     *         name="JobStatus",
     *         in="body",
     *         description="",
     *         @SWG\Schema(ref="#/definitions/JobStatus"),
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Successful operation"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Error"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not found",
     *         @SWG\Schema(ref="#/definitions/ErrorResponse")
     *     )
     * )
     */
    public function setJobFailure($id)
    {
        $jobStatus = new JobStatus($this->getRequest()->getParsedBody(), false, true);

        $job = new Job();
        $job->read($id);

        if ($job->isSuccess()) {
            throw new APIException('Job (' . $job->getId() . ') has already succeeded', [], 0, null, 400);
        }

        if ($job->unlock($id, $jobStatus)) {
            if ($jobStatus->getCallBackUrl()) {
                $job->setStartCallbackUrl($jobStatus->getCallBackUrl());
            }

            $job->setFinished(true);
            $job->setSuccess(false);

            $job->update();

            return $this->getResponse()->withStatus(204);
        }

        if ($job->isFinished()) {
            throw new APIException('Job (' . $job->getId() . ') has already finished', [], 0, null, 400);
        }

        throw new APIException('Job (' . $job->getId() . ') has not been started', [], 0, null, 400);
    }
}
