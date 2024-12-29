<?php

namespace App\Modules\Base\Models;

use App\Modules\BaseModel;
use App\Modules\Base\BizSrc\VisitSaveParams;
use App\Modules\Base\BizSrc\VisitSearchParams;
use App\Utils\Utils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VisitModel extends BaseModel
{
    protected $table = 'visit';
    public $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    /**
     * @param VisitSaveParams $paramsObj
     * @return int
     */
    public function insertData($paramsObj): int
    {
        $paramsData = $this->formatSetData($paramsObj);
        if (empty($paramsData)) {
            return 0;
        }

        $paramsData['create_time'] = date('Y-m-d H:i:s');

        return DB::table($this->table)->insertGetId($paramsData);
    }

    /**
     * 检查数据是否存在
     *
     * @param $code
     * @return bool
     */
    public function isExist($code): bool
    {
        return DB::table($this->table)
            ->select(['id'])
            ->where('code', '=', $code)
            ->exists();
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
     * @param VisitSearchParams $paramsObj
     * @return int
     */
    public function getCount($paramsObj): int
    {
        return parent::getCount($paramsObj);
    }

    /**
     * @param VisitSearchParams $paramsObj
     * @return Collection
     */
    public function getPageData($paramsObj): Collection
    {
        return parent::getPageData($paramsObj);
    }

    /**
     * @param $paramsObj
     * @return Builder
     */
    protected function formatSearchObj($paramsObj): Builder
    {
        return DB::table($this->table);
    }
}
