<?php

namespace App\Modules\Base\BizSrc;

use App\Modules\BaseInfo;

class FileInfo extends BaseInfo
{
    protected $name;
    protected $path;
    protected $mime;
    protected $size;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }
}
