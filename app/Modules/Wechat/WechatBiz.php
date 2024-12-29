<?php

namespace App\Modules\Wechat;

use App\Modules\Wechat\BizSrc\SessionResult;
use Illuminate\Support\Facades\Log;
use Exception;

class WechatBiz extends WechatBaseBiz
{
    const REQUEST_HOST = 'https://api.weixin.qq.com';

    /**
     * 小程序登录 code换取session
     *
     * @param $appId
     * @param $appSecret
     * @param $jsCode
     * @return SessionResult
     * @throws Exception
     */
    public function codeToSession($appId, $appSecret, $jsCode): SessionResult
    {
        $url = rtrim(self::REQUEST_HOST, '/') . '/sns/jscode2session';
        $params = [
            'appid' => $appId,
            'secret' => $appSecret,
            'js_code' => $jsCode,
            'grant_type' => 'authorization_code',
        ];

        // 请求header头
        $requestHeader = [
            'content-type: x-www-form-urlencoded'
        ];

        $data = $this->setHeader($requestHeader)->request($url, $params);

        // response
        return (new SessionResult())->setParams($data);
    }
}
