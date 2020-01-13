<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/11
 * Time: 15:11
 */

namespace App\Admin\Model\DatabaseModel;

use Illuminate\Database\Query\Builder;
use Library\Virtual\Model\DatabaseModel\AbstractMySqlModel;

class BaseModel extends AbstractMySqlModel
{
    /**
     * @var string $platformIdList
     */
    public $platformIdList;

    /**
     * @var string $gameTypeIdList
     */
    public $gameTypeIdList;

    /**
     * @var string $gameIdList
     */
    public $gameIdList;


    /**
     * 根据他的session数据设置他可看见的platform
     * @param $platformIdList
     */
    public function setSessionPlatformIdList(string $platformIdList)
    {
        $this->platformIdList = $platformIdList;
    }

    /**
     * 根据他的session数据设置他可看见的GameType
     * @param string $gameTypeIdList
     */
    public function setSessionGameTypeIdList(string $gameTypeIdList)
    {
        $this->gameTypeIdList = $gameTypeIdList;
    }

    /**
     * 根据他的session数据设置他可看见的GameType
     * @param string $gameIdList
     */
    public function setSessionGameIdList(string $gameIdList)
    {
        $this->gameIdList = $gameIdList;
    }

    /**
     * 需要子类重写此方法
     * @param array $where 查询条件
     * @param array $orderBy 排序条件
     * @return Builder 查询构造器对象
     */
    protected function getCondition($where, $orderBy = []): Builder
    {
        return $this->builder;
    }
}