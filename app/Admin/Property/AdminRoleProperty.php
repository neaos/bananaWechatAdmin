<?php

namespace App\Admin\Property;

use Library\Virtual\Property\AbstractProperty;

/**
 * Class AdminRoleProperty
 * @package App\Admin\Property
 */
class AdminRoleProperty extends AbstractProperty
{
    public $id = 0;

    public $name;

    public $desc;

    public $state = 1;

    public $create_time;

    public $update_time;

    /**
     * AdminRoleProperty constructor.
     */
    public function __construct()
    {
        $this->update_time = time();
        $this->create_time = time();
    }

    /**
     * 设置属性
     * 可以默认写法
     *
     * public function setProperty(array $params)
     * {
     *    return $this->__setProperty($params);
     * }
     *
     * @param array $params
     * @return $this
     * @throws \Exception
     */
    public function setProperty(array $params)
    {
        return $this->__setProperty($params);
    }
}