<?php

namespace App\Http\Middleware\Api;

use App\Modules\Base\TokenBiz;
use App\Utils\BackUtils;
use App\Utils\SubCode;
use Closure;
use Illuminate\Http\Request;

class AdminApiCheck
{
    /**
     *
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $newData = [
            'ret' => 0,
            'sub' => SubCode::SUB_CODE_TOKEN_FAIL,
            'msg' => SubCode::ERROR_MSG[SubCode::SUB_CODE_TOKEN_FAIL],
            'data' => (object)[]
        ];

        $token = $request->headers->get('token');
        if(empty($token)){
            return response()->json($newData);
        }

        $tokenConf = config('service.admin_params.token_conf');
        $tokenInfo = (new TokenBiz($tokenConf['project_name']))->getAccessTokenData($token);
        if (empty($tokenInfo)) {
            return response()->json($newData);
        }

        BackUtils::setTokenData($tokenInfo);
        return $next($request);
    }
}
