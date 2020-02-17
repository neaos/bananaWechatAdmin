<?php
namespace App\Common\Service;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/02/17
 * Time: 15:51
 */

class EventService
{
    /**
     * 静态对象
     * @var null
     */
    protected static $instance = null;

    /**
     * 获取实例
     * @return EventService|static
     */
    public static function instance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * 构造函数
     */
    private function __construct()
    {

    }

    /**
     * 克隆函数
     */
    private function __clone()
    {

    }

    /**
     *
     */
    public function refreshEventBuildCache()
    {

    }
}