<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/28
 * Time: 19:18
 */

namespace App\Api\Controller;

use App\Api\Object\WeChatResponseObject;
use App\Api\Service\WeChatResponseService;
use Library\Virtual\Controller\AbstractController;

abstract class BaseApiController extends AbstractController
{
    /**
     * @var string $appId
     */
    public $appId = '';

    /**
     * 验证服务器地址的有效性
     */
    public function validate()
    {
        //获取参数
        $echoStr = $this->request["echostr"];
        $signature = $this->request["signature"];
        $timestamp = $this->request["timestamp"];
        $nonce = $this->request["nonce"];
        $token = 1;
        //组装
        $tmpArr = [$token, $timestamp, $nonce];
        //处理验证数据
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode('', $tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            header('content-type:text');
            ob_clean();
            exit($echoStr);
        } else {
            exit;
        }
    }

    /**
     * 具体的业务函数
     */
    public function index()
    {
        if (isset($this->request) && is_array($this->request) && isset($this->request['MsgType'])) {
            // 判断消息类型，MsgType:消息类型
            switch ($this->request['MsgType']) {
                //事件推送
                case WeChatResponseObject::MSG_TYPE_EVENT:
                    $this->handler_event();
                    break;
                //自动回复推送
                case WeChatResponseObject::MSG_TYPE_TEXT:
                    $this->handler_text();
                    break;
                //转发数据到腾讯企点
                default:
                    $this->transferToCustomerService();
                    break;
            }
        }
        echo 'success';
        exit;//这个退出是关键，必须加上，没有会出现那个"服务器故障"的提示
    }

    abstract public function handler_event();

    abstract public function handler_text();
}