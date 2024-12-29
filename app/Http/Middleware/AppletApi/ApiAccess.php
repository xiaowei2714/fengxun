<?php

namespace App\Http\Middleware\AppletApi;

use App\Utils\SubCode;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiAccess
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $startTime = $this->micTime();
            $logData = [
                'uri' => $request->getUri(),
                'method' => $request->getMethod(),
                'token' => $request->headers->get('token') ?? null,
                'input' => $request->input(),
                'response' => null
            ];

            $res = $next($request);

            if (strpos($request->getUri(), 'shop/list') === false || strpos($request->getUri(), 'voucher/list') === false) {
                $logData['response'] = $res->getContent();
            }

            // 允许跨域请求
            $res->headers->set("Access-Control-Allow-Origin", "*");

            $endTime = $this->micTime();
            Log::channel('api_log')->info('Api', ['start_time' => date('Y-m-d H:i:s', $startTime / 1000) . ':' . substr($startTime, -3), 'end_time' => date('Y-m-d H:i:s', $endTime / 1000) . ':' . substr($endTime, -3), 'total_time' => $endTime - $startTime, 'data' => $logData]);
            return $res;

        } catch (\Exception $e) {
            Log::channel('exception_log')->error('Api', ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return response()->json(['ret' => 0, 'sub' => SubCode::SYSTEM_EXCEPTION_ERROR, 'msg' => SubCode::ERROR_MSG[SubCode::SYSTEM_EXCEPTION_ERROR], 'data' => (object)[]]);
        }
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
