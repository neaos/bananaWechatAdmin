<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/28
 * Time: 19:18
 */

namespace App\Api\Controller;

use Library\Virtual\Controller\AbstractController;

abstract class BaseWeChatController extends AbstractController
{
    /**
     * 具体的业务函数
     */
    abstract public function index();

    /**
     * 验证服务器地址的有效性
     */
    public function validate()
    {
        //获取参数
        $echoStr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
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
     * 操作分配函数
     */
    public function handler()
    {
        if ($_GET["echostr"] && $_GET["signature"] && $_GET["timestamp"] && $_GET["nonce"]) {
            $this->validate();
        } else {
            $this->index();
        }
    }
}