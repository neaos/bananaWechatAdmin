<?php

namespace App\Common\Model\DatabaseModel;

use Illuminate\Database\Query\Builder;

/**
 * Created by PhpStorm.
 * User: ZhongHao-Zh
 * Date: 2019/10/26
 * Time: 20:18
 */
class AutoReplyModel extends BaseModel
{
    /**
     * AutoReplyModel constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = 'bwa_auto_reply';
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

        return $builder;
    }
}