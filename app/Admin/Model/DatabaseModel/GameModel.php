<?php

namespace App\Admin\Model\DatabaseModel;

use Illuminate\Database\Query\Builder;

class GameModel extends BaseModel
{
    public function __construct(array $attributes = [])
    {
        $this->table = 'cv_game';
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
            if ($this->platformIdList) {
                $builder->whereIn('platform_id', explode(',', $this->platformIdList));
            }
        }
        if (isset($where['game_type_id']) && $where['game_type_id']) {
            $builder->where(['game_type_id' => $where['game_type_id']]);
        } else {
            if ($this->gameTypeIdList) {
                $builder->whereIn('game_type_id', explode(',', $this->gameTypeIdList));
            }
        }
        if (isset($where['id']) && $where['id']) {
            $builder->where(['id' => $where['id']]);
        } else {
            if ($this->gameIdList) {
                $builder->whereIn('id', explode(',', $this->gameIdList));
            }
        }
        if (isset($where['page']) && isset($where['pageSize'])) {
            $builder->skip(($where['page'] - 1) * $where['pageSize'])->limit($where['pageSize']);
            unset($where['page'], $where['pageSize']);
        }
        $builder->where(['status' => 1] + $where);
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

    /**
     * 根据id的list获取信息
     * @param array $gameIdList
     * @return \Illuminate\Support\Collection
     */
    public function getInfoByGameIdList(array $gameIdList)
    {
        $builder = $this->builder;
        $builder->whereIn('game_id', $gameIdList);
        $result = $builder->get();
        return $result;
    }

    /**
     * 根据主键id查询game_id
     * @param $id
     * @return int
     */
    public function getGameIdById(int $id)
    {
        $builder = $this->builder;
        $game = $builder->where(['id' => $id])->first();
        return $game ? $game['game_id'] : 0;
    }

    /**
     * 根据主键id列表查询game_id列表
     * @param string $idList
     * @return array
     */
    public function getGameIdListByIdList(string $idList)
    {
        $builder = $this->builder;
        $gameIdList = $builder->whereIn('id', explode(',', $idList))->get();
        if ($gameIdList) {
            return array_column($gameIdList->toArray(), 'game_id');
        }

        return [];
    }

    /**
     * 获取所有游戏
     * @return \Illuminate\Support\Collection
     */
    public function getAllGame()
    {
        return $this->builder
            ->select(['platform_id', 'game_type_id', 'game_id'])
            ->groupBy(['platform_id', 'game_type_id', 'game_id'])
            ->get();
    }
}