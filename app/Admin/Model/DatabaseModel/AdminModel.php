<?php

namespace App\Admin\Model\DatabaseModel;

use App\Admin\Property\AdminProperty;
use Exception;
use Illuminate\Database\Query\Builder;
use Library\Virtual\Model\DatabaseModel\AbstractMySqlModel;

/**
 * Created by PhpStorm.
 * User: ZhongHao-Zh
 * Date: 2019/10/26
 * Time: 20:18
 */
class AdminModel extends AbstractMySqlModel
{
    /**
     * AdminModel constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = 'cv_admin';
        parent::__construct($attributes);
    }

    /**
     * @param array $where 查询条件
     * @param array $orderBy 排序条件
     * @return Builder 查询构造器对象
     */
    protected function getCondition($where, $orderBy = []): Builder
    {
        $builder = $this->builder;
        if (isset($where['id'])) {
            $builder->where('id', '=', $where['id']);
        }

        if (isset($where['username'])) {
            $builder->where('username', '=', $where['username']);
        }

        if (isset($where['password'])) {
            $builder->where('password', '=', $where['password']);
        }

        if (isset($where['page']) && isset($where['pageSize'])) {
            $builder->skip(($where['page'] - 1) * $where['pageSize'])->limit($where['pageSize']);
            unset($where['page'], $where['pageSize']);
        }
        
        return $builder;
    }

    /**
     * 登陆检验
     * @param string $username
     * @param string $password
     * @return AdminProperty|null
     * @throws Exception
     */
    public function login(string $username, string $password)
    {
        $this->setListColumns([
            'id', 'username', 'nickname', 'role_id', 'create_time', 'update_time', 'last_login_time', 'status',
            'platform_id_list', 'game_type_id_list', 'game_id_list'
        ]);
        $result = $this->getFirst([
            'username' => $username,
            'password' => md5($password)
        ])->toArray();
        if ($result) {
            $result['password'] = '*';
            return (new AdminProperty())->setProperty($result);
        } else {
            return null;
        }
    }

    /**
     * 根据id获取用户信息
     * @param int $id
     * @return AdminProperty|null
     * @throws \Exception
     */
    public function info(int $id)
    {
        $result = $this->getFirst([
            'id' => $id,
        ]);
        if ($result) {
            return (new AdminProperty())->setProperty($result);
        } else {
            return null;
        }
    }

    /**
     * 根据id更新密码信息
     * @param $id
     * @param $password
     * @return int
     */
    public function updatePassword($id, $password)
    {
        $builder = $this->builder;
        return $builder->where(['id' => $id])->update(['password' => md5($password)]);
    }
}