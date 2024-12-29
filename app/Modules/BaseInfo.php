<?php

namespace App\Modules;

use App\Utils\Utils;

class BaseInfo
{
    protected $code;
    protected $show;
    protected $usable;
    protected $createTime;
    protected $updateTime;

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code): BaseInfo
    {
        $this->code = $code;
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
     * @return bool
     */
    public function isShow(): bool
    {
        return $this->show == Utils::USABLE;
    }

    /**
     * @return bool
     */
    public function isHide(): bool
    {
        return $this->show == Utils::DISABLE;
    }

    /**
     * @param $show
     * @return $this
     */
    public function setShow($show): BaseInfo
    {
        $this->show = $show;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsable()
    {
        return $this->usable;
    }

    /**
     * @return bool
     */
    public function isUsable(): bool
    {
        return $this->usable == Utils::USABLE;
    }

    /**
     * @return bool
     */
    public function isDel(): bool
    {
        return $this->usable == Utils::DISABLE;
    }

    /**
     * @param $usable
     * @return $this
     */
    public function setUsable($usable): BaseInfo
    {
        $this->usable = $usable;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * @param $createTime
     * @return $this
     */
    public function setCreateTime($createTime): BaseInfo
    {
        $this->createTime = $createTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * @param mixed $updateTime
     */
    public function setUpdateTime($updateTime): BaseInfo
    {
        $this->updateTime = $updateTime;
        return $this;
    }

    /**
     * @param $dbInfo
     * @return $this
     */
    public function dbChangeInfo($dbInfo): BaseInfo
    {
        foreach (get_object_vars($this) as $key => $value) {
            $tmpKey = strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $key));
            if(!isset($dbInfo->$tmpKey)){
                continue;
            }

            $this->$key = $dbInfo->$tmpKey;
        }

        return $this;
    }

    /**
     * 转化为数组
     *
     * @return array
     */
    public function changeData(): array
    {
        $objVars = get_object_vars($this);

        $data = [];
        foreach ($objVars as $key => $val) {
            if ($val === null) {
                continue;
            }

            $tmpKey = strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $key));
            $data[$tmpKey] = $val;
        }

        return $data;
    }
}
