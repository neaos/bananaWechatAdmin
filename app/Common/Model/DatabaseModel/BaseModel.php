<?php

namespace App\Common\Model\DatabaseModel;

use Library\Virtual\Model\DatabaseModel\AbstractMySqlModel;

/**
 * Created by PhpStorm.
 * User: ZhongHao-Zh
 * Date: 2019/10/26
 * Time: 20:18
 */
abstract class BaseModel extends AbstractMySqlModel
{
    /**
     * @var array $appIdList
     */
    private $appIdList = [];

    /**
     * @param array $appIdList
     */
    public function setAppIdList(array $appIdList)
    {
        $this->appIdList = $appIdList;
    }

    /**
     * @return array
     */
    public function getAppIdList()
    {
        return $this->appIdList;
    }

    /**
     * BaseModel constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}