<?php

namespace App\Http\Controllers\BackPlatform;

use App\Http\Controllers\Controller;
use App\Modules\Base\BizSrc\VisitInfo;
use App\Modules\Base\BizSrc\VisitSearchParams;
use App\Modules\Base\BizSrc\VisitSaveParams;
use App\Modules\Base\VisitBiz;
use App\Utils\SubCode;
use App\Utils\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class VisitController extends Controller
{
    /**
     * 新建保存
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        try {
            // 设置参数 并 校验参数
            $paramsObj = (new VisitSaveParams())->setCreateParams($request->input());
            if (!empty($paramsObj->getErrorCode()) || !empty($paramsObj->getErrorMsg())) {
                $errorCode = !empty($paramsObj->getErrorCode()) ? $paramsObj->getErrorCode() : SubCode::PARAMS_ERROR;
                $errorMsg = !empty($paramsObj->getErrorMsg()) ? $paramsObj->getErrorMsg() : '';
                return $this->setSubCode($errorCode)->setMsg($errorMsg)->outPut((object)[]);
            }

            $bizObj = new VisitBiz();

            // 保存
            $res = $bizObj->insertData($paramsObj);
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
     * 编辑保存
     *
     * @param Request $request
     * @param $code
     * @return JsonResponse
     */
    public function edit(Request $request, $code): JsonResponse
    {
        try {
            // 设置参数 并 校验参数
            $paramsObj = (new VisitSaveParams())->setEditParams($request->input(), $code);
            if (!empty($paramsObj->getErrorCode()) || !empty($paramsObj->getErrorMsg())) {
                $errorCode = !empty($paramsObj->getErrorCode()) ? $paramsObj->getErrorCode() : SubCode::PARAMS_ERROR;
                $errorMsg = !empty($paramsObj->getErrorMsg()) ? $paramsObj->getErrorMsg() : '';
                return $this->setSubCode($errorCode)->setMsg($errorMsg)->outPut((object)[]);
            }

            $bizObj = new VisitBiz();

            // 保存
            $res = $bizObj->setData($paramsObj);
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
                return $this->setSubCode(SubCode::PARAMS_ERROR)->outPut((object)[]);
            }

            $bizObj = new VisitBiz();

            // 置为启用
            $res = $bizObj->setShow($code);
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
                return $this->setSubCode(SubCode::PARAMS_ERROR)->outPut((object)[]);
            }

            $bizObj = new VisitBiz();

            // 置为禁用
            $res = $bizObj->setHide($code);
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
                return $this->setSubCode(SubCode::PARAMS_ERROR)->outPut((object)[]);
            }

            $bizObj = new VisitBiz();

            // 置为删除
            $res = $bizObj->setDel($code);
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
                return $this->setSubCode(SubCode::PARAMS_ERROR)->outPut((object)[]);
            }

            // 获取详情
            $infoObj = (new VisitBiz(true))->getInfo($code);
            if (empty($infoObj->getCode()) || $infoObj->isDel()) {
                return $this->setSubCode(SubCode::ABNORMAL_ACCESS)->outPut((object)[]);
            }

            return $this->outPut([
                'code' => $infoObj->getCode(),
                'show' => Utils::statusShow($infoObj->getShow()),
                'create_time' => $infoObj->getCreateTime(),
                'update_time' => $infoObj->getUpdateTime(),
            ]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->getName(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setSubCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
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
            $paramsObj = (new VisitSearchParams())->setParams($request->input());

            // 设置出参页数
            $this->setPageNum($paramsObj->getPageNum())
                ->setLimitNum($paramsObj->getLimitNum());

            $bizObj = new VisitBiz(true);

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
                if (!($infoObj instanceof VisitInfo)) {
                    continue;
                }

                $this->setInfoData([
                    'code' => $infoObj->getCode(),
                    'show' => Utils::statusShow($infoObj->getShow()),
                    'create_time' => $infoObj->getCreateTime(),
                    'update_time' => $infoObj->getUpdateTime(),
                ]);
            }

            return $this->outPutList();

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->getName(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setSubCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }
}
