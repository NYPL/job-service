<?php
namespace NYPL\Services\Model\Response\SuccessResponse;

use NYPL\Services\Model\CacheModel\BaseJob\Job;
use NYPL\Starter\Model\Response\SuccessResponse;

/**
 * @SWG\Definition(title="JobResponse", type="object")
 */
class JobResponse extends SuccessResponse
{
    /**
     * @SWG\Property
     * @var Job
     */
    public $data;
}
