<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/28
 * Time: 19:18
 */

namespace App\Admin\Controller;

use App\Admin\Model\DatabaseModel\AdminLogModel;
use App\Admin\Object\SessionObject;
use App\Admin\Object\ResCodeObject;
use App\Admin\Property\AdminLogProperty;
use Exception;
use Library\Exception\WebException;
use Library\Request;
use Library\Router;
use Library\Virtual\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    /**
     * @var SessionObject $sessionInfo
     */
    public $sessionInfo;

    /**
     * @var string $sessionId
     */
    public $sessionId;

    /**
     * BaseController constructor.
     * @param $request
     * @throws WebException
     * @throws Exception
     */
    public function __construct($request)
    {
        parent::__construct($request);

        $this->sessionId = Request::cookie('PHPSESSID');
        $this->sessionInfo = new SessionObject($this->sessionId);
        if (!in_array(strtolower(Request::server('request_uri')), [
            '/admin/login'
        ])) {
            //验证用户信息
            $this->verifyUserInfo();
        }
        if (!in_array(strtolower(Request::server('request_uri')), [
            '/admin/info',
            '/adminlog/list',
            '/log/adminlog'
        ])) {
            $this->logAction();
        }
    }

    /**
     * 判断session中是否有用户数据
     * @throws WebException
     */
    public function verifyUserInfo()
    {
        if (!$this->sessionInfo->id) {
            throw new WebException('用户没有登陆', ResCodeObject::$noLogin);
        }
    }

    /**
     * 记录用户操作
     * @throws \Exception
     */
    public function logAction()
    {
        if (!strpos(strtolower(Request::server('request_uri')), 'namelist')) {

            if (in_array(strtolower(Request::server('request_uri')), ['/admin/login'])) {
                $adminId = 0;
                $username = $this->request['username'];
            } else {
                $adminId = $this->sessionInfo->id;
                $username = $this->sessionInfo->username;
            }

            $logDataObject = (new AdminLogProperty())->setProperty([
                'admin_id' => $adminId,
                'username' => $username,
                'route' => Router::getRouteInstance()->getRoute(),
                'request_data' => json_encode($_REQUEST, JSON_UNESCAPED_UNICODE),
                'response_data' => '',
                'ip' => Request::server('remote_addr'),
                'create_time' => time(),
                'update_time' => time(),
            ]);
            (new AdminLogModel())->addOne($logDataObject);
        }
    }

    /**
     * 自动响应返回
     * @param $res
     * @param array $data
     * @return array
     */
    public function autoResponse($res, $data = [])
    {
        return $res ? $this->responseSuccess($data) : $this->responseFailed();
    }

    /**
     * 操作成功返回
     * @param array $data
     * @return array
     */
    public function responseSuccess($data = [])
    {
        //默认返回成功的数据
        $resData = [
            'code' => ResCodeObject::$successHttp,
            'data' => [],
            'message' => '操作成功',
        ];

        $resData['data'] = $data ? $resData['data'] + $data : $resData['data'];

        return $resData;
    }

    /**
     * 操作失败返回
     * @param array $data
     * @return array
     */
    public function responseFailed($data = [])
    {
        //默认返回失败的数据
        $resData = [
            'code' => 40000,
            'data' => [],
            'message' => '操作失败',
        ];

        $resData['data'] = $data ? $resData['data'] + $data : $resData['data'];

        return $resData;
    }
}