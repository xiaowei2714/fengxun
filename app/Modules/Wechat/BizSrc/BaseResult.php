<?php

namespace App\Modules\Wechat\BizSrc;

use App\Utils\Utils;

class BaseResult
{
    protected $response;
    protected $errorCode;       // 错误码
    protected $errorMsg;        // 错误信息

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param $response
     * @return $this
     */
    public function setResponse($response): BaseResult
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFail(): bool
    {
        if ($this->getErrorCode() !== null && $this->getErrorCode() !== 0) {
            return true;
        }

        return false;
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
    public function setErrorCode($errorCode): BaseResult
    {
        $this->errorCode = $errorCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

    /**
     * @param $errorMsg
     * @return $this
     */
    public function setErrorMsg($errorMsg): BaseResult
    {
        $this->errorMsg = $errorMsg;
        return $this;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setParams($params): BaseResult
    {
        if (isset($params['errcode'])) {
            $this->setErrorMsg($params['errcode']);
        }

        if (isset($params['errmsg'])) {
            $this->setErrorMsg($params['errmsg']);
        }

        return $this;
    }
}
