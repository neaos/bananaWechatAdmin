<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/01/13
 * Time: 17:15
 */

namespace App\Admin\Controller;

use App\Admin\Logic\AdminLogic;
use App\Admin\Object\ResCodeObject;
use Exception;

class AdminController extends BaseController
{
    /**
     * 获取管理员列表
     * @return array
     */
    public function adminList()
    {
        $data = (new AdminLogic())->getList($this->request);
        return $this->responseSuccess($data);
    }

    /**
     * 创建管理员
     * @return array
     * @throws Exception
     */
    public function createAdmin()
    {
        $result = (new AdminLogic())->createAdmin($this->request);
        return $this->autoResponse($result);
    }

    /**
     * 管理员登录
     * @return array
     * @throws Exception
     */
    public function login()
    {
        $result = (new AdminLogic())->login($this->request['username'], $this->request['password']);
        if ($result) {
            if ($this->sessionInfo->setSessionData($result)) {
                return [
                    'code' => ResCodeObject::$successHttp,
                    'data' => [
                        'id' => $result['id']
                    ],
                    'message' => '登录成功'
                ];
            } else {
                return [
                    'code' => ResCodeObject::$failHttp,
                    'message' => '无法存储session数据'
                ];
            }
        }
        return [
            'code' => ResCodeObject::$failHttp,
            'message' => '账号或者密码错误'
        ];
    }

    /**
     * 获取管理员账号数据
     * @return array
     * @throws Exception
     */
    public function info()
    {
        return [
            'code' => ResCodeObject::$successHttp,
            'data' => (new AdminLogic())->info($this->sessionInfo->sessionId, $this->sessionInfo->id),
            'message' => 'success'
        ];
    }

    /**
     * 注销登录
     * @return array
     */
    public function logout()
    {
        $this->sessionInfo->delSessionData($this->sessionId);
        return [
            'code' => ResCodeObject::$successHttp,
            'data' => [],
            'message' => '注销登录成功'
        ];
    }

    /**
     * 修改密码
     * @return array
     * @throws Exception
     */
    public function changePassword()
    {
        $result = (new AdminLogic())->changePwd((int)$this->sessionInfo->id, $this->request);
        if ($result['status']) {
            return $this->responseSuccess();
        } else {
            return $this->responseFailed(['message' => $result['message']]);
        }
    }

    /**
     * 获取用户数据权限树
     * @return array
     */
    public function dataPermissionTree()
    {
        $result = (new AdminLogic())->getDataPermissionTree($this->request['id']);
        return $this->responseSuccess($result);
    }

    /**
     * 更新用户数据权限
     */
    public function updateDataPermission()
    {
        $result = (new AdminLogic())->updateDataPermission($this->request);
        return $this->autoResponse($result);
    }
}