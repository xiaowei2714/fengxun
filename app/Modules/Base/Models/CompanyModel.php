<?php

namespace App\Modules\Base\Models;

use App\Modules\BaseModel;
use App\Utils\Utils;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CompanyModel extends BaseModel
{
    protected $table = 'company';
    public $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    /**
     * @param $params
     * @return int
     */
    public function insertCompanyData($params): int
    {
        return DB::table($this->table)->insertGetId([
            'key' => $params['key'],
            'content' => $params['content'],
            'usable' => $params['usable'],
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @param $params
     * @return bool
     */
    public function updateDataByKey($params): bool
    {
        $res = DB::table($this->table)
            ->where('key', '=', $params['key'])
            ->update([
                'content' => $params['content'],
                'usable' => $params['usable'],
                'update_time' => date('Y-m-d H:i:s'),
            ]);

        return !empty($res);
    }

    /**
     * 置为删除
     *
     * @param $keys
     * @return bool
     */
    public function updateDelByKeys($keys): bool
    {
        $res = DB::table($this->table)
            ->whereIn('key', $keys)
            ->update([
                'usable' => Utils::DISABLE,
                'update_time' => date('Y-m-d H:i:s'),
            ]);

        return !empty($res);
    }

    /**
     * 获取所有数据
     * @return Collection
     */
     public function getAllData(): Collection
     {
         return DB::table($this->table)->select(['key', 'content', 'usable'])->get();
     }

    /**
     * 获取所有可用数据
     * @return Collection
     */
    public function getUsableData(): Collection
    {
        return DB::table($this->table)
            ->select(['key', 'content'])
            ->where('usable', '=', Utils::USABLE)
            ->orderBy('id')
            ->limit(100)
            ->get();
    }
}
