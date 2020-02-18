<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/02/18
 * Time: 11:25
 */

namespace App\Api\Service;

use App\Api\Object\WeChatResponseObject;
use App\Common\Model\CacheModel\OfficialAccountModel;
use App\Common\Model\DatabaseModel\AutoReplyModel as AutoReplyDbModel;
use App\Common\Model\CacheModel\AutoReplyModel as AutoReplyCacheModel;
use Illuminate\Support\Collection;

class WeChatApiService
{
    /**
     * @var WeChatApiService $init
     */
    private static $init;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return WeChatApiService
     */
    public static function instance()
    {
        if (!self::$init) {
            self::$init = new static();
        }
        return self::$init;
    }

    /**
     * 确保响应微信发送的信息
     */
    public function successHandle()
    {
        exit('success');
    }

    /**
     * 响应微信发送的信息
     * @param  array|string $contentData 回复信息，文本信息为string类型
     * @param  string $type 消息类型必传WeChatResponseObject的成员变量
     */
    public function response($fromUserName, $toUserName, $contentData, $type)
    {
        exit(xml_encode(WeChatResponseService::instance()->make(
            $fromUserName,
            $toUserName,
            $contentData,
            $type
        )));
    }


    /**
     * 转发到客服系统
     */
    public function transferToCustomerService($fromUserName, $toUserName, $content)
    {
        $this->response($fromUserName, $toUserName, $content, WeChatResponseObject::MSG_TYPE_TRANSFER);
    }

    /**
     * @param $shortName
     * @return string
     */
    public function getAppidByShortName($shortName)
    {
        $cacheModel = new OfficialAccountModel();
        $list = $cacheModel->getOfficialAccountList();
        if ($list) {
            $dbModel = new AutoReplyDbModel();
            $list = $dbModel->getList();
            if ($list) {
                $cacheModel->setOfficialAccountList($list);
            } else {
                $cacheModel->setOfficialAccountList([0]);
            }
        }
        foreach ($list as $key => $value) {
            if ($value->short_name == $shortName) {
                return $value->app_id;
            }
        }
        return '';
    }

    /**
     * @param $appid
     * @return array|Collection
     */
    public function getAutReplyListByAppId($appid)
    {
        $cacheModel = new AutoReplyCacheModel();
        $list = $cacheModel->getAutReplyListByAppId($appid);
        if ($list) {
            $dbModel = new AutoReplyDbModel();
            $list = $dbModel->getList(['app_id' => $appid]);
            if (!$list) {
                $list = [0];
            }
            $cacheModel->setAutoReplyListByAppId($appid, $list);
        }
        if ($list = [0]) {
            return [];
        } else {
            return $list;
        }
    }
}