<?php

namespace App\Utils;

use App\Modules\Base\BizSrc\ManagerInfo;

class BackUtils
{
    public static $tokenData;

    /**
     * @param $tokenData
     * @return void
     */
    public static function setTokenData($tokenData)
    {
        self::$tokenData = $tokenData;
    }

    /**
     * @return mixed
     */
    public static function getTokenData()
    {
        return self::$tokenData;
    }

    /**
     * @return ManagerInfo|null
     */
    public static function getTokenManagerInfo(): ?ManagerInfo
    {
        if (empty(self::getTokenData())) {
            return null;
        }

        return self::getTokenData()['manager_info'];
    }

    /**
     * @return mixed|string
     */
    public static function getTokenManagerCode()
    {
        if (empty(self::getTokenManagerInfo())) {
            return '';
        }

        return self::getTokenManagerInfo()->getCode();
    }
}
