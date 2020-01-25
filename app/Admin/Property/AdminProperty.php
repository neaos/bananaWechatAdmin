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
 * @package App\Admin\Property
 */
class AdminProperty extends AbstractProperty
{
    public $id = 0;

    public $role_id;

    public $username;

    public $password;

    public $nickname;

    public $platform_id_list;

    public $game_type_id_list;

    public $game_id_list;

    public $status = 1;

    public $last_login_time;

    public $create_time;

    public $update_time;

    /**
     * AdminProperty constructor.
     */
    public function __construct()
    {
        $this->last_login_time = time();
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