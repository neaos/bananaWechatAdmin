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
class AdminProperty extends AbstractProperty
{
    public $id = 0;

    public $username;

    public $nickname;

    public $password;

    public $role_id;

    public $create_time;

    public $update_time;

    public $last_login_time;

    public $game_id_list;

    public $platform_id_list;

    public $game_type_id_list;

    public $status;

    public $permission = [];

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