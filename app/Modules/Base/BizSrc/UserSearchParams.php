<?php

namespace App\Modules\Base\BizSrc;

use App\Modules\BaseSearchParams;

class UserSearchParams extends BaseSearchParams
{
    /**
     * @param $params
     * @return UserSearchParams
     */
    public function setParams($params): UserSearchParams
    {
        return parent::setParams($params);
    }
}
