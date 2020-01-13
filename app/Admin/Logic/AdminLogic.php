<?php

namespace App\Admin\Logic;

use App\Admin\Model\DatabaseModel\AdminModel;
use App\Admin\Model\DatabaseModel\AdminPermissionModel;
use App\Admin\Model\DatabaseModel\AdminRolePermissionModel;
use App\Admin\Model\DatabaseModel\GameModel;
use App\Admin\Model\DatabaseModel\GameTypeModel;
use App\Admin\Model\DatabaseModel\PlatformModel;
use App\Admin\Object\SessionObject;
use App\Admin\Property\AdminProperty;
use Exception;

class AdminLogic
{
    /**
     * 获取管理员列表
     * @param array $condition
     * @return array
     */
    public function getList(array $condition)
    {
        $adminModel = new AdminModel();
        return [
            'list' => $adminModel->getList($condition) ?: [],
            'total' => $adminModel->getCount($condition)
        ];
    }

    /**
     * 创建管理员
     * @param $createData
     * @return bool
     * @throws Exception
     */
    public function createAdmin($createData)
    {
        $adminCoModel = new AdminModel();

        $adminCoModel->role_id = $createData['role_id'];
        $adminCoModel->username = $createData['username'];
        $adminCoModel->password = md5('123456');
        $adminCoModel->nickname = $createData['nickname'];
        $adminCoModel->status = $createData['status'];

        return $adminCoModel->save();
    }

    /**
     * @param string $username
     * @param string $password
     * @return array
     * @throws Exception
     */
    public function login(string $username, string $password): array
    {
        $adminModel = new AdminModel();
        $admin = $adminModel->login($username, $password);
        if ($admin) {
            $admin->permission = $this->getRolePermission((int)$admin->role_id);
            return $admin->toArray();
        } else {
            return [];
        }
    }

    public function setSessionInfo()
    {

    }

    /**
     * @param string $sessionId
     * @param int $uid
     * @return array
     * @throws Exception
     */
    public function info(string $sessionId, int $uid)
    {
        $adminModel = new AdminModel();
        $result = $adminModel->getFirst(['id' => $uid])->toArray();
        if ($result) {
            $result['session_id'] = $sessionId;
            $result['password'] = '*';
            $admin = (new AdminProperty())->setProperty($result);
            $admin->permission = $this->getRolePermission((int)$admin->role_id);
            $sessionObject = new SessionObject();
            $sessionObject->setParam($admin->toArray());
            $sessionObject->setSessionData($admin->toArray());
            return $admin->toArray();
        } else {
            return [];
        }
    }

    /**
     * @param int $roleId
     * @return array
     * @throws \Exception
     */
    public function getRolePermission(int $roleId): array
    {
        $tree = $permissionItem = [];

        $permission = (new AdminRolePermissionModel())->getRolePermission($roleId);
        if ($permission !== false && $permission->permission_list) {
            $permissionItem = (new AdminPermissionModel())->getPermission($permission->permission_list);
            $tree = $this->getPermissionTree($permissionItem);
        }

        return [
            'tree' => $tree,
            'permission_uri' => array_column($permissionItem, 'uri')
        ];
    }

    /**
     * 获取一颗组装好的权限树
     * @param array $items
     * @return array
     */
    private function getPermissionTree($items): array
    {
        $tree = []; // 格式化好的树

        /* 把数组的key跟id对应上 */
        $items_index = [];
        foreach ($items as $item) {
            $item = (array)$item;
            $items_index[$item['id']] = $item;
        }

        foreach ($items_index as $item) {
            if (isset($items_index[$item['pid']])) {
                $items_index[$item['pid']]['children'][] = &$items_index[$item['id']];
            } else {
                $tree[] = &$items_index[$item['id']];
            }
        }
        return $tree;
    }

    /**
     * 修改密码
     * @param int $id
     * @param array $requestData
     * @return array
     * @throws \Exception
     */
    public function changePwd(int $id, array $requestData): array
    {
        if ($requestData['new_password'] != $requestData['re_new_password']) {
            return [
                'status' => false,
                'message' => '新密码与确认新密码不一致'
            ];
        }

        $adminModel = new AdminModel();
        $adminInfo = $adminModel->info($id);
        if (!$adminInfo) {
            return [
                'status' => false,
                'message' => '查询管理员信息失败'
            ];
        }

        if ($adminInfo->password != md5($requestData['old_password'])) {
            return [
                'status' => false,
                'message' => '原密码验证失败'
            ];
        }

        if ($adminModel->updatePassword($id, $requestData['new_password'])) {
            return [
                'status' => true,
                'message' => '修改密码成功'
            ];
        } else {
            return [
                'status' => false,
                'message' => '修改密码失败'
            ];
        }
    }

    /**
     * 获取用户数据权限树
     * @param $adminId
     * @return array
     */
    public function getDataPermissionTree($adminId)
    {
        // 查询管理员信息
        $admin = (new AdminModel())->builder->where(['id' => $adminId])->first();

        $tree = [];
        $treeSelectKey = [];
        $treeNodeId = 0;
        // 查询所有的平台信息
        $platformList = (new PlatformModel())->builder->where(['status' => 1])->get()->toArray();
        foreach ($platformList as $platform) {
            $treeNode = ['tree_node_id' => ++$treeNodeId, 'id' => $platform['id'], 'label' => $platform['name'], 'type' => 'platform'];
            // 判断当前用户有没有平台权限
            if (in_array($platform['id'], explode(',', $admin['platform_id_list']))) {
                $treeSelectKey[] = $treeNodeId;
            }

            // 查询平台对应的游戏类型
            $gameTypeList = (new GameTypeModel())->builder->where(['status' => 1, 'platform_id' => $platform['id']])->get();
            foreach ($gameTypeList as $gameType) {
                $lastTreeNode = ['tree_node_id' => ++$treeNodeId, 'id' => $gameType['id'], 'label' => $gameType['name'], 'type' => 'gameType'];
                // 判断当前用户有没有游戏类型权限
                if (in_array($gameType['id'], explode(',', $admin['game_type_id_list']))) {
                    $treeSelectKey[] = $treeNodeId;
                }

                // 查询游戏类型对应的游戏
                $gameList = (new GameModel())->builder->where(['status' => 1, 'platform_id' => $platform['id'], 'game_type_id' => $gameType['id']])->get();
                foreach ($gameList as $game) {
                    $lastTreeNode['children'][] = ['tree_node_id' => ++$treeNodeId, 'id' => $game['id'], 'label' => $game['name'], 'type' => 'game'];
                    // 判断当前用户有没有游戏权限
                    if (in_array($game['id'], explode(',', $admin['game_id_list']))) {
                        $treeSelectKey[] = $treeNodeId;
                    }
                }
                $treeNode['children'][] = $lastTreeNode;
            }
            array_push($tree, $treeNode);
        }

        return ['treeData' => $tree, 'treeSelectKey' => $treeSelectKey];
    }

    /**
     * 更新用户数据权限
     * @param $requestData
     * @return int
     */
    public function updateDataPermission($requestData)
    {
        $updateData = [
            'platform_id_list' => $requestData['platform'] ? implode(',', $requestData['platform']) : '',
            'game_type_id_list' => $requestData['gameType'] ? implode(',', $requestData['gameType']) : '',
            'game_id_list' => $requestData['game'] ? implode(',', $requestData['game']) : '',
        ];

        $result = (new AdminModel())->updateOne($updateData, ['id' => $requestData['id']]);
        return $result;
    }
}