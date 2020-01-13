<?php

namespace App\Admin\Model\DatabaseModel;

use Illuminate\Database\Query\Builder;

class AdminLogModel extends BaseModel
{
    public function __construct(array $attributes = [])
    {
        $this->table = 'cv_admin_log';
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

        if (isset($where['route']) && $where['route']) {
            $builder->where(['route' => $where['route']]);
        }

        if (isset($where['page']) && isset($where['pageSize'])) {
            $builder->skip(($where['page'] - 1) * $where['pageSize'])->limit($where['pageSize']);
            unset($where['page'], $where['pageSize']);
        }
        $builder->where($where);
        $builder->orderBy('id', 'desc');
        return $builder;
    }
}