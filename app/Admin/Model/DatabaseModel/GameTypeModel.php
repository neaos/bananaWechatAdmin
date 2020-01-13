<?php

namespace App\Admin\Model\DatabaseModel;

use Illuminate\Database\Query\Builder;

class GameTypeModel extends BaseModel
{
    public function __construct(array $attributes = [])
    {
        $this->table = 'cv_game_type';
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
        if (isset($where['platform_id']) && $where['platform_id']) {
            $builder->where(['platform_id' => $where['platform_id']]);
        } else {
            if($this->platformIdList) {
                $builder->whereIn('platform_id', explode(',', $this->platformIdList));
            }
        }
        if (isset($where['id']) && $where['id']) {
            $builder->where(['id' => $where['id']]);
        } else {
            if($this->gameTypeIdList){
                $builder->whereIn('id', explode(',', $this->gameTypeIdList));
            }
        }
        $builder->where(['status' => 1]);
        $builder->orderBy('id', 'desc');
        return $builder;
    }

    /**
     * 根据id的list获取信息
     * @param array $idList
     * @return \Illuminate\Support\Collection
     */
    public function getInfoByIdList(array $idList)
    {
        $builder = $this->builder;
        $builder->whereIn('id', $idList);
        $result = $builder->get();
        return $result;
    }

}