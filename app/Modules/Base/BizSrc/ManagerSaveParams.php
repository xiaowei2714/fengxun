<?php

namespace App\Modules\Base\BizSrc;

use App\Modules\BaseSaveParams;

class ManagerSaveParams extends BaseSaveParams
{
    protected $account;
    protected $name;
    protected $password;

    /**
     * @param $account
     * @return $this
     */
    public function setAccount($account): ManagerSaveParams
    {
        $this->account = $account;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name): ManagerSaveParams
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password): ManagerSaveParams
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setCreateParams($params): ManagerSaveParams
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
    public function setEditParams($params, $code): ManagerSaveParams
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
    public function setParams($params): ManagerSaveParams
    {
        parent::setParams($params);
        if (!empty($this->getErrorCode()) || !empty($this->getErrorMsg())) {
            return $this;
        }

        if (isset($params['account'])) {
            $this->setAccount($params['account']);
        }
        if (isset($params['name'])) {
            $this->setName($params['name']);
        }
        if (isset($params['password'])) {
            $this->setPassword($params['password']);
        }

        return $this;
    }

    /**
     * 检查密码
     *
     * @param $params
     * @return $this
     */
    public function setPasswordParams($params): ManagerSaveParams
    {
        // 原密码
        if (empty($params['ori_password'])) {
            $this->errorMsg = '原密码为必填项';
            return $this;
        }

        $res = $this->checkPassword($params);
        if (!$res) {
            return $this;
        }
        if ($params['ori_password'] === $this->getPassword()) {
            $this->errorMsg = '新旧密码一致，请重新填写新密码';
            return $this;
        }

        return $this;
    }

    /**
     * 检查密码
     *
     * @param $params
     * @return bool
     */
    private function checkPassword($params): bool
    {
        if (empty($params['password']) || empty($params['re_password'])) {
            $this->errorMsg = '密码为必填项';
            return false;
        }
        if ($params['password'] !== $params['re_password']) {
            $this->errorMsg = '两次密码不一致';
            return false;
        }
        if (!preg_match('/^[a-zA-Z0-9]+[._-]*$/', $params['password'])) {
            $this->errorMsg = '密码由字母、数字、下划线组成';
            return false;
        }
        if (strlen($params['password']) > 32) {
            $this->errorMsg = '密码长度不能过长';
            return false;
        }

        // 密码加密
        $encryptPassword = password_hash($params['password'], PASSWORD_DEFAULT);
        if (empty($encryptPassword)) {
            $this->errorMsg = '密码机密失败';
            return false;
        }

        $this->setPassword($encryptPassword);
        return true;
    }

    /**
     * 检查规则
     *
     * @return string[]
     */
    protected function checkRules(): array
    {
        $newData = [
            'account' => 'string|max:64',
            'name' => 'string|max:64',
            'password' => 'string|max:64',
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
            'account.*' => '传入用户编码值异常',
            'name.*' => '传入用户名称值异常',
            'password.*' => '传入密码值异常',
        ];

        return array_merge(parent::checkMessage(), $newData);
    }
}
