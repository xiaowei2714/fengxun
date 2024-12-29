<?php

namespace App\Http\Controllers\AppPlatform;

use App\Http\Controllers\Controller;
use App\Modules\Base\BizSrc\VisitSaveParams;
use App\Modules\Base\CompanyBiz;
use App\Modules\Base\TokenBiz;
use App\Modules\Base\UserBiz;
use App\Modules\Base\VisitBiz;
use App\Modules\Wechat\BizSrc\SessionResult;
use App\Modules\Wechat\WechatBiz;
use App\Utils\AppletUtils;
use App\Utils\SubCode;
use App\Utils\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class UserController extends Controller
{
    /**
     * 获取token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function wechatToken(Request $request): JsonResponse
    {
        try {
            $code = trim($request->input('code'));
            $isVisit = trim($request->input('is_visit'));
            if (empty($code) || strlen($code) > 64) {
                return $this->setSubCode(SubCode::PARAMS_ERROR)->outPut((object)[]);
            }
            if (!empty($isVisit) && $isVisit != Utils::USABLE_SHOW) {
                $isVisit = Utils::DISABLE_SHOW;
            }
            if (empty($isVisit)) {
                $isVisit = Utils::DISABLE_SHOW;
            }

            // 获取单位信息
            $companyInfo = (new CompanyBiz())->getCompanyInfo();
            if (empty($companyInfo) || $companyInfo->isDisable()) {
                return $this->setSubCode(SubCode::ABNORMAL_ACCESS)->outPut((object)[]);
            }

            // 微信小程序登录
//            $sessionInfo = (new WechatBiz())->codeToSession($companyInfo->getWechatAppId(), $companyInfo->getWechatAppSecret(), $code);
//            if ($sessionInfo->isFail() || empty($sessionInfo->getOpenId())) {
//                Log::channel('base_log')->error('CodeToSessionError', ['channel' => 'Wechat', 'app_id' => $companyInfo->getWechatAppId(), 'data' => $sessionInfo->getResponse(), 'msg' => $sessionInfo->getErrorMsg()]);
//                return $this->setSubCode(SubCode::SESSION_ERROR)->outPut((object)[]);
//            }
            $sessionInfo = (new SessionResult())
                ->setOpenId('f1bd2835897e0b0b14bf5724311b62dc')
                ->setSessionKey('pd-xb4QvQ4rAiC-VoafpiA');

            // 获取用户信息
            $userInfo = (new UserBiz(true))->checkAndSaveData(Utils::WECHAT_CHANNEL, $sessionInfo->getOpenId());
            if (empty($userInfo->getCode())) {
                return $this->setSubCode(SubCode::SAVE_ERROR)->outPut((object)[]);
            }
            if ($userInfo->isDel() || $userInfo->isHide()) {
                return $this->setSubCode(SubCode::ABNORMAL_ACCESS)->outPut((object)[]);
            }

            // 保存访问
            if ($isVisit == Utils::USABLE_SHOW) {
                $visitSaveParams = (new VisitSaveParams())
                    ->setIp($request->getClientIp())
                    ->setUserCode($userInfo->getCode())
                    ->setChannel(Utils::WECHAT_CHANNEL);

                $res = (new VisitBiz())->insertData($visitSaveParams);
                if (!$res) {
                    Log::channel('exception_log')->error('VisitError', ['file' => $visitSaveParams->changeDbData(), 'msg' => '保存失败']);
                }
            }

            $tokenCacheData = [
                'channel' => Utils::WECHAT_CHANNEL,
                'user_code' => $userInfo->getCode(),
                'open_id' => $userInfo->getOpenId(),
                'session_key' => $sessionInfo->getSessionKey()
            ];

            // 获取token
            $tokenConf = config('service.app_params.token_conf');
            $tokenBiz = (new TokenBiz($tokenConf['project_name']))
                ->setQueueLength($tokenConf['max_login_account_num'])
                ->setExpireTime($tokenConf['expire_time'])
                ->genAccessToken($tokenCacheData, $userInfo->getCode());

            if (empty($tokenBiz->getAccessToken())) {
                return $this->setSubCode(SubCode::TOKEN_ERROR)->outPut((object)[]);
            }

            return $this->outPut([
                'token' => $tokenBiz->getAccessToken(),
                'phone' => $userInfo->getPhone() ?? '',
                'user_name' => $userInfo->getUserName() ?? '',
                'user_file_url' => $userInfo->getUserFileUrl() ?? '',
                'agree_policy' => $userInfo->getAgreePolicyShow(),
                'user_code' => $userInfo->getCode(),
            ]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->getName(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setSubCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }

    /**
     * 授权手机号, 保存用户联系方式
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function userPhone(Request $request): JsonResponse
    {
        try {

            $params = $request->input();
            if (empty($params['encryptedData']) || empty($params['iv'])) {
                return $this->setSubCode(SubCode::PARAMS_ERROR)->outPut((object)[]);
            }

            // 解密
            $decryptData = $this->decrypt(AppletUtils::getTokenUserSessionKey(), $params['encryptedData'], $params['iv']);
            if (!empty($decryptData['msg'])) {
//                return $this->setSubCode(SubCode::DECRYPT_ERROR)->outPut((object)[]);
            }
            if (empty($decryptData['data']) || !isset($decryptData['data']['phoneNumber'])) {
//                return $this->setSubCode(SubCode::DECRYPT_DATA_ERROR)->outPut((object)[]);
            }

            // 设置参数 并 校验参数
            $paramsObj = (new UserSaveParams())
                ->setCode(AppletUtils::getTokenUserCode(false))
                ->setPhone($decryptData['data']['phoneNumber']);

            $bizObj = new UserBiz();

            // 保存
            $res = $bizObj->setData($paramsObj);
            if (!$res) {
                $errorCode = !empty($bizObj->getErrorCode()) ? $bizObj->getErrorCode() : SubCode::SAVE_ERROR;
                $errorMsg = !empty($bizObj->getErrorMsg()) ? $bizObj->getErrorMsg() : '';
                return $this->setSubCode($errorCode)->setMsg($errorMsg)->outPut((object)[]);
            }

            // 入同步用户券队列
            $syncParams = (new SyncUserVoucherParams())
                ->setMerchantCode(AppletUtils::getTokenMerchantCode())
                ->setPhone($decryptData['data']['phoneNumber'])
                ->autoSetFlag();

            SyncUserVoucherJob::dispatch($syncParams)->onQueue(SyncUserVoucherJob::QUEUE_NAME);
            Log::channel('base_log')->info('SyncUserVoucherJob', ['data' => $syncParams->getParams(), 'msg' => '手机号授权，入同步用户券队列']);

            return $this->outPut([
                'phone' => $decryptData['data']['phoneNumber']
            ]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->getName(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setSubCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }

    /**
     * 用户同意规则
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function userAgreePolicy(Request $request): JsonResponse
    {
        try {

            $tokenUserCode = AppletUtils::getTokenUserCode(false);

            // 设置参数 并 校验参数
            $paramsObj = (new UserSaveParams())
                ->setCode($tokenUserCode)
                ->setAgreePolicy(Utils::USABLE);

            $bizObj = new UserBiz();

            // 获取用户信息
            $userInfo = (new UserBiz(true))->getInfo($tokenUserCode);
            if (empty($userInfo->getCode())) {
                return $this->setSubCode(SubCode::SAVE_ERROR)->outPut((object)[]);
            }
            if ($userInfo->isDel() || $userInfo->isHide()) {
                return $this->setSubCode(SubCode::ABNORMAL_ACCESS)->outPut((object)[]);
            }
            if ($userInfo->getAgreePolicy() == Utils::USABLE) {
                return $this->outPut((object)[]);
            }

            // 保存
            $res = $bizObj->updateData($paramsObj);
            if (!$res) {
                $errorCode = !empty($bizObj->getErrorCode()) ? $bizObj->getErrorCode() : SubCode::SAVE_ERROR;
                $errorMsg = !empty($bizObj->getErrorMsg()) ? $bizObj->getErrorMsg() : '';
                return $this->setSubCode($errorCode)->setMsg($errorMsg)->outPut((object)[]);
            }

            return $this->outPut((object)[]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->getName(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setSubCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }

    /**
     * @param $sessionKey
     * @param $encryptedData
     * @param $iv
     * @return array
     */
    private function decrypt($sessionKey, $encryptedData, $iv): array
    {
        $decryptData = [
            'msg' => '',
            'data' => [],
        ];

        try {
            $tmpData = Utils::decrypt($sessionKey, $encryptedData, $iv);
            $decryptData['data'] = !empty($tmpData) ? json_decode($tmpData, true) : [];
        } catch (Exception $e) {
            $decryptData['msg'] = $e->getMessage();
        }

        return $decryptData;
    }
}
