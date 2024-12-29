<?php

namespace App\Modules\Base\Models;

use App\Modules\BaseModel;
use App\Modules\Base\BizSrc\FileSaveParams;
use App\Modules\Base\BizSrc\FileSearchParams;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FileModel extends BaseModel
{
    protected $table = 'files';
    public $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    /**
     * @param FileSaveParams $paramsObj
     * @return int
     */
    public function insertData($paramsObj): int
    {
        return parent::insertData($paramsObj);
    }

    /**
     * @param $code
     * @param FileSaveParams $paramsObj
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
     * @param FileSearchParams $paramsObj
     * @return int
     */
    public function getCount($paramsObj): int
    {
        return parent::getCount($paramsObj);
    }

    /**
     * @param FileSearchParams $paramsObj
     * @return Collection
     */
    public function getPageData($paramsObj): Collection
    {
        return parent::getPageData($paramsObj);
    }
}
