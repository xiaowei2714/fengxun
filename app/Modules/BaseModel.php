<?php

namespace App\Modules;

use App\Utils\Utils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    protected $sorts = [
        ['sort' => 'id', 'by' => 'desc']
    ];

    /**
     * 插入一条数据
     *
     * @param $paramsObj
     * @return int
     */
    public function insertData($paramsObj): int
    {
        $paramsData = $this->formatSetData($paramsObj);
        if (empty($paramsData)) {
            return 0;
        }

        $paramsData['create_time'] = date('Y-m-d H:i:s');
        $paramsData['update_time'] = date('Y-m-d H:i:s');

        return DB::table($this->table)->insertGetId($paramsData);
    }

    /**
     * 更新数据
     *
     * @param $code
     * @param $paramsObj
     * @return bool
     */
    public function updateData($code, $paramsObj): bool
    {
        $paramsData = $this->formatSetData($paramsObj);
        if (empty($paramsData)) {
            return false;
        }

        $paramsData['update_time'] = date('Y-m-d H:i:s');
        unset($paramsData['id']);
        unset($paramsData['code']);
        unset($paramsData['create_time']);

        $res = DB::table($this->table)
            ->where('code', '=', $code)
            ->update($paramsData);

        return !empty($res);
    }

    /**
     * 置为展示
     *
     * @param $code
     * @return bool
     */
    public function updateShow($code): bool
    {
        $res = DB::table($this->table)
            ->where('code', '=', $code)
            ->update([
                'show' => Utils::USABLE,
                'update_time' => date('Y-m-d H:i:s')
            ]);

        return !empty($res);
    }

    /**
     * 置为不展示
     *
     * @param $code
     * @return bool
     */
    public function updateHide($code): bool
    {
        $res = DB::table($this->table)
            ->where('code', '=', $code)
            ->update([
                'show' => Utils::DISABLE,
                'update_time' => date('Y-m-d H:i:s')
            ]);

        return !empty($res);
    }

    /**
     * 置为删除
     *
     * @param $code
     * @return bool
     */
    public function updateDel($code): bool
    {
        $res = DB::table($this->table)
            ->where('code', '=', $code)
            ->update([
                'usable' => Utils::DISABLE,
                'update_time' => date('Y-m-d H:i:s')
            ]);

        return !empty($res);
    }

    /**
     * 检查数据是否存在
     *
     * @param $code
     * @return bool
     */
    public function isExist($code): bool
    {
        $obj = DB::table($this->table)
            ->select(['id'])
            ->where('code', '=', $code)
            ->where('usable', '=', Utils::USABLE);

        return $obj->exists();
    }

    /**
     * 获取详情
     *
     * @param $code
     * @return Model|Builder|object|null
     */
    public function getInfo($code)
    {
        return DB::table($this->table)
            ->where('code', '=', $code)
            ->first();
    }

    /**
     * 获取数据
     *
     * @param $codes
     * @return Collection
     */
    public function getData($codes): Collection
    {
        return DB::table($this->table)
            ->whereIn('code', $codes)
            ->get();
    }

    /**
     * 获取数据
     *
     * @param $orderCode
     * @return Collection
     */
    public function getDataByOrderCode($orderCode): Collection
    {
        return DB::table($this->table)
            ->where('order_code', '=', $orderCode)
            ->get();
    }

    /**
     * 获取数据
     *
     * @param $orderCodes
     * @return Collection
     */
    public function getDataByOrderCodes($orderCodes): Collection
    {
        return DB::table($this->table)
            ->whereIn('order_code', $orderCodes)
            ->get();
    }

    /**
     * 获取总数
     *
     * @param $paramsObj
     * @return int
     */
    public function getCount($paramsObj): int
    {
        return $this->formatSearchObj($paramsObj)->count();
    }

    /**
     * 获取分页数据
     *
     * @param $paramsObj
     * @return Collection
     */
    public function getPageData($paramsObj): Collection
    {
        $obj = $this->formatSearchObj($paramsObj)
            ->limit($paramsObj->getLimitNum())
            ->offset(($paramsObj->getPageNum() - 1) * $paramsObj->getLimitNum());

        foreach ($this->sorts as $value) {
            $obj = $obj->orderBy($value['sort'], $value['by']);
        }

        return $obj->get();
    }

    /**
     * 获取当前最大排序
     *
     * @return mixed
     */
    public function getMaxSort()
    {
        return DB::table($this->table)
            ->where('usable', '=', Utils::USABLE)
            ->max('sort');
    }

    /**
     * 格式化写入数据
     *
     * @param $paramsObj
     * @return array
     */
    protected function formatSetData($paramsObj): array
    {
        return $paramsObj->changeDbData();
    }

    /**
     * @param $paramsObj
     * @return Builder
     */
    protected function formatSearchObj($paramsObj): Builder
    {
        $obj = DB::table($this->table);

        if ($paramsObj->getUsable() !== '') {
            $obj = $obj->where('usable', '=', $paramsObj->getUsable());
        }
        if ($paramsObj->getSearch() !== '') {
            $obj = $obj->where('name', 'like', '%' . $paramsObj->getSearch() . '%');
        }

        return $obj;
    }

    /**
     * @return string[][]
     */
    public function getSort(): array
    {
        return $this->sorts;
    }

    /**
     * @param array $sort
     * @return $this
     */
    public function setSort(array $sort): BaseModel
    {
        $this->sorts = $sort;
        return $this;
    }
}
