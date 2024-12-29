<?php

namespace App\Modules;

use App\Utils\SubCode;
use Exception;
use Illuminate\Support\Facades\DB;

class BaseBiz
{
    protected $modelObj;
    protected $infoObj;

    private $errorCode = '';
    private $errorMsg = '';

    /**
     * @return mixed
     * @throws Exception
     */
    public function getModelObj()
    {
        if (empty($this->modelObj) || !is_object($this->modelObj)) {
            throw new Exception('实例化Model类异常');
        }

        return $this->modelObj;
    }

    /**
     * @param $modelObj
     * @return $this
     */
    public function setModelObj($modelObj): BaseBiz
    {
        $this->modelObj = $modelObj;
        return $this;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getInfoObj()
    {
        if (empty($this->infoObj) || !is_object($this->infoObj)) {
            throw new Exception('实例化Info类异常');
        }

        return $this->infoObj;
    }

    /**
     * @param $infoObj
     * @return $this
     */
    public function setInfoObj($infoObj)
    {
        $this->infoObj = $infoObj;
        return $this;
    }

    /**
     * 新建一条数据
     *
     * @param $paramsObj
     * @param bool $needAutoCode // 需要自动生成编码
     * @return bool
     * @throws Exception
     */
    public function insertData($paramsObj, bool $needAutoCode = true): bool
    {
        if ($needAutoCode) {
            $paramsObj->autoSetCode();
            if (empty($paramsObj->getCode())) {
                $this->setErrorMsg('生成Code值异常');
                return false;
            }
        }

        if (!empty($paramsObj->getCode())) {
            $checkRes = $this->checkExist($paramsObj->getCode());
            if ($checkRes) {
                $this->setErrorMsg('系统异常，请稍后重试');
                return false;
            }
        }

        $id = $this->getModelObj()->insertData($paramsObj);
        if (empty($id)) {
            return false;
        }

        return true;
    }

    /**
     * 更新数据
     *
     * @param $paramsObj
     * @return bool
     * @throws Exception
     */
    public function updateData($paramsObj): bool
    {
        $res = $this->getModelObj()->updateData($paramsObj->getCode(), $paramsObj);
        if (!$res) {
            return false;
        }

        return true;
    }

    /**
     * 保存数据
     *
     * @param BaseSaveParams $paramsObj
     * @return bool
     * @throws Exception
     */
    public function setData($paramsObj): bool
    {
        // 检查是否存在
        $checkRes = $this->checkExist($paramsObj->getCode());
        if (!$checkRes) {
            $this->setErrorCode(SubCode::ABNORMAL_ACCESS);
            return false;
        }

        // update
        $res = $this->updateData($paramsObj);
        if (!$res) {
            return false;
        }

        return true;
    }

    /**
     * 保存数据
     *
     * @param BaseSaveParams $paramsObj
     * @return bool
     * @throws Exception
     */
    public function saveData($paramsObj): bool
    {
        DB::beginTransaction();

        if (!empty($paramsObj->getCode())) {
            $res = $this->setData($paramsObj);
        } else {    // insert
            $res = $this->insertData($paramsObj);
        }
        if (!$res) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }

    /**
     * 置为上架
     *
     * @param $code
     * @return bool
     * @throws Exception
     */
    public function updateShow($code): bool
    {
        return $this->getModelObj()->updateShow($code);
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
        DB::beginTransaction();

        // 检查是否存在
        $checkRes = $this->checkExist($code);
        if (!$checkRes) {
            $this->setErrorCode(SubCode::ABNORMAL_ACCESS);
            DB::rollBack();
            return false;
        }

        // update
        $res = $this->updateShow($code);
        if (!$res) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }

    /**
     * 置为下架
     *
     * @param $code
     * @return bool
     * @throws Exception
     */
    public function updateHide($code): bool
    {
        return $this->getModelObj()->updateHide($code);
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
        DB::beginTransaction();

        // 检查是否存在
        $checkRes = $this->checkExist($code);
        if (!$checkRes) {
            $this->setErrorCode(SubCode::ABNORMAL_ACCESS);
            DB::rollBack();
            return false;
        }

        // down
        $res = $this->updateHide($code);
        if (!$res) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }

    /**
     * 置为删除
     *
     * @param $code
     * @return bool
     * @throws Exception
     */
    public function updateDel($code): bool
    {
        return $this->getModelObj()->updateDel($code);
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
        DB::beginTransaction();

        // 检查是否存在
        $checkRes = $this->checkExist($code);
        if (!$checkRes) {
            $this->setErrorCode(SubCode::ABNORMAL_ACCESS);
            DB::rollBack();
            return false;
        }

        // del
        $res = $this->updateDel($code);
        if (!$res) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }

    /**
     * 检查是否存在
     *
     * @param $code
     * @return bool
     * @throws Exception
     */
    public function checkExist($code): bool
    {
        return $this->getModelObj()->isExist($code);
    }

    /**
     * 获取详情
     *
     * @param $code
     * @return mixed
     * @throws Exception
     */
    public function getInfo($code)
    {
        $data = $this->getModelObj()->getInfo($code);
        if (empty($data->id)) {
            return $this->getInfoObj();
        }

        return $this->getInfoObj()->dbChangeInfo($data);
    }

    /**
     * 获取总数
     *
     * @param $paramsObj
     * @return int
     * @throws Exception
     */
    public function getCount($paramsObj): int
    {
        return $this->getModelObj()->getCount($paramsObj);
    }

    /**
     * 分页数据
     *
     * @param $paramsObj
     * @return array
     * @throws Exception
     */
    public function getPageData($paramsObj): array
    {
        $data = $this->getModelObj()->getPageData($paramsObj);
        if ($data->isEmpty()) {
            return [];
        }

        $newData = [];
        $data->each(function ($item) use (&$newData) {
            $tmpInfoObj = clone $this->getInfoObj();
            $newData[] = $tmpInfoObj->dbChangeInfo($item);
        });

        return $newData;
    }

    /**
     * 获取数据
     *
     * @param $codes
     * @return array
     * @throws Exception
     */
    public function getDataByCodes($codes): array
    {
        $data = $this->getModelObj()->getData($codes);
        if ($data->isEmpty()) {
            return [];
        }

        $newData = [];
        $data->each(function ($item) use (&$newData) {
            $tmpInfoObj = clone $this->getInfoObj();
            $newData[$item->code] = $tmpInfoObj->dbChangeInfo($item);
        });

        return $newData;
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     * @return $this
     */
    public function setErrorCode(string $errorCode): BaseBiz
    {
        $this->errorCode = $errorCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorMsg(): string
    {
        return $this->errorMsg;
    }

    /**
     * @param string $errorMsg
     * @return $this
     */
    protected function setErrorMsg(string $errorMsg): BaseBiz
    {
        $this->errorMsg = $errorMsg;
        return $this;
    }
}
