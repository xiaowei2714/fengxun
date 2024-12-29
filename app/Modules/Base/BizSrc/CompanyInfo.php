<?php

namespace App\Modules\Base\BizSrc;

use App\Utils\Utils;

class CompanyInfo
{
    protected $name;
    protected $short;
    protected $logoCode;
    protected $wechatAppId;
    protected $wechatAppSecret;
    protected $wechatMchId;
    protected $usable;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name): CompanyInfo
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShort()
    {
        return $this->short;
    }

    /**
     * @param $short
     * @return $this
     */
    public function setShort($short): CompanyInfo
    {
        $this->short = $short;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLogoCode()
    {
        return $this->logoCode;
    }

    /**
     * @param $logoCode
     * @return $this
     */
    public function setLogoCode($logoCode): CompanyInfo
    {
        $this->logoCode = $logoCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWechatAppId()
    {
        return $this->wechatAppId;
    }

    /**
     * @param $wechatAppId
     * @return $this
     */
    public function setWechatAppId($wechatAppId): CompanyInfo
    {
        $this->wechatAppId = $wechatAppId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWechatAppSecret()
    {
        return $this->wechatAppSecret;
    }

    /**
     * @param $wechatAppSecret
     * @return $this
     */
    public function setWechatAppSecret($wechatAppSecret): CompanyInfo
    {
        $this->wechatAppSecret = $wechatAppSecret;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWechatMchId()
    {
        return $this->wechatMchId;
    }

    /**
     * @param $wechatMchId
     * @return $this
     */
    public function setWechatMchId($wechatMchId): CompanyInfo
    {
        $this->wechatMchId = $wechatMchId;
        return $this;
    }

    /**
     * @return bool
     */
    public function getUsableShow(): bool
    {
        return Utils::statusShow($this->usable);
    }

    /**
     * @return bool
     */
    public function getUsable(): bool
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
    public function isDisable(): bool
    {
        return $this->usable == Utils::DISABLE;
    }

    /**
     * @return $this
     */
    public function setUsable($usable): CompanyInfo
    {
        $this->usable = $usable;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function dbChangeInfo(array $data): CompanyInfo
    {
        foreach ($data as $value) {
            $tmpValue = $value->content;
            switch ($value->key) {
                case 'short':
                    $this->setShort($tmpValue);
                    break;

                case 'name':
                    $this->setName($tmpValue);
                    break;

                case 'logo_code':
                    $this->setLogoCode($tmpValue);
                    break;

                case 'wechat_app_id':
                    $this->setWechatAppId($tmpValue);
                    break;

                case 'wechat_app_secret':
                    $this->setWechatAppSecret($tmpValue);
                    break;

                case 'wechat_mch_id':
                    $this->setWechatMchId($tmpValue);
                    break;

                case 'usable':
                    $this->setUsable($tmpValue);
                    break;
            }
        }

        return $this;
    }
}
