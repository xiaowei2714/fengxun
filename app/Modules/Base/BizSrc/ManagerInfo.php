<?php

namespace App\Modules\Base\BizSrc;

use App\Modules\BaseInfo;

class ManagerInfo extends BaseInfo
{
    protected $account;
    protected $name;
    protected $password;

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->account;
    }

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
    public function getPassword()
    {
        return $this->password;
    }
}
