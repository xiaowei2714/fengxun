<?php

namespace App\Http\Controllers\BackPlatform;

use App\Modules\Base\BizSrc\UserInfo;
use App\Modules\Base\BizSrc\UserSearchParams;
use App\Modules\Base\UserBiz;
use App\Utils\SubCode;
use App\Utils\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class UserController extends BackController
{
    /**
     * 启用
     *
     * @param Request $request
     * @param $code
     * @return JsonResponse
     */
    public function show(Request $request, $code): JsonResponse
    {
        try {
            if (empty($code)) {
                return $this->setCode(SubCode::PARAMS_ERROR)->outPut((object)[]);
            }

            $bizObj = new UserBiz();

            // 置为启用
            $res = $bizObj->setShow($code);
            if (!$res) {
                $errorCode = !empty($bizObj->getErrorCode()) ? $bizObj->getErrorCode() : SubCode::SAVE_ERROR;
                $errorMsg = !empty($bizObj->getErrorMsg()) ? $bizObj->getErrorMsg() : '';
                return $this->setCode($errorCode)->setMsg($errorMsg)->outPut((object)[]);
            }

            return $this->outPut((object)[]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->getName(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }

    /**
     * 禁用
     *
     * @param Request $request
     * @param $code
     * @return JsonResponse
     */
    public function hide(Request $request, $code): JsonResponse
    {
        try {
            if (empty($code)) {
                return $this->setCode(SubCode::PARAMS_ERROR)->outPut((object)[]);
            }

            $bizObj = new UserBiz();

            // 置为禁用
            $res = $bizObj->setHide($code);
            if (!$res) {
                $errorCode = !empty($bizObj->getErrorCode()) ? $bizObj->getErrorCode() : SubCode::SAVE_ERROR;
                $errorMsg = !empty($bizObj->getErrorMsg()) ? $bizObj->getErrorMsg() : '';
                return $this->setCode($errorCode)->setMsg($errorMsg)->outPut((object)[]);
            }

            return $this->outPut((object)[]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->getName(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }

    /**
     * 删除
     *
     * @param Request $request
     * @param $code
     * @return JsonResponse
     */
    public function del(Request $request, $code): JsonResponse
    {
        try {
            if (empty($code)) {
                return $this->setCode(SubCode::PARAMS_ERROR)->outPut((object)[]);
            }

            $bizObj = new UserBiz();

            // 置为删除
            $res = $bizObj->setDel($code);
            if (!$res) {
                $errorCode = !empty($bizObj->getErrorCode()) ? $bizObj->getErrorCode() : SubCode::SAVE_ERROR;
                $errorMsg = !empty($bizObj->getErrorMsg()) ? $bizObj->getErrorMsg() : '';
                return $this->setCode($errorCode)->setMsg($errorMsg)->outPut((object)[]);
            }

            return $this->outPut((object)[]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->getName(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }

    /**
     * 详情
     *
     * @param Request $request
     * @param $code
     * @return JsonResponse
     */
    public function info(Request $request, $code): JsonResponse
    {
        try {
            if (empty($code)) {
                return $this->setCode(SubCode::PARAMS_ERROR)->outPut((object)[]);
            }

            // 获取详情
            $infoObj = (new UserBiz(true))->getInfo($code);
            if (empty($infoObj->getCode()) || $infoObj->isDel()) {
                return $this->setCode(SubCode::ABNORMAL_ACCESS)->outPut((object)[]);
            }

            return $this->outPut([
                'code' => $infoObj->getCode(),
                'name' => $infoObj->getName() ?? '',
                'file_url' => Utils::getWholeUploadUrl($infoObj->getFilePath()),
                'open_id' => $infoObj->getOpenId(),
                'phone' => $infoObj->getPhone(),
                'show' => Utils::statusShow($infoObj->getShow()),
                'create_time' => $infoObj->getCreateTime(),
                'update_time' => $infoObj->getUpdateTime(),
            ]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->getName(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }

    /**
     * 列表
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        try {
            // 参数类
            $paramsObj = (new UserSearchParams())->setParams($request->input());

            // 设置出参页数
            $this->setPageNum($paramsObj->getPageNum())
                ->setLimitNum($paramsObj->getLimitNum());

            $bizObj = new UserBiz(true);

            // 获取总数
            $totalNum = $bizObj->getCount($paramsObj);
            if ($totalNum <= 0) {
                return $this->outPutList();
            }
            $this->setCountNum($totalNum);

            // 获取当前页列表数据
            $data = $bizObj->getPageData($paramsObj);
            if (empty($data)) {
                return $this->outPutList();
            }

            // 输出数据 data
            foreach ($data as $infoObj) {
                if (!($infoObj instanceof UserInfo)) {
                    continue;
                }

                $this->setInfoData([
                    'code' => $infoObj->getCode(),
                    'name' => $infoObj->getName() ?? '',
                    'file_url' => Utils::getWholeUploadUrl($infoObj->getFilePath()),
                    'open_id' => $infoObj->getOpenId(),
                    'phone' => $infoObj->getPhone(),
                    'show' => Utils::statusShow($infoObj->getShow()),
                    'create_time' => $infoObj->getCreateTime(),
                    'update_time' => $infoObj->getUpdateTime(),
                ]);
            }

            return $this->outPutList();

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->getName(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }
}
