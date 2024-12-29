<?php

namespace App\Modules\Base\BizSrc;

use App\Modules\BaseSaveParams;
use App\Utils\SubCode;
use App\Utils\Utils;

class CompanySaveParams extends BaseSaveParams
{
    protected $params;

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setParams($params): CompanySaveParams
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setEditParams($params): CompanySaveParams
    {
        if (empty($params)) {
            return $this->setErrorCode(SubCode::PARAMS_ERROR);
        }

        $keysConfig = CompanyKeys::getAllKeys();
        $newParams = [];
        foreach ($params as $key => $value) {
            if (!isset($keysConfig[$key])) {
                continue;
            }

            $tmpValue = null;

            if ($keysConfig[$key] === 'string') {
                $tmpValue = (string)$value;
                if (strlen($tmpValue) > 64) {
                    return $this->setErrorMsg('保存的数据过长了');
                }
            }

            if ($keysConfig[$key] === 'int') {
                $tmpValue = (int)$value;
            }

            if ($key === 'usable') {
                $usableValue = Utils::statusFlip($value);
                if ($usableValue === '') {
                    continue;
                }

                $tmpValue = $usableValue;
            }

            if ($tmpValue === null) {
                return $this->setErrorMsg('保存的数据异常');
            }

            $newParams[$key] = $tmpValue;
        }

        if (empty($newParams)) {
            return $this->setErrorCode(SubCode::PARAMS_ERROR);
        }

        return $this->setParams($newParams);
    }
}
