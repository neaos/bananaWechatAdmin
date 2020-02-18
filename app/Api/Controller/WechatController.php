<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/28
 * Time: 19:18
 */

namespace App\Api\Controller;

use App\Api\Logic\WeChatEventLogic;
use App\Api\Logic\WeChatTextLogic;
use App\Api\Object\WeChatEventTypeObject;
use App\Api\Service\WeChatApiService;

class WeChatController extends BaseApiController
{
    /**
     * WeChatController constructor.
     * @param $request
     */
    public function __construct($request)
    {
        parent::__construct($request);
        $this->appId = WeChatApiService::instance()->getAppidByShortName($this->request['name']);
        if ($this->appId) {
            WeChatApiService::instance()->successHandle();
        } else {
            $this->request['appid'] = $this->appId;
        }
    }

    public function handler_event()
    {
        $weChatEventLogic = new WeChatEventLogic();
        switch ($this->request['Event']) {
            case WeChatEventTypeObject::MSG_EVENT_CLICK:
                $weChatEventLogic->click();
                break;
            case WeChatEventTypeObject::MSG_EVENT_SUBSCRIBE : //第一次订阅
                $weChatEventLogic->subscribe();
                break;
            case WeChatEventTypeObject::MSG_EVENT_SCAN : //已经关注了，去扫描一次二维码(二维码扫描)
                $weChatEventLogic->scan();
                break;
            case WeChatEventTypeObject::MSG_EVENT_UNSUBSCRIBE : //取消订阅
                $weChatEventLogic->unsubscribe();
                break;
            default:
                WeChatApiService::instance()->successHandle();
                break;
        }
    }

    public function handler_text()
    {
        $weChatTextLogic = new WeChatTextLogic();
        $weChatTextLogic->textRoute($this->request);
    }
}