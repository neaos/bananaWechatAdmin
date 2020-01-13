<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/15
 * Time: 20:06
 */
namespace App\Admin\Property;

use Library\Virtual\Property\AbstractProperty;

/**
 * Class AdminProperty
 * @package App\Api\Property
 */
class AdminLogProperty extends AbstractProperty
{
    public $id = 0;

    public $admin_id;

    public $username;

    public $route;

    public $request_data;

    public $response_data;

    public $ip;

    public $create_time;

    public $update_time;

    /**
     * 设置属性
     * 可以默认写法
     * public function setProperty(array $params)
     * {
     *    return $this->__setProperty($params);
     * }
     * @param array $params
     * @return $this
     * @throws \Exception
     */
    public function setProperty(array $params)
    {
        return $this->__setProperty($params);
    }
}