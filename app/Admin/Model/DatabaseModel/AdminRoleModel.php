<?php

namespace App\Admin\Model\DatabaseModel;

use Illuminate\Database\Query\Builder;

class AdminRoleModel extends BaseModel
{
    protected $fillable = ['name', 'desc', 'state'];

    public function __construct(array $attributes = [])
    {
        $this->table = 'cv_admin_role';
        parent::__construct($attributes);
    }

    /**
     * @param array $where
     * @param array $orderBy
     * @return Builder
     */
    protected function getCondition($where, $orderBy = []): Builder
    {
        $builder = $this->builder;

        if (isset($where['page']) && isset($where['pageSize'])) {
            $builder->skip(($where['page'] - 1) * $where['pageSize'])->limit($where['pageSize']);
            unset($where['page'], $where['pageSize']);
        }

        $builder = $builder->where('id', '!=', 1);

        return $builder;
    }
}
