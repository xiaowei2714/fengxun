<?php

namespace App\Modules\Base;

use App\Modules\BaseBiz;
use App\Modules\Base\BizSrc\UserInfo;
use App\Modules\Base\Models\UserModel;
use App\Modules\Base\BizSrc\UserSaveParams;
use App\Modules\Base\BizSrc\UserSearchParams;
use App\Utils\Utils;
use Exception;

class UserBiz extends BaseBiz
{
    /**
     * @param bool $newModel
     * @param bool $newInfo
     */
    public function __construct(bool $newInfo = false, bool $newModel = true)
    {
        if ($newModel) {
            $this->setModelObj((new UserModel()));
        }

        if ($newInfo) {
            $this->setInfoObj((new UserInfo()));
        }
    }

    /**
     * 获取Model实体类
     *
     * @return UserModel
     * @throws Exception
     */
    public function getModelObj(): UserModel
    {
        return parent::getModelObj();
    }

    /**
     * 获取Info实体类
     *
     * @return UserInfo
     * @throws Exception
     */
    public function getInfoObj(): UserInfo
    {
        return parent::getInfoObj();
    }

    /**
     * 保存数据 新增
     *
     * @param UserSaveParams $paramsObj
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
     * @param UserSaveParams $paramsObj
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
     * @param UserSaveParams $paramsObj
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
     * 用户存在即返回详情，不存在则写入数据
     *
     * @param $channel
     * @param $openId
     * @return UserInfo
     * @throws Exception
     */
    public function checkAndSaveData($channel, $openId): UserInfo
    {
        $data = $this->getModelObj()->getInfoByOpenId($channel, $openId);
        if (empty($data->id)) { // 若不存在则新增
            $saveParamsObj = (new UserSaveParams())
                ->setOpenId($openId)
                ->setChannel($channel)
                ->autoSetCode();

            $res = $this->insertData($saveParamsObj);
            if (empty($res)) {
                return $this->getInfoObj();
            } else {
                return $this->getInfoObj()
                    ->setCode($saveParamsObj->getCode())
                    ->setOpenId($openId)
                    ->setChannel($channel)
                    ->setShow(Utils::USABLE)
                    ->setUsable(Utils::USABLE);
            }
        } else {
            return $this->getInfoObj()->dbChangeInfo($data);
        }
    }

    /**
     * 获取详情
     *
     * @param $code
     * @return UserInfo
     * @throws Exception
     */
    public function getInfo($code): UserInfo
    {
        return parent::getInfo($code);
    }

    /**
     * 获取搜索总数
     *
     * @param UserSearchParams $paramsObj
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
     * @param UserSearchParams $paramsObj
     * @return array
     * @throws Exception
     */
    public function getPageData($paramsObj): array
    {
        return parent::getPageData($paramsObj);
    }
}
