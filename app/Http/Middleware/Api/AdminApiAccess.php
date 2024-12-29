<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminApiAccess
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = $this->micTime();

        // log
        $logData = [
            'url' => $request->getUri(),
            'method' => $request->getMethod(),
            'token' => $request->headers->get('token'),
            'request' => $request->input(),
            'response' => null,
        ];

        try {
            $res = $next($request);

            // 允许跨域请求
            $res->headers->set("Access-Control-Allow-Origin", "*");

//            $logData['response'] = $res->getContent();

        } catch (\Exception $exception) {
            $logData['response'] = $exception->getMessage();
            $res = '';
        }

        $endTime = $this->micTime();
        Log::channel('api_log')->info('Api', ['start_time' => date('Y-m-d H:i:s', $startTime / 1000) . ':' . substr($startTime, -3), 'end_time' => date('Y-m-d H:i:s', $endTime / 1000) . ':' . substr($endTime, -3), 'total_time' => $endTime - $startTime, 'data' => $logData]);
        return $res;
    }

    /**
     * @return float
     */
    private function micTime()
    {
        list($msvc, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($msvc) + floatval($sec)) * 1000);
    }
}
