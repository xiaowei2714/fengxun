<?php

namespace App\Modules\Base\BizSrc;

use App\Modules\BaseInfo;

class VisitInfo extends BaseInfo
{
    protected $ip;
    protected $userCode;
    protected $channel;

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return mixed
     */
    public function getUserCode()
    {
        return $this->userCode;
    }

    /**
     * @return mixed
     */
    public function getChannel()
    {
        return $this->channel;
    }
}
