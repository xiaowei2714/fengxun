<?php

namespace App\Utils;

class AppletUtils
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
     * @return mixed
     */
    public static function getTokenMerchantCode()
    {
        if (empty(self::getTokenData())) {
            return '';
        }

        return self::getTokenData()['merchant_code'];
    }

    /**
     * @return mixed
     */
    public static function getTokenUserCode()
    {
        if (empty(self::getTokenData())) {
            return '';
        }

        return self::getTokenData()['user_code'];
    }

    /**
     * @return mixed
     */
    public static function getTokenUserOpenId()
    {
        if (empty(self::getTokenData())) {
            return '';
        }


        return self::getTokenData()['open_id'];
    }

    /**
     * @return mixed
     */
    public static function getTokenUserSessionKey()
    {
        if (empty(self::getTokenData())) {
            return '';
        }

        return self::getTokenData()['session_key'];
    }

    /**
     * @return mixed
     */
    public static function getTokenMerchantAppId()
    {
        if (empty(self::getTokenData())) {
            return '';
        }

        return self::getTokenData()['app_id'];
    }

    /**
     * @return mixed
     */
    public static function getTokenMerchantAppSecret()
    {
        if (empty(self::getTokenData())) {
            return '';
        }

        return self::getTokenData()['app_secret'];
    }
}
