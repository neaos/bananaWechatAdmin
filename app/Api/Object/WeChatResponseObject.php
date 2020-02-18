<?php

namespace App\Api\Object;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/02/18
 * Time: 11:46
 */
class WeChatResponseObject
{
    /* 消息类型常量 */
    const MSG_TYPE_TEXT = 'text';
    const MSG_TYPE_IMAGE = 'image';
    const MSG_TYPE_VOICE = 'voice';
    const MSG_TYPE_VIDEO = 'video';
    const MSG_TYPE_MUSIC = 'music';
    const MSG_TYPE_NEWS = 'news';
    const MSG_TYPE_LOCATION = 'location';
    const MSG_TYPE_LINK = 'link';
    const MSG_TYPE_EVENT = 'event';
    const MSG_TYPE_TRANSFER = 'transfer_customer_service';
}