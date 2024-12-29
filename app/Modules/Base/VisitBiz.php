<?php

namespace App\Modules\Base;

use App\Modules\BaseBiz;
use App\Modules\Base\BizSrc\VisitInfo;
use App\Modules\Base\Models\VisitModel;
use App\Modules\Base\BizSrc\VisitSaveParams;
use App\Modules\Base\BizSrc\VisitSearchParams;
use Exception;

class VisitBiz extends BaseBiz
{
    /**
     * @param bool $newModel
     * @param bool $newInfo
     */
    public function __construct(bool $newInfo = false, bool $newModel = true)
    {
        if ($newModel) {
            $this->setModelObj((new VisitModel()));
        }

        if ($newInfo) {
            $this->setInfoObj((new VisitInfo()));
        }
    }

    /**
     * 获取Model实体类
     *
     * @return VisitModel
     * @throws Exception
     */
    public function getModelObj(): VisitModel
    {
        return parent::getModelObj();
    }

    /**
     * 获取Info实体类
     *
     * @return VisitInfo
     * @throws Exception
     */
    public function getInfoObj(): VisitInfo
    {
        return parent::getInfoObj();
    }

    /**
     * 保存数据 新增
     *
     * @param VisitSaveParams $paramsObj
     * @param bool $needAutoCode
     * @return bool
     * @throws Exception
     */
    public function insertData($paramsObj, bool $needAutoCode = true): bool
    {
        return parent::insertData($paramsObj, $needAutoCode);
    }

    /**
     * 获取详情
     *
     * @param $code
     * @return VisitInfo
     * @throws Exception
     */
    public function getInfo($code): VisitInfo
    {
        return parent::getInfo($code);
    }

    /**
     * 获取搜索总数
     *
     * @param VisitSearchParams $paramsObj
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
     * @param VisitSearchParams $paramsObj
     * @return array
     * @throws Exception
     */
    public function getPageData($paramsObj): array
    {
        return parent::getPageData($paramsObj);
    }
}
