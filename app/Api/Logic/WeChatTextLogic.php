<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/28
 * Time: 19:18
 */

namespace App\Api\Logic;

use App\Api\Model\CacheModel\TextAnswerModel;
use App\Api\Object\WeChatResponseObject;
use App\Api\Service\WeChatApiService;
use App\Api\Service\WeChatGiftService;
use App\Common\Model\CacheModel\EventBuildModel as EventBuildCacheModel;
use App\Common\Model\DatabaseModel\EventBuildModel as EventBuildDatabaseModel;
use App\Common\Model\CacheModel\EventQuestionModel as EventQuestionCacheModel;
use App\Common\Model\DatabaseModel\EventQuestionModel as EventQuestionDatabaseModel;

class WeChatTextLogic
{
    public function textRoute($data)
    {
        $autoReplyList = WeChatApiService::instance()->getAutoReplyListByAppId($data['appid']);
        $this->answer($data);
        foreach ($autoReplyList as $key => $value) {
            $keywordList = explode(',', $value->keyword_list);
            if (in_array($data['Content'], $keywordList)) {
                $eventKeyPrd = explode('_', $value->event_key);
                $data['event_key'] = $value->event_key;
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

    private function getGift($appId, $fromUserName, $toUserName, $giftKey)
    {
        $code = WeChatGiftService::instance()->getGiftCode($fromUserName, $toUserName, $giftKey);
        if (!$code) {
            WeChatApiService::instance()->response(
                $fromUserName,
                $toUserName,
                '礼包已被领取完',
                WeChatResponseObject::MSG_TYPE_TEXT
            );
        }
        $giftMessage = WeChatGiftService::instance()->getGiftReply($appId, $giftKey);
        if (!$giftMessage) {
            WeChatApiService::instance()->response(
                $fromUserName,
                $toUserName,
                "礼包码是:{$code}",
                WeChatResponseObject::MSG_TYPE_TEXT
            );
        }
    }

    private function build($data)
    {
        $cacheModel = new EventBuildCacheModel();
        $eventBuildList = $cacheModel->getEventBuild($data['app_id']);
        if (!$eventBuildList) {
            $dbModel = new EventBuildDatabaseModel();
            $eventBuildList = $dbModel->getList(['page' => 1, 'pageSize' => 20, 'app_id' => $data['app_id']]);
            if ($eventBuildList) {
                $eventBuildList = $eventBuildList->keyBy('event_key');
            } else {
                $eventBuildList = [0];
            }
            $cacheModel->setEventBuild($data['app_id'], $eventBuildList);
        }
        if ($eventBuildList = [0]) {
            return;
        }
        if (isset($eventBuildList[$data['event_key']])) {
            $eventBuild = $eventBuildList[$data['event_key']];
            $nowFloor = $cacheModel->getNowFloor($data['event_key']);
            $cacheModel->incNowFloor($data['event_key']);
            if ($eventBuild->floor_type == 1) {
                if ($nowFloor % $eventBuild->floor_num == 0) {
                    $this->getGift($data['app_id'], $data['FromUserName'], $data['ToUserName'], $eventBuild->gift_key);
                } else {
                    WeChatApiService::instance()->response(
                        $data['FromUserName'],
                        $data['ToUserName'],
                        "盖楼成功",
                        WeChatResponseObject::MSG_TYPE_TEXT
                    );
                }
            } else {
                if (in_array($nowFloor, explode(',', $eventBuild->floor_num_list))) {
                    $this->getGift($data['app_id'], $data['FromUserName'], $data['ToUserName'], $eventBuild->gift_key);
                } else {
                    WeChatApiService::instance()->response(
                        $data['FromUserName'],
                        $data['ToUserName'],
                        "盖楼成功",
                        WeChatResponseObject::MSG_TYPE_TEXT
                    );
                }
            }
        } else {
            return;
        }
    }

    private function answer($data)
    {
        $cacheModel = new TextAnswerModel();
        $answerData = $cacheModel->getUserAnswerData($data['appid'], $data['FromUserName']);
        if (!$answerData) {
            return;
        }
        $cacheModel->delUserAnswerData($data['appid'], $data['FromUserName']);
        if ($answerData->answer == $data['Content']) {
            $this->getGift($data['app_id'], $data['FromUserName'], $data['ToUserName'], $answerData->gift_key);
        } else {
            WeChatApiService::instance()->response(
                $data['FromUserName'],
                $data['ToUserName'],
                "回答错误",
                WeChatResponseObject::MSG_TYPE_TEXT
            );
        }
    }

    private function question($data)
    {
        $cacheModel = new EventQuestionCacheModel();
        $eventQuestionList = $cacheModel->getEventQuestion($data['app_id']);
        if (!$eventQuestionList) {
            $dbModel = new EventQuestionDatabaseModel();
            $eventQuestionList = $dbModel->getList(['page' => 1, 'pageSize' => 20, 'app_id' => $data['app_id']]);
            if ($eventQuestionList) {
                $eventQuestionList = $eventQuestionList->keyBy('event_key');
            } else {
                $eventQuestionList = [0];
            }
            $cacheModel->setEventQuestion($data['app_id'], $eventQuestionList);
        }
        if ($eventBuildList = [0]) {
            return;
        }

        if (isset($eventQuestionList[$data['event_key']])) {
            $eventQuestion = $eventQuestionList[$data['event_key']];
            $textAnswerCache = new TextAnswerModel();
            $textAnswerCache->setUserAnswerData($data['app_id'], $data['FromUserName'], $eventQuestion);
            WeChatApiService::instance()->response(
                $data['FromUserName'],
                $data['ToUserName'],
                $eventQuestion->question,
                WeChatResponseObject::MSG_TYPE_TEXT
            );
        } else {
            return;
        }
    }

    private function gift($data)
    {

    }

    private function message($data)
    {

    }
}