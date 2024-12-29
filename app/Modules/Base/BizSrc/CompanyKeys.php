<?php

namespace App\Modules\Base\BizSrc;

class CompanyKeys
{
    const BASE_KEYS = [
        'short' => 'string',                // 简称
        'name' => 'string',                 // 名称
        'logo_code' => 'string',            // 店铺logo
        'wechat_app_id' => 'string',        // 微信app id
        'wechat_app_secret' => 'string',    // 微信app secret
        'wechat_mch_id' => 'string',        // 微信商户号
        'usable' => 'int',                  // 是否可用
    ];

    /**
     * @return array
     */
    private static function getExtendKeys(): array
    {
        return [

        ];
    }

    /**
     * @return array|string[]
     */
    public static function getAllKeys(): array
    {
        return array_merge(self::BASE_KEYS, self::getExtendKeys());
    }
}
