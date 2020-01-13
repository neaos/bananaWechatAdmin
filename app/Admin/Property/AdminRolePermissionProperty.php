<?php

namespace App\Admin\Property;

use Library\Virtual\Property\AbstractProperty;

/**
 * Class AdminRolePermissionProperty
 * @package App\Api\Property
 */
class AdminRolePermissionProperty extends AbstractProperty
{
    public $id = 0;

    public $role_id;

    public $permission_list;

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