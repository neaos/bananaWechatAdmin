<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/28
 * Time: 19:18
 */

namespace App\Api\Logic;

use App\Api\Service\WeChatApiService;

class WeChatTextLogic
{
    public function textRoute($data)
    {
        $autoReplyList = WeChatApiService::instance()->getAutReplyListByAppId($data['appid']);
        foreach ($autoReplyList as $key => $value) {
            $keywordList = explode(',', $value->keyword_list);
            if (in_array($data['Content'], $keywordList)) {
                $eventKeyPrd = explode('_', $value->event_key);
                switch ($eventKeyPrd) {
                    case 'build':
                        $this->build($data);
                        break;
                    case 'question':
                        $this->question($data);
                        break;
                    case 'gift':
                        $this->gift($data);
                        break;
                    case 'message':
                        $this->message($data);
                        break;
                }
                break;
            }
        }
        // 找不到自动回复的匹配就转发到客服
        WeChatApiService::instance()->transferToCustomerService($data['FromUserName'], $data['ToUserName'], $data['Content']);
    }

    private function build($data)
    {

    }

    private function question($data)
    {

    }

    private function gift($data)
    {

    }

    private function message($data)
    {

    }
}