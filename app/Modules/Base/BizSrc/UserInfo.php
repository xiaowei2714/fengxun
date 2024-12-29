<?php

namespace App\Modules\Base\BizSrc;

use App\Modules\BaseInfo;
use App\Utils\Utils;

class UserInfo extends BaseInfo
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
    public function setOpenId($openId): UserInfo
    {
        $this->openId = $openId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOpenId()
    {
        return $this->openId;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return !empty($this->name) ? $this->name : Utils::USER_NAME;
    }

    /**
     * @return mixed
     */
    public function getFileCode()
    {
        return $this->fileCode;
    }

    /**
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->filePath;
    }


    /**
     * @return string
     */
    public function getUserFileUrl(): string
    {
        return !empty($this->filePath) ? $this->filePath : Utils::USER_IMAGE;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getAgreePolicyShow(): string
    {
        return Utils::statusShow($this->agreePolicy ?? 0);
    }

    /**
     * @return mixed
     */
    public function getAgreePolicy()
    {
        return $this->agreePolicy;
    }

    /**
     * @param $channel
     * @return $this
     */
    public function setChannel($channel): UserInfo
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChannel()
    {
        return $this->channel;
    }
}
