<?php

namespace App\Modules\Base\BizSrc;

use App\Modules\BaseSaveParams;

class UserSaveParams extends BaseSaveParams
{
    protected $openId;
    protected $name;
    protected $fileCode;
    protected $filePath;
    protected $phone;
    protected $agreePolicy;
    protected $channel;

    /**
     * @param $openId
     * @return $this
     */
    public function setOpenId($openId): UserSaveParams
    {
        $this->openId = $openId;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name): UserSaveParams
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param $fileCode
     * @return $this
     */
    public function setFileCode($fileCode): UserSaveParams
    {
        $this->fileCode = $fileCode;
        return $this;
    }

    /**
     * @param $filePath
     * @return $this
     */
    public function setFilePath($filePath): UserSaveParams
    {
        $this->filePath = $filePath;
        return $this;
    }

    /**
     * @param $phone
     * @return $this
     */
    public function setPhone($phone): UserSaveParams
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @param $agreePolicy
     * @return $this
     */
    public function setAgreePolicy($agreePolicy): UserSaveParams
    {
        $this->agreePolicy = $agreePolicy;
        return $this;
    }

    /**
     * @param $channel
     * @return $this
     */
    public function setChannel($channel): UserSaveParams
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setCreateParams($params): UserSaveParams
    {
        if (!empty($params['code'])) {
            return $this->setErrorMsg('新增不应传入编码值');
        }

        unset($params['code']);
        return $this->setParams($params);
    }

    /**
     * @param $params
     * @param $code
     * @return $this
     */
    public function setEditParams($params, $code): UserSaveParams
    {
        if (empty($code)) {
            return $this->setErrorMsg('编辑时应传入编码值');
        }

        $params['code'] = $code;
        return $this->setParams($params);
    }

    /**
     * @param $params
     * @return $this
     */
    public function setParams($params): UserSaveParams
    {
        parent::setParams($params);
        if (!empty($this->getErrorCode()) || !empty($this->getErrorMsg())) {
            return $this;
        }

        if (isset($params['open_id'])) {
            $this->setOpenId($params['open_id']);
        }
        if (isset($params['phone'])) {
            $this->setPhone($params['phone']);
        }
        if (isset($params['agree_policy'])) {
            $this->setAgreePolicy($params['agree_policy']);
        }
        if (isset($params['channel'])) {
            $this->setChannel($params['channel']);
        }

        return $this;
    }

    /**
     * 检查规则
     *
     * @return string[]
     */
    protected function checkRules(): array
    {
        $newData = [
            'open_id' => 'string|max:64',
            'phone' => 'string|max:16',
            'agree_policy' => 'numeric|max:2',
            'channel' => 'numeric|max:2',
        ];

        return array_merge(parent::checkRules(), $newData);
    }

    /**
     * 错误提示
     *
     * @return string[]
     */
    protected function checkMessage(): array
    {
        $newData = [
            'open_id.*' => '传入open id值异常',
            'phone.*' => '传入用户手机号值异常',
            'agree_policy.*' => '传入是否同意规则 1：同意，0：不同意值异常',
            'channel.*' => '传入类型 0：微信，1：支付宝值异常',
        ];

        return array_merge(parent::checkMessage(), $newData);
    }
}
