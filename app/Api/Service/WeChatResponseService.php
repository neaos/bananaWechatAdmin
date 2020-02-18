<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/02/18
 * Time: 11:25
 */

namespace App\Api\Service;

class WeChatResponseService
{
    /**
     * @var WeChatResponseService $init
     */
    private static $init;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return WeChatResponseService
     */
    public static function instance()
    {
        if (!self::$init) {
            self::$init = new static();
        }
        return self::$init;
    }

    /**
     * 创造微信返回的data数组
     * @param string $fromUserName
     * @param string $ToUserName
     * @param array $data
     * @param string $type
     * @return array
     */
    public function make(string $fromUserName, string $ToUserName, array $data, string $type)
    {
        /* 基础数据 */
        $baseData = array(
            'ToUserName' => $fromUserName,
            'FromUserName' => $ToUserName,
            'CreateTime' => time(),
            'MsgType' => $type,
        );

        /* 按类型添加额外数据 */
        $data = call_user_func(array($this, $type), $data);

        return array_merge($baseData, $data);
    }

    /**
     * 构造文本信息
     * @param  string $content 要回复的文本
     * @return mixed
     */
    private static function text($content)
    {
        $data['Content'] = $content;
        return $data;
    }

    /**
     * 构造图片信息
     * @param  integer $media 图片ID
     * @return mixed
     */
    private function image($media)
    {
        $data['MediaId'] = $media;
        return $data;
    }

    /**
     * 构造音频信息
     * @param  integer $media 语音ID
     * @return mixed
     */
    private function voice($media)
    {
        $data['MediaId'] = $media;
        return $data;
    }

    /**
     * 构造视频信息
     * @param  array $video 要回复的视频 [视频ID，标题，说明]
     * @return array
     */
    private function video($video)
    {
        $data = array();
        list(
            $data['MediaId'],
            $data['Title'],
            $data['Description'],
            ) = $video;

        return $data;
    }

    /**
     * 构造音乐信息
     * @param  array $music 要回复的音乐[标题，说明，链接，高品质链接，缩略图ID]
     * @return array
     */
    private function music($music)
    {
        $data = array();
        list(
            $data['Title'],
            $data['Description'],
            $data['MusicUrl'],
            $data['HQMusicUrl'],
            $data['ThumbMediaId'],
            ) = $music;

        return $data;
    }

    /**
     * 构造图文信息
     * @param  array $news 要回复的图文内容
     * [
     *      0 => 第一条图文信息[标题，说明，图片链接，全文连接]，
     *      1 => 第二条图文信息[标题，说明，图片链接，全文连接]，
     *      2 => 第三条图文信息[标题，说明，图片链接，全文连接]，
     * ]
     * @return mixed
     */
    private function news($news)
    {
        $articles = array();
        foreach ($news as $key => $value) {
            list(
                $articles[$key]['Title'],
                $articles[$key]['Description'],
                $articles[$key]['Url'],
                $articles[$key]['PicUrl']
                ) = $value;

            if ($key >= 9) break; //最多只允许10条图文信息
        }
        $data['ArticleCount'] = count($articles);
        $data['Articles'] = $articles;

        return $data;
    }

    /**
     * 转发客服消息
     * @param $content
     * @return mixed
     */
    private function transfer_customer_service($content)
    {
        return $content;
    }
}