<?php

namespace App\Admin\Model\DatabaseModel;

class AdminPermissionModel extends BaseCoModel
{
    public function __construct(array $attributes = [])
    {
        $this->table = 'cv_admin_permission';
        parent::__construct($attributes);
    }

    public function getPermission($permissionIdStr)
    {
        $builder = $this->builder;
        $result = $builder->whereIn('id', explode(',', $permissionIdStr))->get();
        return $result ?: [];
    }

    public function getFullPermission()
    {
        $builder = $this->builder;
        $result = $builder->get();
        return $result ?: [];
    }
}