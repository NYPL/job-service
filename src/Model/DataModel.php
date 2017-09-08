<?php
namespace NYPL\Services\Model;

use NYPL\Starter\Model;
use NYPL\Starter\Model\ModelTrait\DBReadTrait;
use NYPL\Starter\Model\ModelTrait\DBTrait;

abstract class DataModel extends Model
{
    use DBReadTrait, DBTrait;
}
