<?php

namespace App\Modules;

use App\Utils\Utils;

class BaseSearchParams
{
    protected $pageNum = 1;
    protected $limitNum = 10;
    protected $show;
    protected $usable = Utils::USABLE;
    protected $search = '';

    protected $errorCode;

    /**
     * @return int
     */
    public function getPageNum(): int
    {
        return $this->pageNum;
    }

    /**
     * @param $pageNum
     * @return $this
     */
    public function setPageNum($pageNum)
    {
        $this->pageNum = (int)$pageNum;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimitNum()
    {
        return $this->limitNum;
    }

    /**
     * @param $limitNum
     * @return $this
     */
    public function setLimitNum($limitNum)
    {
        $this->limitNum = (int)$limitNum;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShow()
    {
        return $this->show;
    }

    /**
     * @param int $show
     * @return $this
     */
    public function setShow(int $show)
    {
        $this->show = $show;
        return $this;
    }

    /**
     * @return int
     */
    public function getUsable(): int
    {
        return $this->usable;
    }

    /**
     * @param int $usable
     * @return $this
     */
    public function setUsable(int $usable)
    {
        $this->usable = $usable;
        return $this;
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param $search
     * @return $this
     */
    public function setSearch($search)
    {
        $this->search = (string)$search;
        return $this;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setParams($params): BaseSearchParams
    {
        if (empty($params) || !is_array($params)) {
            return $this;
        }

        if (isset($params['show']) && strlen($params['show']) === 1) {
            $tmpShow = Utils::statusFlip($params['show']);
            if ($tmpShow !== '') {
                $this->setShow($tmpShow);
            }
        }

        if (!empty($params['page_num']) && is_numeric($params['page_num'])) {
            $this->setPageNum($params['page_num']);
        }

        if (isset($params['limit_num']) && is_numeric($params['limit_num']) && $params['limit_num'] > 0 && $params['limit_num'] <= 1000) {
            $this->setLimitNum($params['limit_num']);
        }

        if (isset($params['search']) && is_string($params['search']) && strlen($params['search']) < 100) {
            $this->setSearch($params['search']);
        }

        return $this;
    }

    /**
     * 转化为DB数组
     *
     * @return array
     */
    public function getParams(): array
    {
        $objVars = get_object_vars($this);
        $notFields = ['id', 'errorMsg'];
        $data = [];
        foreach ($objVars as $key => $val) {
            if ($val === null) {
                continue;
            }
            if (in_array($key, $notFields)) {
                continue;
            }

            $tmpKey = strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $key));
            $data[$tmpKey] = $val;
        }

        return $data;
    }

    /**
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param $errorCode
     * @return $this
     */
    public function setErrorCode($errorCode): BaseSearchParams
    {
        $this->errorCode = $errorCode;
        return $this;
    }
}
