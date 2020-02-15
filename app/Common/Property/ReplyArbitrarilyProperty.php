<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/15
 * Time: 20:06
 */

namespace App\Common\Property;

use Library\Virtual\Property\AbstractProperty;

/**
 * Class ReplyArbitrarilyProperty
 * @package App\Common\Property
 */
class ReplyArbitrarilyProperty extends AbstractProperty
{
    public $id = 0;

    public $app_id;

    public $pid;

    public $name;

    public $type;

    public $view_url;

    public $mp_app_id;

    public $mp_page_path;

    public $event_key;

    public $weight = 0;

    public $status = 1;

    public $update_time;

    public $create_time;

    /**
     * ReplyArbitrarilyProperty constructor.
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