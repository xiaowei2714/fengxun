<?php

namespace App\Modules\Base\BizSrc;

use App\Modules\BaseSaveParams;

class FileSaveParams extends BaseSaveParams
{
    protected $name;
    protected $path;
    protected $mime;
    protected $size;

    /**
     * @param $name
     * @return $this
     */
    public function setName($name): FileSaveParams
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setPath($path): FileSaveParams
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param $mime
     * @return $this
     */
    public function setMime($mime): FileSaveParams
    {
        $this->mime = $mime;
        return $this;
    }

    /**
     * @param $size
     * @return $this
     */
    public function setSize($size): FileSaveParams
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setCreateParams($params): FileSaveParams
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
    public function setEditParams($params, $code): FileSaveParams
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
    public function setParams($params): FileSaveParams
    {
        parent::setParams($params);
        if (!empty($this->getErrorCode()) || !empty($this->getErrorMsg())) {
            return $this;
        }

        if (isset($params['name'])) {
            $this->setName($params['name']);
        }
        if (isset($params['path'])) {
            $this->setPath($params['path']);
        }
        if (isset($params['mime'])) {
            $this->setMime($params['mime']);
        }
        if (isset($params['size'])) {
            $this->setSize($params['size']);
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
            'name' => 'string|max:255',
            'path' => 'string|max:255',
            'mime' => 'string|max:255',
            'size' => 'numeric|max:9',
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
            'name.*' => '传入名称值异常',
            'path.*' => '传入存储路径值异常',
            'mime.*' => '传入文件类型值异常',
            'size.*' => '传入文件大小值异常',
        ];

        return array_merge(parent::checkMessage(), $newData);
    }
}
