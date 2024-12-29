<?php

namespace App\Http\Controllers\BackPlatform;

use App\Modules\Base\BizSrc\ManagerSaveParams;
use App\Modules\Base\ManagerBiz;
use App\Modules\Base\TokenBiz;
use App\Utils\BackUtils;
use App\Utils\SubCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class ManagerController extends BackController
{
    /**
     * 登录
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $username = $request->input('username');
            $password = $request->input('password');
            if (empty($username) || empty($password)) {
                return $this->setCode(SubCode::PARAMS_ERROR)->outPut((object)[]);
            }

            $managerBiz = new ManagerBiz(true);

            // 获取用户详情
            $managerInfo = $managerBiz->getInfoByAccount($username);
            if (empty($managerInfo->getCode())) {
                return $this->setCode(SubCode::ACCOUNT_PASSWORD_ERROR)->outPut((object)[]);
            }
            if ($managerInfo->isDel() || $managerInfo->isHide()) {
                return $this->setCode(SubCode::ACCOUNT_DISABLED_ERROR)->outPut((object)[]);
            }
            if (!$managerBiz->checkPassword($password, $managerInfo->getPassword())) {
                return $this->setCode(SubCode::ACCOUNT_PASSWORD_ERROR)->outPut((object)[]);
            }

            // 获取token 并 记录登录数据
            $tokenData = ['manager_info' => $managerInfo];

            // 获取token
            $tokenConf = config('service.admin_params.token_conf');
            $tokenBiz = (new TokenBiz($tokenConf['project_name']))
                ->setQueueLength($tokenConf['max_login_account_num'])
                ->setExpireTime($tokenConf['expire_time'])
                ->setRefreshExpireTime($tokenConf['refresh_expire_time'])
                ->genAccessToken($tokenData, $managerInfo->getCode())
                ->genRefreshToken($managerInfo->getCode());

            if (empty($tokenBiz->getAccessToken())) {
                return $this->setCode(SubCode::TOKEN_ERROR)->outPut((object)[]);
            }

            return $this->outPut([
                'token' => $tokenBiz->getAccessToken(),
                'refreshToken' => $tokenBiz->getRefreshToken(),
                'managerName' => $managerInfo->getName()
            ]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->uri(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }

    /**
     * 修改密码
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {

            $params = $request->input();

            // 设置参数 并 校验参数
            $paramsObj = (new ManagerSaveParams())->setPasswordParams($params);
            if (!empty($paramsObj->getErrorMsg())) {
                return $this->setCode(SubCode::PARAMS_ERROR)->setMsg($paramsObj->getErrorMsg())->outPut((object)[]);
            }
            $paramsObj->setCode(BackUtils::getTokenManagerCode());

            $managerBiz = new ManagerBiz(true);

            // 获取详情
            $managerInfo = $managerBiz->getInfo(BackUtils::getTokenManagerCode());
            if (empty($managerInfo->getCode())) {
                return $this->setCode(SubCode::ABNORMAL_ACCESS)->outPut((object)[]);
            }

            // 检查密码
            if (!$managerBiz->checkPassword($params['ori_password'], $managerInfo->getPassword())) {
                return $this->setCode(SubCode::PASSWORD_ERROR)->outPut((object)[]);
            }

            // 更新密码
            $res = $managerBiz->setPassword($paramsObj);
            if (!$res) {
                return $this->setCode(SubCode::SAVE_ERROR)->outPut((object)[]);
            }

            return $this->outPut((object)[]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->getName(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }

    /**
     * 登录详情
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function loginInfo(Request $request): JsonResponse
    {
        try {

            // 获取token
            $token = $request->headers->get('token');
            if (empty($token)) {
                return $this->outPut((object)[]);
            }

            $managerInfo = BackUtils::getTokenManagerInfo();

            return $this->outPut([
                'userId' => $managerInfo->getCode(),
                'userName' => $managerInfo->getName(),
                'roles' => [
                    'R_SUPER'
                ],
                'buttons' => [
                    'B_CODE1',
                    'B_CODE2',
                    'B_CODE3'
                ],
            ]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->uri(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }

    /**
     * 退出
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // 获取token
            $token = $request->headers->get('token');
            if (empty($token)) {
                return $this->outPut((object)[]);
            }

            // 销毁token
            $tokenConf = config('service.admin_params.token_conf');
            $res = (new TokenBiz($tokenConf['project_name']))->removeToken($token, BackUtils::getTokenManagerCode());
            if (!$res) {
                return $this->setCode(SubCode::LOGOUT_ERROR)->outPut((object)[]);
            }

            return $this->outPut((object)[]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->uri(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }

    /**
     * 更新refresh token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $refreshToken = $request->input('refreshToken');
            if (empty($refreshToken)) {
                return $this->setCode(SubCode::PARAMS_ERROR)->outPut((object)[]);
            }

            $tokenConf = config('service.admin_params.token_conf');
            $tokenBiz = new TokenBiz($tokenConf['project_name']);

            // 获取refresh token中数据
            $managerCode = $tokenBiz->getRefreshTokenData($refreshToken);
            if (empty($managerCode)) {
                return $this->setCode(SubCode::ABNORMAL_ACCESS)->outPut((object)[]);
            }

            $managerBiz = new ManagerBiz(true);

            // 获取用户详情
            $managerInfo = $managerBiz->getInfo($managerCode);
            if (empty($managerInfo->getCode())) {
                return $this->setCode(SubCode::ACCOUNT_PASSWORD_ERROR)->outPut((object)[]);
            }
            if ($managerInfo->isDel() || $managerInfo->isHide()) {
                return $this->setCode(SubCode::ACCOUNT_DISABLED_ERROR)->outPut((object)[]);
            }

            // 获取token 并 记录登录数据
            $tokenData = ['manager_info' => $managerInfo];

            // 获取token
            $tokenBiz = $tokenBiz->setQueueLength($tokenConf['max_login_account_num'])
                ->setExpireTime($tokenConf['expire_time'])
                ->setRefreshExpireTime($tokenConf['refresh_expire_time'])
                ->genAccessToken($tokenData, $managerInfo->getCode())
                ->genRefreshToken($managerInfo->getCode())
                ->delRefreshToken($refreshToken);

            if (empty($tokenBiz->getAccessToken())) {
                return $this->setCode(SubCode::TOKEN_ERROR)->outPut((object)[]);
            }

            return $this->outPut([
                'token' => $tokenBiz->getAccessToken(),
                'refreshToken' => $tokenBiz->getRefreshToken(),
                'managerName' => $managerInfo->getName()
            ]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->uri(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }
}
