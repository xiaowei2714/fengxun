<?php

namespace App\Utils;

class Utils
{
    // 通用状态
    const USABLE = 1;           // 能用
    const DISABLE = 0;          // 不能用

    const USABLE_SHOW = 'Y';    // 能用
    const DISABLE_SHOW = 'N';   // 不能用

    // 渠道
    const WECHAT_CHANNEL = 0;   // 微信渠道
    const ALIPAY_CHANNEL = 1;   // 支付宝渠道

    const USER_NAME = '默认用户';
    const USER_IMAGE = '/image/user_default';

    /**
     * 状态配置
     *
     * @return string[]
     */
    public static function statusConf(): array
    {
        return [
            self::USABLE => self::USABLE_SHOW,
            self::DISABLE => self::DISABLE_SHOW,
        ];
    }

    /**
     * 状态展示
     *
     * @param $status
     * @return string
     */
    public static function statusShow($status): string
    {
        return self::statusConf()[$status] ?? '';
    }

    /**
     * 状态转换
     *
     * @param $status
     * @return string
     */
    public static function statusFlip($status): string
    {
        return array_flip(self::statusConf())[$status] ?? '';
    }

    /**
     * 上传目录
     *
     * @return string
     */
    public static function uploadDir(): string
    {
        return '';
    }

    /**
     * 获取完整上传内容路径
     *
     * @param $path
     * @return string
     */
    public static function getWholeUploadUrl($path): string
    {
        if (empty($path)) {
            return '';
        }

        return rtrim(env('APP_URL'), '/') . '/' . ltrim($path, '/');
    }
}
