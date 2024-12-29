<?php

namespace App\Modules\Base\Models;

use App\Modules\BaseModel;
use App\Modules\Base\BizSrc\ManagerSaveParams;
use App\Modules\Base\BizSrc\ManagerSearchParams;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ManagerModel extends BaseModel
{
    protected $table = 'manager';
    public $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    /**
     * @param ManagerSaveParams $paramsObj
     * @return int
     */
    public function insertData($paramsObj): int
    {
        return parent::insertData($paramsObj);
    }

    /**
     * @param $code
     * @param ManagerSaveParams $paramsObj
     * @return bool
     */
    public function updateData($code, $paramsObj): bool
    {
        return parent::updateData($code, $paramsObj);
    }

    /**
     * @param $code
     * @return bool
     */
    public function updateDel($code): bool
    {
        return parent::updateDel($code);
    }

    /**
     * @param $code
     * @return Model|Builder|object|null
     */
    public function getInfo($code)
    {
        return parent::getInfo($code);
    }

    /**
     * 获取详情
     *
     * @param $account
     * @return Model|Builder|object|null
     */
    public function getInfoByAccount($account)
    {
        return DB::table($this->table)->where('account', '=', $account)->first();
    }

    /**
     * @param ManagerSearchParams $paramsObj
     * @return int
     */
    public function getCount($paramsObj): int
    {
        return parent::getCount($paramsObj);
    }

    /**
     * @param ManagerSearchParams $paramsObj
     * @return Collection
     */
    public function getPageData($paramsObj): Collection
    {
        return parent::getPageData($paramsObj);
    }
}
