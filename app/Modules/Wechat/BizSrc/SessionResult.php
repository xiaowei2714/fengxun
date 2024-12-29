<?php

namespace App\Modules\Wechat\BizSrc;

class SessionResult extends BaseResult
{
    protected $sessionKey;      // 会话密钥
    protected $unionId;         // 用户在开放平台的唯一标识符
    protected $openId;          // 用户唯一标识

    /**
     * @return mixed
     */
    public function getSessionKey()
    {
        return $this->sessionKey;
    }

    /**
     * @param $sessionKey
     * @return $this
     */
    public function setSessionKey($sessionKey): SessionResult
    {
        $this->sessionKey = $sessionKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnionId()
    {
        return $this->unionId;
    }

    /**
     * @param $unionId
     * @return $this
     */
    public function setUnionId($unionId): SessionResult
    {
        $this->unionId = $unionId;
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
     * @param $openId
     * @return $this
     */
    public function setOpenId($openId): SessionResult
    {
        $this->openId = $openId;
        return $this;
    }

    /**
     * @param $params
     * @return $this|SessionResult
     */
    public function setParams($params): SessionResult
    {
        $this->setResponse($params);
        if (empty($params)) {
            return $this;
        }

        if (is_string($params)) {
            $params = json_decode($params, true);
        }

        parent::setParams($params);

        if (isset($params['openid'])) {
            $this->setOpenId($params['openid']);
        }

        if (isset($params['session_key'])) {
            $this->setSessionKey($params['session_key']);
        }

        if (isset($params['unionid'])) {
            $this->setUnionId($params['unionid']);
        }

        return $this;
    }
}
