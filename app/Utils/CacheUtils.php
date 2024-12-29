<?php

namespace App\Utils;

class CacheUtils
{
    /**
     * 缓存key-公司详情
     *
     * @return string
     */
    public static function getCompanyInfoKey(): string
    {
        return 'COMPANY_INFO';
    }

    /**
     * 缓存时间-公司详情
     *
     * @return int
     */
    public static function getCompanyInfoExpireTime(): int
    {
        return 86400;
    }
}
