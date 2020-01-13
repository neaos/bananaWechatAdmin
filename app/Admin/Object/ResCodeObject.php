<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/15
 * Time: 20:49
 */

namespace App\Admin\Object;

class ResCodeObject
{
    /**
     * @var int $successHttp
     */
    public static $successHttp = 0;

    /**
     * @var int $failHttp
     */
    public static $failHttp = 10002;

    /**
     * @var int $errSign 错误签名
     */
    public static $errSign = 10004;

    /**
     * @var int $errSign 无权限
     */
    public static $noAuth = 10005;

    /**
     * @var int $lostParam 缺少参数
     */
    public static $lostParam = 10006;

    /**
     * @var int $noLogin 无登录
     */
    public static $noLogin = 10007;
}