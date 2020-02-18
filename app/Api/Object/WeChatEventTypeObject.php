<?php

namespace App\Api\Object;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/02/18
 * Time: 11:46
 */
class WeChatEventTypeObject
{
    /* 事件类型常量 */
    const MSG_EVENT_SUBSCRIBE = 'subscribe';
    const MSG_EVENT_UNSUBSCRIBE = 'unsubscribe';
    const MSG_EVENT_SCAN = 'SCAN';
    const MSG_EVENT_LOCATION = 'LOCATION';
    const MSG_EVENT_CLICK = 'CLICK';
    const MSG_EVENT_MASSSENDJOBFINISH = 'MASSSENDJOBFINISH';
}