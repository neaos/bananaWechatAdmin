<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/30
 * Time: 19:44
 */

namespace App\Admin\Object;

class SessionObject
{
    public $sessionId;

    public $id;

    public $username;

    public $nickname;

    public $name;

    public $avatar;

    public $roleId;

    public $permission;

    public $platformIdList;

    public $gameTypeIdList;

    public $gameIdList;

    /**
     * MessageObject constructor.
     * @param string $sessionId
     */
    public function __construct(string $sessionId = null)
    {
        if ($sessionId) {
            $sessionInfo = $_SESSION['bwa']['admin'] ?? [];
            if ($sessionInfo) {
                $this->sessionId = $sessionInfo['session_id'] ?? '';
                $this->id = $sessionInfo['id'] ?? '';
                $this->username = $sessionInfo['username'] ?? '';
                $this->nickname = $sessionInfo['nickname'] ?? '';
                $this->name = $sessionInfo['name'] ?? '';
                $this->avatar = $sessionInfo['avatar'] ?? '';
                $this->roleId = $sessionInfo['role_id'] ?? '';
                $this->permission = $sessionInfo['permission'] ?? '';
                $this->platformIdList = $sessionInfo['platform_id_list'] ?? '';
                $this->gameTypeIdList = $sessionInfo['game_type_id_list'] ?? '';
                $this->gameIdList = $sessionInfo['game_id_list'] ?? '';
            }
        }
    }

    /**
     * @param array $data
     * @return SessionObject
     */
    public function setParam(array $data)
    {
        $this->sessionId = $data['session_id'] ?? '';
        $this->id = $data['id'] ?? '';
        $this->username = $data['username'] ?? '';
        $this->nickname = $data['nickname'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->avatar = $data['avatar'] ?? '';
        $this->roleId = $data['role_id'] ?? '';
        $this->permission = $data['permission'] ?? '';
        $this->platformIdList = $data['platform_id_list'] ?? '';
        $this->gameTypeIdList = $data['game_type_id_list'] ?? '';
        $this->gameIdList = $data['game_id_list'] ?? '';
        return $this;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function setSessionData(array $data)
    {
        $_SESSION['bwa']['admin'] = $data;
        return true;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function delSessionData(array $data)
    {
        $_SESSION['bwa']['admin'] = [];
        return true;
    }
}