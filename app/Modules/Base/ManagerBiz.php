<?php

namespace App\Modules\Base;

use App\Modules\BaseBiz;
use App\Modules\Base\BizSrc\ManagerInfo;
use App\Modules\Base\Models\ManagerModel;
use App\Modules\Base\BizSrc\ManagerSaveParams;
use App\Modules\Base\BizSrc\ManagerSearchParams;
use Exception;

class ManagerBiz extends BaseBiz
{
    /**
     * @param bool $newModel
     * @param bool $newInfo
     */
    public function __construct(bool $newInfo = false, bool $newModel = true)
    {
        if ($newModel) {
            $this->setModelObj((new ManagerModel()));
        }

        if ($newInfo) {
            $this->setInfoObj((new ManagerInfo()));
        }
    }

    /**
     * 获取Model实体类
     *
     * @return ManagerModel
     * @throws Exception
     */
    public function getModelObj(): ManagerModel
    {
        return parent::getModelObj();
    }

    /**
     * 获取Info实体类
     *
     * @return ManagerInfo
     * @throws Exception
     */
    public function getInfoObj(): ManagerInfo
    {
        return parent::getInfoObj();
    }

    /**
     * 保存数据 新增
     *
     * @param ManagerSaveParams $paramsObj
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
     * @param ManagerSaveParams $paramsObj
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
     * @param ManagerSaveParams $paramsObj
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
     * 更新密码
     *
     * @param ManagerSaveParams $paramsObj
     * @return bool
     * @throws Exception
     */
    public function setPassword(ManagerSaveParams $paramsObj): bool
    {
        return $this->updateData($paramsObj);
    }

    /**
     * 验证密码
     *
     * @param $password
     * @param $encryptPassword
     * @return bool
     */
    public function checkPassword($password, $encryptPassword): bool
    {
        if (empty($password) || empty($encryptPassword)) {
            return false;
        }

        return password_verify($password, $encryptPassword);
    }

    /**
     * 获取详情
     *
     * @param $code
     * @return ManagerInfo
     * @throws Exception
     */
    public function getInfo($code): ManagerInfo
    {
        return parent::getInfo($code);
    }

    /**
     * 获取管理员详情
     *
     * @param $account
     * @return ManagerInfo
     * @throws Exception
     */
    public function getInfoByAccount($account): ManagerInfo
    {
        $data = $this->getModelObj()->getInfoByAccount($account);
        if (empty($data->code)) {
            return $this->getInfoObj();
        }

        return $this->getInfoObj()->dbChangeInfo($data);
    }

    /**
     * 获取搜索总数
     *
     * @param ManagerSearchParams $paramsObj
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
     * @param ManagerSearchParams $paramsObj
     * @return array
     * @throws Exception
     */
    public function getPageData($paramsObj): array
    {
        return parent::getPageData($paramsObj);
    }
}
