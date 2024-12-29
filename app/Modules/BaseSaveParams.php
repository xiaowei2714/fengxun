<?php

namespace App\Modules;

use Illuminate\Support\Facades\Validator;

class BaseSaveParams
{
    protected $id;
    protected $code;
    protected $errorCode;
    protected $errorMsg;
    protected array $notFields = [];

    public function __construct()
    {
        $this->notFields = ['id', 'errorMsg', 'notFields'];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = (string)$code;
        return $this;
    }

    /**
     * @return $this
     */
    public function autoSetCode()
    {
        $this->code = app('snowflake')->id();
        return $this;
    }

    /**
     * @return array
     */
    public function getNotFields(): array
    {
        return $this->notFields;
    }

    /**
     * @param array $notFields
     * @return $this
     */
    public function setNotFields(array $notFields)
    {
        $this->notFields = $notFields;
        return $this;
    }

    /**
     * 设置参数类
     *
     * @param $params
     * @return $this
     */
    public function setParams($params)
    {
        $validator = Validator::make($params, $this->checkRules(), $this->checkMessage());
        if ($validator->fails()) {
            $this->setErrorMsg($validator->errors()->first());
        }

        if (isset($params['id'])) {
            $this->setId($params['id']);
        }
        if (isset($params['code'])) {
            $this->setCode($params['code']);
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
        return [
            'id' => 'numeric|max:11',
            'code' => 'string|max:32',
        ];
    }

    /**
     * 错误提示
     *
     * @return string[]
     */
    protected function checkMessage(): array
    {
        return [
            'id.*' => '传入Id值异常',
            'code.*' => '传入Code值异常',
        ];
    }

    /**
     * 转化为DB数组
     *
     * @return array
     */
    public function changeDbData(): array
    {
        $objVars = get_object_vars($this);
        $notFields = array_values($this->getNotFields());

        $data = [];
        foreach ($objVars as $key => $val) {
            if ($val === null) {
                continue;
            }
            if (in_array($key, $notFields)) {
                continue;
            }

            $tmpKey = strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $key));
            $data[$tmpKey] = $val;
        }

        return $data;
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
    public function setErrorCode($errorCode)
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
    public function setErrorMsg($errorMsg)
    {
        $this->errorMsg = $errorMsg;
        return $this;
    }
}
