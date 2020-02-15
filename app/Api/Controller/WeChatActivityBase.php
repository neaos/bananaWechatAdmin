<?php

namespace WeChatActivityBaseSpace;


/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/1
 * Time: 17:43
 */

require('Wechat.class.php');
require(dirname(__FILE__) . '/../common.inc.php');

class WeChatActivityBase
{
    /*----------------------------------------------------------------------------------------------------------------*/
    /*                                      父类初始化的基本成员变量
    /*----------------------------------------------------------------------------------------------------------------*/

    /**
     * @var string 警报提示机器人webHook
     */
    public $robotUrl = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=9c76fd55-d532-4047-9321-b3e51104a38a';

    /**
     * @var bool $is_test 测试模式的开关
     */
    public $is_test = false;

    /**
     * @var null $weChatOperation 微信操作类
     */
    public $weChatOperation = null;

    /**
     * @var \DbMysql $wab_db WeChatActivityBase类用的db
     */
    public $wab_db = null;

    /**
     * @var \predis $wab_redis WeChatActivityBase类用的redis
     */
    public $wab_redis = null;

    /**
     * @var bool $is_run_redis 是否已经成功连接运行redis了
     */
    public $is_run_redis = false;

    /*----------------------------------------------------------------------------------------------------------------*/
    /*                                      父类使用部分-用于父类变量
    /*----------------------------------------------------------------------------------------------------------------*/

    /**
     * @var string $event_no_start 事件没开始提示信息
     */
    public $event_no_start = '活动还未开始';

    /**
     * @var string $event_already_end 事件
     */
    public $event_already_end = '活动已经结束';

    /**
     * @var string $default_code_message 默认礼包提示
     */
    public $default_code_message = '礼包码为:{{code}}';

    /**
     * 数据表名映射表
     * @var array $db_table
     */
    private $db_table = [
        'code' => 'hd_code',
        //礼包表
        'address' => 'hd_lottery_address',
        //联系
        'prize' => 'hd_lottery_prize',
        //奖品
        'log' => 'hd_lottery_log',
        //日志
        'user' => 'hd_user_common',
        //用户表
        'reply' => 'tw_wechat_reply',
        //公众号回复表
        'menu' => 'tw_wechat_menu',
        //公众号自定义菜单表
        'wechat_user' => 'tw_wechat_user',
        //公众号用户表
        'hd_code' => 'db_www.hd_code',
        // 礼包码表
        'code_message' => 'hd_code_message',
        // 礼包推送文字表
        'hd_config' => 'hd_config',
        // 活动配置表
        "keyword" => "tw_wechat_keyword",
        // 口令礼包设置
        "game" => "tw_wechat_game",
        // 游戏配置
        "scan_qrcode_log" => "tw_wechat_scan_qrcode_log",
        // 二维码推广记录
    ];

    /**
     * @var string $app_id 微信公众号appID
     */
    protected $app_id = '';

    /**
     * @var string $token 微信公众号$token
     */
    protected $token = '';

    /**
     * @var string $ip 当前http的访问者的ip
     */
    public $ip = '';

    /**
     * @var array $weChatRequest 微信请求服务器的请求数据
     */
    public $weChatRequest = [];

    /**
     * @var bool $is_connect_redis 是否连接redis
     */
    public $is_connect_redis = true;

    /**
     * @var array $jsonBack_debug_data 用于测试模式下jsonBack时的debug返回数据
     */
    public $jsonBack_debug_data = [];

    /**
     * @var int $http_fail_code 请求失败返回码
     */
    public $http_fail_code = 0;

    /**
     * @var int $http_success_code 请求成功返回码
     */
    public $http_success_code = 1;

    /**
     * @var string $http_request_timestamp http请求时间戳
     */
    public $http_request_timestamp = '';

    /**
     * @var string $http_request_datetime http的请求日期
     */
    public $http_request_datetime = '';

    /**
     * @var string $http_request_date http请求的日期
     */
    public $http_request_date = '';

    /**
     * @var string $http_request_date_timestamp http请求的日期时间戳
     */
    public $http_request_date_timestamp = '';

    /**
     * 礼包领取锁
     * @var string $gift_code_lock_key
     */
    protected $gift_code_lock_key = '';

    /**
     * 礼包领取排队锁
     * @var string $gift_code_public_lock_key
     */
    protected $gift_code_public_lock_key = '';

    /**
     * 礼包领取ip锁
     * @var string $gift_code_public_lock_key
     */
    protected $gift_code_ip_lock_key = '';

    /**
     * 礼包领取锁提示信息
     * @var string $gift_code_lock_error
     */
    protected $gift_code_lock_error = '现在太多人在排队了，请稍后重试。';

    /**
     * 默认回复锁
     * @var string $default_reply_lock_key
     */
    protected $default_reply_lock_key = '';

    /**
     * @var string $default_reply
     */
    protected $default_reply = '';

    /**
     * @var \Closure $default_reply_function 自动回复的函数
     */
    protected $default_reply_function = null;

    /*----------------------------------------------------------------------------------------------------------------*/
    /*                                      父子类使用部分-用于业务变量
    /*----------------------------------------------------------------------------------------------------------------*/

    /**
     * @var array 微信公众号事件常驻对象
     */
    public $wechat_key_words = [];

    /**
     * @var array 微信公众号礼包提示信息
     */
    public $wechat_code_message = [];

    /**
     * @var array $wechat_reply 微信公众号自动回复列表
     */
    public $wechat_reply = [];


    /**
     * 构造函数
     * WeChatActivityBase constructor.
     * @param \DbMysql $db DbMysql数据库对象
     * @param boolean $db_debug 数据库是否打开测试模式
     * @param string $app_id 微信公众号的app_id
     * @param string $token 微信公众号的token
     * @param bool $is_test 测试模式开关
     */
    public function __construct($db, $db_debug, $app_id, $token, $is_test = false)
    {
        $this->weChatOperation = new \Wechat('');

        //设置测试状态
        if (isset($_REQUEST['is_test'])) {
            if ((int)$_REQUEST['is_test'] == 1) {
                $this->is_test = true;
            } else {
                $this->is_test = false;
            }
        } else {
            $this->is_test = $is_test;
        }

        //根据test状态开启PHP调试状态
        if ($this->is_test) {
            ini_set('display_errors', 'On');
            error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
        } else {
            ini_set('display_errors', 'Off');
        }

        //初始化数据库对象
        $this->wab_db = $db;
        $this->wab_db->connect_hd();
        //输出数据库查询错误
        $this->wab_db->output = $db_debug;

        //初始化ao_redis
        if ($this->is_connect_redis) {
            if (($this->connect_wab_redis())['code'] == 0) {
                $this->is_run_redis = true;
            } else {
                $this->is_run_redis = false;
                //redis连接失败
                $this->weChatOperation->replyText($this->default_reply);
            }
        }

        //初始化业务数据
        $this->ip = $this->GetIP();
        $this->app_id = $app_id;
        $this->token = $token;
        $this->weChatRequest = $this->weChatOperation->request();

        //初始化一些锁
        $this->gift_code_lock_key = "wab_{$this->weChatRequest['FromUserName']}_{$this->app_id}_get_gift_code_lock";
        $this->gift_code_public_lock_key = "wab_{$this->app_id}_get_gift_code_public_lock_key";
        $this->gift_code_ip_lock_key = "wab_{$this->ip}_{$this->app_id}_get_gift_code_ip_lock_key";
        $this->default_reply_lock_key = "wab_{$this->weChatRequest['FromUserName']}_{$this->app_id}_default_reply_lock";

        //初始化业务逻辑信息
        $this->http_request_timestamp = $_SERVER["REQUEST_TIME"];
        $this->http_request_datetime = date('Y-m-d H:i:s', $_SERVER["REQUEST_TIME"]);
        $this->http_request_date = date('Y-m-d');
        $this->http_request_date_timestamp = strtotime($this->http_request_date);

        //初始化自动回复函数
        if (!$this->default_reply_function) {
            $this->default_reply_function = function ($reply_list) {
                $this->weChatOperation->replyText($reply_list[$this->app_id][6][0]['content'] ?? $this->default_reply);
            };
        }
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*                                              报警函数
    /*----------------------------------------------------------------------------------------------------------------*/

    public function enterpriseWxRobotTalk($message, $type, $markdown)
    {
        if (!$this->is_test) {
            $data['msgtype'] = 'markdown';
            $data['markdown']['content'] = '';
            $robot_name = '迪丽热巴小姐姐提醒你';
            $env = '生产环境';
            $env_color = 'warning';
            $data['markdown']['content'] .= "{$robot_name}:<font color=\"comment\">{$message}</font>，请相关同事注意。\n";
            $data['markdown']['content'] .= ">报错环境:　<font color=\"{$env_color}\">{$env}</font> \n";
            $data['markdown']['content'] .= ">类型:　<font color=\"info\">{$type}</font> \n";
            $data['markdown']['content'] .= ">详细问题:　<font color=\"comment\">活动标识为:</font>`{$this->app_id}`<font color=\"comment\">，出现问题　{$markdown}</font>";
            $this->post_curl($this->robotUrl, json_encode($data));
        }
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*                                父类初始化使用部分-子类有必要可以使用
    /*----------------------------------------------------------------------------------------------------------------*/

    /**
     * 连接上wab_redis
     * @return array
     */
    protected function connect_wab_redis()
    {
        try {
            if ($this->wab_redis && $this->wab_redis->ping() == '+PONG') {
                return ['code' => 0];
            }
        } catch (\Exception $error) {
            if ($this->is_test) {
                $this->jsonBack($error);
            }
        }
        $this->wab_redis = new \predis();
        return $this->wab_redis->connect_hd_new();
    }

    /**
     * 获取数据库同步表名
     * @param string $table_name db_table中的key
     * @return string
     */
    protected function get_table($table_name)
    {
        if (isset($this->db_table[$table_name])) {
            return $this->db_table[$table_name];
        } else {
            $this->jsonBack([
                'status' => $this->http_fail_code,
                'msg' => $this->is_test ? "wab_db_table中找不到{$table_name}" : "网络出错，请稍后再试！"
            ]);
            return "";
        }
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*                                      父类使用部分-用于业务操作
    /*----------------------------------------------------------------------------------------------------------------*/

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
        //组装
        $tmpArr = [$this->token, $timestamp, $nonce];
        //处理验证数据
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
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
     * 微信请求的主要入口
     */
    public function index()
    {
        if (isset($this->weChatRequest) && is_array($this->weChatRequest) && isset($this->weChatRequest['MsgType'])) {
            // 判断消息类型，MsgType:消息类型
            switch ($this->weChatRequest['MsgType']) {
                //事件推送
                case $this->weChatOperation::MSG_TYPE_EVENT:
                    $this->index_event();
                    break;
                //自动回复推送
                case $this->weChatOperation::MSG_TYPE_TEXT:
                    $this->index_text();
                    break;
                //转发数据到腾讯企点
                default:
                    $this->customerService();
                    break;
            }
        }
        echo 'success';
        exit;//这个退出是关键，必须加上，没有会出现那个"服务器故障"的提示
    }

    /**
     * 菜单点击类型事件
     */
    private function index_event()
    {
        switch ($this->weChatRequest['Event']) {
            case $this->weChatOperation::MSG_EVENT_CLICK:
                $this->click_event();
                break;
            case $this->weChatOperation::MSG_EVENT_SUBSCRIBE : //第一次订阅
                $this->subscribe_event();
                break;
            case $this->weChatOperation::MSG_EVENT_SCAN : //已经关注了，去扫描一次二维码(二维码扫描)
                $this->scan_event();
                break;
            case $this->weChatOperation::MSG_EVENT_UNSUBSCRIBE : //取消订阅
                $this->unsubscribe_event();
                break;
            default:
                echo 'success';
                exit;
                break;
        }
    }

    /**
     * 回复类型事件
     */
    private function index_text()
    {
        //初始化回复数据
        $reply_list = $this->get_wechat_reply();

        //关键字自动回复模糊匹配:需要在tw_wechat_reply中有type=3是关键词回复,直接返回content
        $this->text_key_words_reply($reply_list);

        //回复答题-获取问题:需要在tw_wechat_reply中有type=8,8是问题
        $this->text_answer($reply_list);

        //回复答题-获取问题:需要在tw_wechat_reply中有type=8,8是问题
        $this->text_question($reply_list);

        //回复盖楼:需要在tw_wechat_reply中有type=7,7是盖楼回复
        $this->text_build_sequence($reply_list);

        //回复领取礼包:需要在tw_wechat_key_words中有type=2&is_many=1的数据,根据password寻找hd,根据hd获取礼包码
        $this->text_gift_code();

        //回复事件回复:需要在tw_wechat_key_words中有type=2&is_many=0的数据,根据password寻找content
        $this->text_event_message($reply_list);

        //默认回复:需要在tw_wechat_reply中有type=6是默认回复,直接返回content
        $this->text_default_reply($reply_list);

        //没有命中:直接转发到腾讯企点
        $this->customerService();
    }

    /**
     * 获取答题成功礼包
     * @param $gift_key
     */
    private function answer_question_gift_code($gift_key)
    {
        $all_event = $this->get_wechat_key_words();
        $key_words = [];
        if ($all_event[$this->app_id]) {
            foreach ($all_event[$this->app_id] as $key => $value) {
                if ((int)$value['type'] === 2) {
                    $key_words[$value['hd']] = $value['hd'];
                }
            }
        }
        //获取答题成功礼包
        if (array_key_exists($gift_key, $key_words)) {
            //活动是否在运行
            $this->check_event_time($gift_key);
            $code_info = $this->check_user_gift($gift_key);
            if ($code_info == []) {
                $code_info = $this->get_gift_code($gift_key);
            } else {
                $this->send_code_message($code_info['code'], $gift_key);
            }
            if ($code_info['code'] != '') {
                $update_code = $this->wab_db
                    ->table($this->get_table('code'), true)
                    ->data([
                        'uname' => $this->weChatRequest['FromUserName'],
                        'logtime' => $this->http_request_datetime,
                        'ip' => $this->ip,
                        'state' => 1
                    ])->where([
                        'id' => $code_info['id'],
                        'state' => 0
                    ])->update();
                if (!$update_code) {
                    $this->weChatOperation->replyText('哎呀，礼包不足，紧急补仓中...请重试');
                } else {
                    $this->send_code_message($code_info['code'], $gift_key);
                }
            } else {
                $this->weChatOperation->replyText('哎呀，礼包不足，紧急补仓中...');
            }
        }
    }

    /**
     * 获取盖楼礼包
     * @param $gift_key
     */
    private function build_sequence_gift_code($gift_key)
    {
        $all_event = $this->get_wechat_key_words();
        $key_words = [];
        if ($all_event[$this->app_id]) {
            foreach ($all_event[$this->app_id] as $key => $value) {
                if ((int)$value['type'] === 2) {
                    $key_words[$value['hd']] = $value['hd'];
                }
            }
        }
        //回复菜单礼包
        if (array_key_exists($gift_key, $key_words)) {
            //活动是否在运行
            $this->check_event_time($gift_key);
            $code_info = $this->check_user_gift($gift_key);
            if ($code_info == []) {
                $code_info = $this->get_gift_code($gift_key);
            } else {
                $this->send_code_message($code_info['code'], $gift_key);
            }
            if ($code_info['code'] != '') {
                $update_code = $this->wab_db
                    ->table($this->get_table('code'), true)
                    ->data([
                        'uname' => $this->weChatRequest['FromUserName'],
                        'logtime' => $this->http_request_datetime,
                        'ip' => $this->ip,
                        'state' => 1
                    ])->where([
                        'id' => $code_info['id'],
                        'state' => 0
                    ])->update();
                if (!$update_code) {
                    $this->weChatOperation->replyText('哎呀，礼包不足，紧急补仓中...请重试');
                } else {
                    $this->send_code_message($code_info['code'], $gift_key);
                }
            } else {
                $this->weChatOperation->replyText('很遗憾，大奖和你擦肩而过了，继续努力吧！');
            }
        }
    }

    /**
     * 回复事件的回复
     * @param $reply_list
     */
    private function text_event_message($reply_list)
    {
        $all_event = $this->get_wechat_key_words();
        $key_words = [];
        if ($all_event[$this->app_id]) {
            foreach ($all_event[$this->app_id] as $key => $value) {
                if ((int)$value['type'] === 2 && (int)$value['is_many'] == 0) {
                    $key_words[$value['password']] = $value;
                }
            }
        }
        //回复事件回复
        if (array_key_exists($this->weChatRequest['Content'], $key_words)) {
            // type = 5 点击事件自动回复
            $hdKey = $key_words[$this->weChatRequest['Content']]['hd'];
            $this->check_event_time($key_words[$this->weChatRequest['Content']]['hd']);
            $rely = $reply_list[$this->app_id][5][$hdKey];
            if (isset($rely['content']) && $rely['content']) {
                $this->weChatOperation->replyText($rely['content']);
            }
        }
    }

    /**
     * 回复礼包
     */
    private function text_gift_code()
    {
        $all_event = $this->get_wechat_key_words();
        $key_words = [];
        if ($all_event[$this->app_id]) {
            foreach ($all_event[$this->app_id] as $key => $value) {
                if ((int)$value['type'] === 2 && (int)$value['is_many'] == 1) {
                    $key_words[$value['password']] = $value['hd'];
                }
            }
        }
        //回复菜单礼包
        if (array_key_exists($this->weChatRequest['Content'], $key_words)) {
            //活动是否在运行
            $hd_key = $key_words[$this->weChatRequest['Content']];
            $this->check_event_time($hd_key);
            $code_info = $this->check_user_gift($hd_key);
            if ($code_info == []) {
                $code_info = $this->get_gift_code($hd_key);
            } else {
                $this->send_code_message($code_info['code'], $hd_key);
            }
            if ($code_info['code'] != '') {
                $update_code = $this->wab_db
                    ->table($this->get_table('code'), true)
                    ->data([
                        'uname' => $this->weChatRequest['FromUserName'],
                        'logtime' => $this->http_request_datetime,
                        'ip' => $this->ip,
                        'state' => 1
                    ])->where([
                        'id' => $code_info['id'],
                        'state' => 0
                    ])->update();
                if (!$update_code) {
                    $this->weChatOperation->replyText('哎呀，礼包不足，紧急补仓中...请重试');
                } else {
                    $this->send_code_message($code_info['code'], $hd_key);
                }
            } else {
                $this->weChatOperation->replyText('哎呀，礼包不足，紧急补仓中...');
            }
        }
    }

    /**
     * 关键字回复
     * @param array $reply_list
     */
    private function text_key_words_reply($reply_list)
    {
        if ($this->weChatRequest['Content'] && $reply_list[$this->app_id][3]) {
            foreach ($reply_list[$this->app_id][3] as $key => $value) {
                if (false !== strpos($this->weChatRequest['Content'], $key)) {
                    $this->weChatOperation->replyText($value['content']);
                }
            }
        }
    }

    /**
     * 盖楼回复
     * @param $reply_list
     */
    private function text_build_sequence($reply_list)
    {
        if ($this->weChatRequest['Content'] && $reply_list[$this->app_id][7]) {
            foreach ($reply_list[$this->app_id][7] as $key => $value) {
                if (false !== strpos($this->weChatRequest['Content'], $value['build_word'])) {
                    $this->check_event_time($value['keyword']);
                    $inc_after = $this->wab_redis->incrby($this->get_build_sequence_table(urlencode($value['build_word'])), 1, 3600 * 24 * 3);
                    if ((int)$value['number'] > 0 && (int)($inc_after % $value['number']) === 0) {
                        $this->build_sequence_gift_code("{$value['keyword']}");
                    } else {
                        $this->weChatOperation->replyText($value['content']);
                    }
                    break;
                }
            }
        }
    }

    /**
     * 答题回复-回答问题
     * @param $reply_list
     */
    private function text_answer($reply_list)
    {
        $answer = $this->wab_redis->get($this->get_user_question_table());
        if ($answer) {
            if ($this->weChatRequest['Content'] == $answer) {
                foreach ($reply_list[$this->app_id][8] as $key => $value) {
                    if (false !== strpos($this->weChatRequest['Content'], $value['answer_word'])) {
                        $this->check_event_time($value['keyword']);
                        $this->wab_redis->del($this->get_user_question_table());
                        $this->answer_question_gift_code($value['keyword']);
                    }
                }
            } else {
                $this->weChatOperation->replyText('很遗憾答错了,你还可以继续答题。');
            }
        }
    }

    /**
     * 答题回复-获取问题
     * @param $reply_list
     */
    private function text_question($reply_list)
    {
        if ($this->weChatRequest['Content'] && $reply_list[$this->app_id][8]) {
            foreach ($reply_list[$this->app_id][8] as $key => $value) {
                if ($this->weChatRequest['Content'] == $value['build_word']) {
                    $this->check_event_time($value['keyword']);
                    $this->wab_redis->set($this->get_user_question_table(), $value['answer_word'], 180);
                    $this->weChatOperation->replyText($value['content']);
                }
            }
        }
    }

    /**
     * 默认回复
     * @param array $reply_list
     */
    private function text_default_reply($reply_list)
    {
        $function = $this->default_reply_function;
        $function($reply_list);
    }

    /**
     * 点击菜单事件
     */
    private function click_event()
    {
        //用于腾讯企点的客服，传1或者customerService
        if (in_array((int)$this->weChatRequest['EventKey'], [1, 2])) {
            $this->customerService();
            exit();
        }

        //执行菜单点击获取礼包逻辑
        $this->click_event_gift_code();

        //菜单点击回复事件的文案
        $this->click_event_rely();

        //执行菜单点击触发类函数
        if (method_exists($this, $this->weChatRequest['EventKey'])) {
            $method = $this->weChatRequest['EventKey'];
            $this->$method();
        }

        //执行默认回复
        $this->text_default_reply($this->get_wechat_reply());
    }

    /**
     * 点击菜单礼包事件
     */
    private function click_event_gift_code()
    {
        $all_event = $this->get_wechat_key_words();
        $key_words = [];
        if ($all_event[$this->app_id]) {
            foreach ($all_event[$this->app_id] as $key => $value) {
                if ((int)$value['type'] === 1 && (int)$value['is_many'] === 1) {
                    $key_words[$value['hd']] = $value['password'];
                }
            }
        }
        //点击菜单触发的礼包事件
        if (array_key_exists($this->weChatRequest['EventKey'], $key_words)) {
            //活动是否在运行
            $this->check_event_time($this->weChatRequest['EventKey']);
            $code_info = $this->check_user_gift($this->weChatRequest['EventKey']);

            if ($code_info == []) {
                $code_info = $this->get_gift_code($this->weChatRequest['EventKey']);
            } else {
                $this->send_code_message($code_info['code'], $this->weChatRequest['EventKey']);
            }
            if ($code_info['code'] != '') {
                $update_code = $this->wab_db
                    ->table($this->get_table('code'), true)
                    ->data([
                        'uname' => $this->weChatRequest['FromUserName'],
                        'logtime' => $this->http_request_datetime,
                        'ip' => $this->ip,
                        'state' => 1
                    ])->where([
                        'id' => $code_info['id'],
                        'state' => 0
                    ])->update();

                if (!$update_code) {
                    $this->weChatOperation->replyText('哎呀，礼包不足，紧急补仓中...请重试');
                } else {
                    $this->send_code_message($code_info['code'], $this->weChatRequest['EventKey']);
                }
            } else {
                $this->weChatOperation->replyText('哎呀，礼包不足，紧急补仓中...');
            }
        }
    }

    /**
     * 点击菜单回复事件
     */
    private function click_event_rely()
    {
        // type = 5 点击事件自动回复
        $this->check_event_time($this->weChatRequest['EventKey']);
        $reply_list = $this->get_wechat_reply();
        $rely = $reply_list[$this->app_id][5][$this->weChatRequest['EventKey']];
        if (isset($rely['content']) && $rely['content']) {
            $this->weChatOperation->replyText($rely['content']);
        }
    }

    /**
     * 用户关注公众号事件
     */
    private function subscribe_event()
    {
        //这里if要记录
        //玩游戏的注册数 付费数 付费金额
        if (is_string($this->weChatRequest['EventKey']) && strpos($this->weChatRequest['EventKey'], "qrscene_") !== false) {
            $scene_str_array = explode('qrscene_', $this->weChatRequest['EventKey']);
            $scene_str = $scene_str_array[1] ?? '0';
            $insert_data = [
                'app_id' => $this->app_id,
                'open_id' => $this->weChatRequest['FromUserName'],
                'event' => $this->weChatRequest['Event'],
                'event_key' => $this->weChatRequest['EventKey'],
                'scene_str' => $scene_str,
                'ticket' => $this->weChatRequest['Ticket'],
                'create_time' => $this->http_request_timestamp,
            ];
            $this->wab_db->table($this->get_table('scan_qrcode_log'))->data($insert_data)->insert();
        }
        $content = $this->default_reply;
        $reply = $this->get_wechat_reply();
        if (isset($reply[$this->app_id][1][0]['content']) && $reply[$this->app_id][1][0]['content']) {
            $content = $reply[$this->app_id][1][0]['content'];
        }
        $this->weChatOperation->replyText($content);
    }

    /**
     * 用户二次关注
     */
    private function scan_event()
    {
        $reply = $this->get_wechat_reply();
        $content = '';
        if (isset($reply[$this->app_id][1][0]['content']) && $reply[$this->app_id][1][0]['content']) {
            $content = $reply[$this->app_id][1][0]['content'];
        }
        $this->weChatOperation->replyText($content);
    }

    /**
     * 用户取关
     */
    private function unsubscribe_event()
    {
// 这里不需要再写入和更新wechat_user表
//        $this->wab_db
//            ->table($this->get_table('wechat_user'), true)
//            ->data(['status' => 0])
//            ->where([
//                'app_id' => $this->app_id,
//                'open_id' => $this->weChatRequest['FromUserName']
//            ])
//            ->update();
    }

    /**
     * 客服系统
     */
    private function customerService()
    {
        $content = $this->weChatRequest['Content'];  // 消息内容
        $this->weChatOperation->response($content, $this->weChatOperation::MSG_TYPE_TRANSFER);
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*                                      子类使用部分-用于业务操作
    /*----------------------------------------------------------------------------------------------------------------*/

    /**
     * 获取自动回复列表
     * @return array
     */
    protected function get_wechat_reply()
    {
        $reply_list = $this->wechat_reply;
        if (!$reply_list) {
            $reply_list = unserialize($this->wab_redis->get('wab_tw_wechat_reply')) ?: [];
            if (!$reply_list) {
                $reply_list = $this->wab_db
                    ->table($this->get_table('reply'), true)
                    ->select();
                if (count($reply_list) > 0) {
                    $reply_list_new = [];
                    foreach ($reply_list as $key => $value) {
                        /**
                         * 3 => '关键词自动回复',
                         * 4 => '关键词领取礼包',
                         */
                        if (in_array((int)($value['type']), [3, 4, 5])) {
                            $value['keyword'] = explode(',', $value['keyword']);
                            foreach ($value['keyword'] as $k_key => $k_value) {
                                $value['content'] = urldecode($value['content']);
                                $reply_list_new[$value['app_id']][$value['type']][$k_value] = $value;
                            }
                        } else {
                            $value['content'] = urldecode($value['content']);
                            $reply_list_new[$value['app_id']][$value['type']][] = $value;
                        }
                    }

                    $this->wechat_reply = $reply_list_new;
                    $reply_list = $reply_list_new;
                    $this->wab_redis->set('wab_tw_wechat_reply', serialize($reply_list), -1);
                }
            }
        }

        return $reply_list;
    }

    /**
     * 获取指定菜单的事件
     * @param $event_key
     * @return array
     */
    protected function get_wechat_menu($event_key)
    {
        $redis_event_key = "wab_{$this->app_id}_{$event_key}";
        $menu_info = json_decode($this->wab_redis->get($redis_event_key), true);
        if (!$menu_info) {
            $menu_info = $this->wab_db
                ->table($this->get_table('menu'), true)
                ->where([
                    'app_id' => $this->app_id,
                    'type' => 'click',
                    'event_key' => $event_key,
                    'status' => 1
                ])
                ->find();
            if ($menu_info) {
                $this->wab_redis->set($redis_event_key, json_encode($menu_info), 600);
            }
        } else {
            $menu_info = [];
        }
        return $menu_info;
    }

    /**
     * 获取礼包事件信息
     * @return array
     */
    protected function get_wechat_key_words()
    {
        /**
         * $this->get_table('keyword')表中的type意思
         * 1 => '菜单礼包事件',
         * 2 => '回复礼包事件',
         */
        $wechat_key_words = $this->wechat_key_words;
        if (!$wechat_key_words) {
            $wechat_key_words = unserialize($this->wab_redis->get('wab_tw_wechat_key_words_config')) ?: [];
            if (!$wechat_key_words) {
                $wechat_key_words_original = $this->wab_db
                    ->table($this->get_table('keyword'), true)
                    ->select();
                if (count($wechat_key_words_original) > 0) {
                    $wechat_key_words = [];
                    foreach ($wechat_key_words_original as $key => $value) {
                        $wechat_key_words[$value['app_id']][] = $value;
                    }
                    $this->wab_redis->set('wab_tw_wechat_key_words_config', serialize($wechat_key_words), -1);
                }
            }
            $this->wechat_key_words = $wechat_key_words;
        }

        return $wechat_key_words;
    }

    /**
     * 获取礼包提示信息
     */
    protected function get_wechat_code_message()
    {
        $wechat_code_message = $this->wechat_code_message;
        if (!$wechat_code_message) {
            $wechat_code_message = unserialize($this->wab_redis->get('wab_tw_wechat_code_message')) ?: [];
            if (!$wechat_code_message) {
                $wechat_code_message_original = $this->wab_db
                    ->table($this->get_table('code_message'), true)
                    ->field('*')
                    ->select();
                if (count($wechat_code_message_original) > 0) {
                    $wechat_code_message = [];
                    foreach ($wechat_code_message_original as $key => $value) {
                        $wechat_code_message[$value['hd']] = $value;
                    }
                    $this->wab_redis->set('wab_tw_wechat_code_message', serialize($wechat_code_message), -1);
                }
            }
            $this->wechat_code_message = $wechat_code_message;
        }
        return $wechat_code_message;
    }

    /**
     * 判断事件的时间段
     * @param $event_key
     */
    protected function check_event_time($event_key)
    {
        $all_event = $this->get_wechat_key_words();
        $event_info = [];
        if ($all_event[$this->app_id]) {
            foreach ($all_event[$this->app_id] as $key => $value) {
                if ($value['hd'] == $event_key) {
                    $event_info = $value;
                }
            }
        }

        $event_start_datetime = strtotime($event_info['s_date']);
        $event_end_datetime = strtotime($event_info['e_date']);

        if ($this->http_request_timestamp < $event_start_datetime) {
            $this->weChatOperation->replyText($this->event_no_start);
        }
        if ($this->http_request_timestamp > $event_end_datetime) {
            $this->weChatOperation->replyText($this->event_already_end);
        }
    }

    /**
     * 根据event_key获取用户所领取的礼包的redis_key
     * @param string $event_key
     * @param string $username
     * @return string
     */
    private function check_user_gift_table($event_key, $username)
    {
        return "wab_{$event_key}_{$username}_{$this->app_id}_gift_code";
    }

    /**
     * 获取用户礼包码
     * @param $event_key
     * @return array
     */
    protected function check_user_gift($event_key)
    {
        $gift_code_info = $this->wab_redis->get($this->check_user_gift_table($event_key, $this->weChatRequest['FromUserName']));
        if ($gift_code_info) {
            return unserialize($gift_code_info);
        } else {
            $db_data = $this->wab_db
                ->table($this->get_table('code'), true)
                ->where([
                    'hd' => $event_key,
                    'uname' => $this->weChatRequest['FromUserName'],
                ])
                ->find();
            if ($db_data) {
                $this->wab_redis->set($this->check_user_gift_table($event_key, $this->weChatRequest['FromUserName']), serialize(['code' => $db_data['code']]), 1800);
                return [
                    'code' => $db_data['code']
                ];
            } else {
                return [];
            }
        }
    }

    /**
     * 发送礼包码消息
     * @param $code
     * @param $event_key
     */
    protected function send_code_message($code, $event_key)
    {
        $code_msg_array = $this->get_wechat_code_message();
        //设置礼包redis
        $this->wab_redis->set($this->check_user_gift_table($event_key, $this->weChatRequest['FromUserName']), serialize(['code' => $code]), 1800);
        //组装信息
        $code_msg = $code_msg_array[$event_key]['content'] ?? $this->default_code_message;
        $code_link = '<a href="http://hd.693975.com/image/code/copycode.html?code=' . trim($code) . '">　' . trim($code) . '　</a>';
        $this->weChatOperation->replyText(str_replace('{{code}}', $code_link, $code_msg));
    }

    /**
     * 回答问题的redis
     * @return string
     */
    protected function get_user_question_table()
    {
        return "{$this->app_id}_{$this->weChatRequest['FromUserName']}_question";
    }

    /**
     * 获取盖楼列表的redis
     * @param $event_key
     * @return string
     */
    protected function get_build_sequence_table($event_key)
    {
        return "wab_{$event_key}_{$this->app_id}_build_sequence_list";
    }

    /**
     * 获取礼包码列表的redis
     * @param $event_key
     * @return string
     */
    protected function get_code_list_table($event_key)
    {
        return "wab_{$event_key}_{$this->app_id}_code_list";
    }

    /**
     * 根据类型获得礼包码
     * @param string $event_key 事件标识
     * @return array 礼包的id，code和type
     */
    protected function get_gift_code($event_key)
    {
//        //判断微信礼包ip锁
//        if ((int)($this->wab_redis->get("{$this->gift_code_ip_lock_key}_{$event_key}")) > 3) {
//            $this->weChatOperation->replyText("礼包已被领取完!");
//        } else {
//            $this->wab_redis->incrby(
//                "{$this->gift_code_ip_lock_key}_{$event_key}",
//                1,
//                strtotime(date('Y-m-d 23:59:59')) - time()
//            );
//        }
        //判断礼包锁
        if ($this->wab_redis->get($this->gift_code_lock_key)) {
            $this->weChatOperation->replyText($this->gift_code_lock_error . "!");
        } else {
            $this->wab_redis->set($this->gift_code_lock_key, 1, 7);
        }
        //获取礼包
        $code_list_redis_key = $this->get_code_list_table($event_key);
        if ($this->is_run_redis) {
            $codeData = unserialize($this->wab_redis->rpop($code_list_redis_key));
            if (!$codeData) {
                $lock_res = $this->wab_redis->set($this->gift_code_public_lock_key, 1, ['nx', 'ex' => 40]);
                if (!$lock_res) {
                    $this->weChatOperation->replyText($this->gift_code_lock_error);
                }
                $codeInfo = $this->wab_db
                    ->table($this->get_table('code'))
                    ->where([
                        'hd' => $event_key,
                        'state' => 0
                    ])
                    ->field("id,code,type")
                    ->limit('100')
                    ->select();
                if ($codeInfo) {
                    $codeInfo = array_map('serialize', $codeInfo);
                    $this->wab_redis->lPushAll($code_list_redis_key, $codeInfo, 3600 * 2);
                    $this->wab_redis->expire($this->gift_code_public_lock_key, 1);
                    $codeData = unserialize($this->wab_redis->rpop($code_list_redis_key));
                    $codeData['code'] = trim($codeData['code']);
                } else {
                    $this->wab_redis->expire($this->gift_code_public_lock_key, 1);
                    $codeData = [
                        'code' => "",
                        'id' => 0,
                        'type' => -1
                    ];
                }
            } else {
                $codeData['code'] = trim($codeData['code']);
            }
        } else {
            $codeData = [
                'code' => "",
                'id' => 0,
                'type' => -1
            ];

        }
        return $codeData;
    }

    /**
     * 获取客户访问IP-父类使用，子类不允许使用
     * @return string
     */
    private function GetIP()
    {
        if ($_SERVER["HTTP_X_FORWARDED_FOR"] ?? false) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            $ips = explode(',', $ip);//阿里cdn
            $ip = $ips[0];
            if ($ip == 'unknown') {
                $ip = $ips[1];
            }
        } elseif ($_SERVER["HTTP_CDN_SRC_IP"] ?? false) {
            $ip = $_SERVER["HTTP_CDN_SRC_IP"];
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $ip = str_replace(['::ffff:', '[', ']'], ['', '', ''], $ip);

        return $ip;
    }

    /**
     * 检查http参数的正确性
     * @param $data
     * @return array|string
     */
    public function checkData($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $v) {
                $data[$key] = $this->checkData($v);
            }
        } else {
            $data = trim($data);
            $data = strip_tags($data);
            $data = htmlspecialchars($data);
            $data = addslashes($data);
        }
        return $data;
    }

    /**
     * 获取http传入的数据
     * @param array $field 需要的request值的数组
     * @param array $error_msg 路由传值错误提示
     * @param bool $strict 是否严格匹配非空
     * @return array
     */
    protected function take_http_data($field, $error_msg = [], $strict = true)
    {
        $http_data = [];
        foreach ($field as & $field_name) {
            if ($field_name != 'a') {
                if ($_GET[$field_name] != '') {
                    $http_data[$field_name] = $this->checkData($_GET[$field_name]);
                } else {
                    if (!$strict) {
                        $http_data[$field_name] = $this->checkData($_GET[$field_name]);
                    } else {
                        $this->jsonBack([
                            'status' => $this->http_fail_code,
                            'msg' => $error_msg[$field_name] ? "{$error_msg[$field_name]}不能为空！" : "{$field_name}不能为空！"
                        ]);
                    }
                }
            } else {
                if (isset($_GET[$field_name])) {
                    if ($_GET[$field_name] != '') {
                        $http_data[$field_name] = $this->checkData($_GET[$field_name]);
                    } else {
                        $this->jsonBack([
                            'status' => $this->http_fail_code,
                            'msg' => "非法接口，停止访问"
                        ]);
                    }
                } else {
                    $this->jsonBack([
                        'status' => $this->http_fail_code,
                        'msg' => "停止访问"
                    ]);
                }
            }
        }
        return $http_data;
    }

    /**
     * 返回json数组
     * @param $data
     * @param boolean $is_exit
     */
    public function jsonBack($data, $is_exit = true)
    {
        $callback = ($this->take_http_data(['callback'], [], false))['callback'];
        if ($this->is_test && count($this->jsonBack_debug_data) > 0) {
            $data['debug'] = $this->jsonBack_debug_data;
        }
        if ($callback) {
            echo '' . $callback . "(" . json_encode($data, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE) . ")";
        } else {
            echo json_encode($data, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
        }
        if ($is_exit) {
            exit();
        }
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*                                           工具函数
    /*----------------------------------------------------------------------------------------------------------------*/

    /**
     * post方式请求的curl
     * @param $url
     * @param $post_data
     * @param int $timeout
     * @return mixed
     */
    protected function post_curl($url, $post_data, $timeout = 5)
    {
        $ch = curl_init();  //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);  //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);  //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //设置不输出直接返回字符串
        curl_setopt($ch, CURLOPT_POST, 1);  //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $result = curl_exec($ch);  //运行curl
        curl_close($ch);

        return $result;
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*                                           路由处理部分
    /*----------------------------------------------------------------------------------------------------------------*/

    /**
     * 基本路由
     */
    public function route()
    {
        if ($_GET["echostr"] && $_GET["signature"] && $_GET["timestamp"] && $_GET["nonce"]) {
            $act = 'validate';
        } else {
            $act = $_REQUEST['a'] ?? 'index';
        }
        if (method_exists($this, $act)) {
            $this->$act();
        } else {
            $this->weChatOperation->replyText("{$act}是非法接口，拒绝访问");
        }
    }
}