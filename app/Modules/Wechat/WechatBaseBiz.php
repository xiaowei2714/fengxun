<?php

namespace App\Modules\Wechat;

use Illuminate\Support\Facades\Log;
use Exception;

class WechatBaseBiz
{
    protected array $header = [];
    private string $accessToken = '';

    /**
     * @param $url
     * @param array $params
     * @param string $method
     * @return bool|string
     * @throws Exception
     */
    protected function request($url, array $params = [], string $method = 'GET')
    {
        if (!function_exists('curl_init')) {
            throw new Exception('缺失curl扩展');
        }

        $startTime = $this->micTime();
        $method = strtoupper($method);
        $curl = curl_init();
        $option = [
            CURLOPT_USERAGENT => 'XW',
            CURLOPT_CONNECTTIMEOUT => 0,    // 在发起连接前等待的时间，如果设置为0，则无限等待。
            CURLOPT_TIMEOUT => 10,          // 设置CURL允许执行的最长秒数
            CURLOPT_RETURNTRANSFER => true, // 在启用CURLOPT_RETURNTRANSFER的时候，返回原生的（Raw）输出
            CURLOPT_HEADER => false         // 启用时会将头文件的信息作为数据流输出
        ];

        if (strtolower(substr($url, 0, 5)) == 'https') {
            $option[CURLOPT_SSL_VERIFYPEER] = false;
            $option[CURLOPT_SSL_VERIFYHOST] = false;
        }

        if (!empty($this->getHeader())) {
            $option[CURLOPT_HTTPHEADER] = $this->getHeader();
        }

        switch ($method) {
            case 'GET':
                if (!empty($params)) {
                    $url = $url . (strpos($url, '?') ? '&' : '?') . (is_array($params) ? http_build_query($params) : $params);
                }
                break;

            case 'POST':
                $option[CURLOPT_POST] = TRUE;
                if (!empty($params)) {
                    $option[CURLOPT_POSTFIELDS] = json_encode($params);
                }
                break;
        }

        $option[CURLOPT_URL] = $url;
        curl_setopt_array($curl, $option);
        $returnData = curl_exec($curl);
        curl_close($curl);

        $endTime = $this->micTime();

        // log record
        Log::channel('wechat')->info('wx_request', ['params' => func_get_args(), 'response' => $returnData, 'start_time' => $startTime, 'end_time' => $endTime, 'total_time' => $endTime - $startTime]);

        return $returnData;
    }

    /**
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * @param array $header
     * @return $this
     */
    public function setHeader(array $header): WechatBaseBiz
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param $accessToken
     * @return $this
     */
    public function setAccessToken($accessToken): WechatBaseBiz
    {
        $this->accessToken = (string)$accessToken;
        return $this;
    }

    /**
     * @return float
     */
    private function micTime(): float
    {
        list($msc, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($msc) + floatval($sec)) * 1000);
    }
}
