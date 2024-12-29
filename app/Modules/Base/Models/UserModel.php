<?php

namespace App\Modules\Base\Models;

use App\Modules\BaseModel;
use App\Modules\Base\BizSrc\UserSaveParams;
use App\Modules\Base\BizSrc\UserSearchParams;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserModel extends BaseModel
{
    protected $table = 'user';
    public $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    /**
     * @param UserSaveParams $paramsObj
     * @return int
     */
    public function insertData($paramsObj): int
    {
        return parent::insertData($paramsObj);
    }

    /**
     * @param $code
     * @param UserSaveParams $paramsObj
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
     * @param $channel
     * @param $openId
     * @return Model|Builder|object|null
     */
    public function getInfoByOpenId($channel, $openId)
    {
        return DB::table($this->table)
            ->where('open_id', '=', $openId)
            ->where('channel', '=', $channel)
            ->first();
    }

    /**
     * @param UserSearchParams $paramsObj
     * @return int
     */
    public function getCount($paramsObj): int
    {
        return parent::getCount($paramsObj);
    }

    /**
     * @param UserSearchParams $paramsObj
     * @return Collection
     */
    public function getPageData($paramsObj): Collection
    {
        return parent::getPageData($paramsObj);
    }
}
