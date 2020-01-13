<?php

namespace App\Admin\Model\DatabaseModel;

use Illuminate\Database\Query\Builder;
use App\Admin\Property\AdminRolePermissionProperty;

class AdminRolePermissionModel extends BaseModel
{
    protected $fillable = ['role_id', 'permission_list'];

    public function __construct(array $attributes = [])
    {
        $this->table = 'cv_admin_role_permission';
        parent::__construct($attributes);
    }

    /**
     * 需要子类重写此方法
     * @param array $where 查询条件
     * @param array $orderBy 排序条件
     * @return Builder 查询构造器对象
     */
    protected function getCondition($where, $orderBy = []): Builder
    {
        $builder = $this->builder;
        if (isset($where['role_id'])) {
            $builder->where(['role_id' => $where['role_id']]);
        }
        return $builder;
    }

    /**
     * 获取角色权限对象
     * @param $roleId
     * @return AdminRolePermissionProperty|null
     * @throws \Exception
     */
    public function getRolePermission($roleId)
    {
        $result = $this->getFirst(['role_id' => $roleId]);
        if ($result) {
            return (new AdminRolePermissionProperty())->setProperty($result);
        }

        return null;
    }
}
