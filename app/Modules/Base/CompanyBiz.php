<?php

namespace App\Modules\Base;

use App\Modules\Base\BizSrc\CompanyInfo;
use App\Modules\Base\BizSrc\CompanySaveParams;
use App\Modules\Base\Models\CompanyModel;
use App\Modules\BaseBiz;
use App\Utils\CacheUtils;
use App\Utils\Utils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;

class CompanyBiz  extends BaseBiz
{
    /**
     * @param bool $newModel
     * @param bool $newInfo
     */
    public function __construct(bool $newInfo = true, bool $newModel = true)
    {
        if ($newModel) {
            $this->setModelObj((new CompanyModel()));
        }

        if ($newInfo) {
            $this->setInfoObj((new CompanyInfo()));
        }
    }

    /**
     * 获取Model实体类
     *
     * @return CompanyModel
     * @throws Exception
     */
    public function getModelObj(): CompanyModel
    {
        return parent::getModelObj();
    }

    /**
     * 获取Info实体类
     *
     * @return CompanyInfo
     * @throws Exception
     */
    public function getInfoObj(): CompanyInfo
    {
        return parent::getInfoObj();
    }

    /**
     * 保存数据
     *
     * @param CompanySaveParams $paramsObj
     * @return bool
     * @throws Exception
     */
    public function saveData($paramsObj): bool
    {
        $curData = $this->getAllData();

        $addParams = $setUsableParams = $delKeys =[];
        foreach ($paramsObj->getParams() as $key => $value) {
            $tmpData = [
                'key' => $key,
                'content' => $value,
                'usable' => Utils::USABLE,
            ];
            if (isset($curData[$key])) {
                $setUsableParams[] = $tmpData;
            } else {
                $addParams[] = $tmpData;
            }
        }

        // 筛选出需要置为不可用数据
        foreach ($curData as $key => $value) {
            if (!$value['usable']) {
                continue;
            }
            if (isset($paramsObj->getParams()[$key])) {
                continue;
            }

            $delKeys[] = $key;
        }

        if (empty($addParams) && empty($setUsableParams) && empty($delKeys)) {
            return true;
        }

        DB::beginTransaction();

        // 新增之前没有的数据
        if (!empty($addParams)) {
            foreach ($addParams as $params) {
                $res = $this->getModelObj()->insertCompanyData($params);
                if (!$res) {
                    $this->setErrorMsg('新增数据失败：' . json_encode($params));
                    DB::rollBack();
                    return false;
                }
            }
        }

        // 变更为可用
        if (!empty($setUsableParams)) {
            foreach ($setUsableParams as $params) {
                $res = $this->getModelObj()->updateDataByKey($params);
                if (!$res) {
                    $this->setErrorMsg('置为可用失败：' . json_encode($params));
                    DB::rollBack();
                    return false;
                }
            }
        }

        // 批量变更为不可用
        if (!empty($delKeys)) {
            $res = $this->getModelObj()->updateDelByKeys($delKeys);
            if (!$res) {
                $this->setErrorMsg('删除数据失败：' . json_encode($delKeys));
                DB::rollBack();
                return false;
            }
        }

        // 更新缓存r
        $this->getCompanyInfoByCache(true);

        DB::commit();
        return true;
    }

    /**
     * 获取详情
     *
     * @return CompanyInfo
     * @throws Exception
     */
    public function getCompanyInfo(): CompanyInfo
    {
        $data = $this->getModelObj()->getUsableData();
        if ($data->isEmpty()) {
            return $this->getInfoObj();
        }

        return $this->getInfoObj()->dbChangeInfo($data->toArray());
    }

    /**
     * 获取详情
     *
     * @param bool $genCache
     * @return CompanyInfo
     * @throws Exception
     */
    public function getCompanyInfoByCache(bool $genCache = false): CompanyInfo
    {
        $cacheKey = CacheUtils::getCompanyInfoKey();

        if (!$genCache) {
            $cacheData = Cache::get($cacheKey);
            if (!empty($cacheData)) {
                return $cacheData;
            }
        }

        $data = $this->getCompanyInfo();

        Cache::put($cacheKey, $data, CacheUtils::getCompanyInfoExpireTime());
        return $data;
    }

    /**
     * 获取所有可用数据
     *
     * @return array
     * @throws Exception
     */
    public function getAllData(): array
    {
        $data = $this->getModelObj()->getAllData();
        if ($data->isEmpty()) {
            return [];
        }

        $newData = [];
        $data->each(function ($item) use (&$newData) {
            $newData[$item->key] = [
                'content' => $item->content,
                'usable' => $item->usable === Utils::USABLE
            ];
        });

        return $newData;
    }
}
