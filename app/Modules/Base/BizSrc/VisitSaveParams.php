<?php

namespace App\Modules\Base\BizSrc;

use App\Modules\BaseSaveParams;

class VisitSaveParams extends BaseSaveParams
{
    protected $ip;
    protected $userCode;
    protected $channel;

    /**
     * @param $ip
     * @return $this
     */
    public function setIp($ip): VisitSaveParams
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @param $userCode
     * @return $this
     */
    public function setUserCode($userCode): VisitSaveParams
    {
        $this->userCode = $userCode;
        return $this;
    }

    /**
     * @param $channel
     * @return $this
     */
    public function setChannel($channel): VisitSaveParams
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setCreateParams($params): VisitSaveParams
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
    public function setEditParams($params, $code): VisitSaveParams
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
    public function setParams($params): VisitSaveParams
    {
        parent::setParams($params);
        if (!empty($this->getErrorCode()) || !empty($this->getErrorMsg())) {
            return $this;
        }

        if (isset($params['ip'])) {
            $this->setIp($params['ip']);
        }
        if (isset($params['user_code'])) {
            $this->setUserCode($params['user_code']);
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
            'ip' => 'string|max:64',
            'user_code' => 'string|max:64',
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
            'ip.*' => '传入IP地址值异常',
            'user_code.*' => '传入用户编码值异常',
            'channel.*' => '传入渠道 0：微信，1：支付宝值异常',
        ];

        return array_merge(parent::checkMessage(), $newData);
    }
}
