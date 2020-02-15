<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/26
 * Time: 20:05
 */

namespace App\Api\Service;

class OfficialAccountInfoService
{
    /**
     * @var OfficialAccountInfoService $init
     */
    private static $init;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return OfficialAccountInfoService
     */
    public static function instance()
    {
        if (!self::$init) {
            self::$init = new static();
        }
        return self::$init;
    }

    public function getInfoByKey(string $key): array
    {
        $info = [];
        return $info;
    }
}