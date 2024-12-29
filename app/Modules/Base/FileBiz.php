<?php

namespace App\Modules\Base;

use App\Modules\BaseBiz;
use App\Modules\Base\BizSrc\FileInfo;
use App\Modules\Base\Models\FileModel;
use App\Modules\Base\BizSrc\FileSaveParams;
use App\Modules\Base\BizSrc\FileSearchParams;
use Exception;

class FileBiz extends BaseBiz
{
    /**
     * @param bool $newModel
     * @param bool $newInfo
     */
    public function __construct(bool $newInfo = false, bool $newModel = true)
    {
        if ($newModel) {
            $this->setModelObj((new FileModel()));
        }

        if ($newInfo) {
            $this->setInfoObj((new FileInfo()));
        }
    }

    /**
     * 获取Model实体类
     *
     * @return FileModel
     * @throws Exception
     */
    public function getModelObj(): FileModel
    {
        return parent::getModelObj();
    }

    /**
     * 获取Info实体类
     *
     * @return FileInfo
     * @throws Exception
     */
    public function getInfoObj(): FileInfo
    {
        return parent::getInfoObj();
    }

    /**
     * 保存数据 新增
     *
     * @param FileSaveParams $paramsObj
     * @param bool $needAutoCode
     * @return bool
     * @throws Exception
     */
    public function insertData($paramsObj, bool $needAutoCode = true): bool
    {
        return parent::insertData($paramsObj, $needAutoCode);
    }

    /**
     * 保存数据 新增
     *
     * @param FileSaveParams $paramsObj
     * @return bool
     * @throws Exception
     */
    public function updateData($paramsObj): bool
    {
        return parent::updateData($paramsObj);
    }

    /**
     * 保存数据 编辑
     *
     * @param FileSaveParams $paramsObj
     * @return bool
     * @throws Exception
     */
    public function setData($paramsObj): bool
    {
        return parent::setData($paramsObj);
    }

    /**
     * 置为上架
     *
     * @param $code
     * @return bool
     * @throws Exception
     */
    public function setShow($code): bool
    {
        return parent::setShow($code);
    }

    /**
     * 置为下架
     *
     * @param $code
     * @return bool
     * @throws Exception
     */
    public function setHide($code): bool
    {
        return parent::setHide($code);
    }

    /**
     * 置为删除
     *
     * @param $code
     * @return bool
     * @throws Exception
     */
    public function setDel($code): bool
    {
        return parent::setDel($code);
    }

    /**
     * 获取详情
     *
     * @param $code
     * @return FileInfo
     * @throws Exception
     */
    public function getInfo($code): FileInfo
    {
        return parent::getInfo($code);
    }

    /**
     * 获取搜索总数
     *
     * @param FileSearchParams $paramsObj
     * @return int
     * @throws Exception
     */
    public function getCount($paramsObj): int
    {
        return parent::getCount($paramsObj);
    }

    /**
     * 获取分页数据
     *
     * @param FileSearchParams $paramsObj
     * @return array
     * @throws Exception
     */
    public function getPageData($paramsObj): array
    {
        return parent::getPageData($paramsObj);
    }

    /**
     * 获取数据
     *
     * @param $codes
     * @return array
     */
    public function getDataByCodes($codes): array
    {
        $data = (new FileModel())->getData($codes);
        if ($data->isEmpty()) {
            return [];
        }

        $newData = [];
        $data->each(function ($item) use (&$newData) {
            $newData[$item->code] = (new FileInfo())->dbChangeInfo($item);
        });

        return $newData;
    }
}
