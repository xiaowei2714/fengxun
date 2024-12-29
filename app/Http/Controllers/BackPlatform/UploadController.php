<?php

namespace App\Http\Controllers\BackPlatform;

use App\Http\Controllers\Controller;
use App\Modules\Base\UploadBiz;
use App\Utils\SubCode;
use App\Utils\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class UploadController extends Controller
{
    /**
     * 图片上传
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadImage(Request $request): JsonResponse
    {
        try {
            // 获取文件obj
            $fileObj = $request->file('file');
            if (empty($fileObj)) {
                return $this->setSubCode(SubCode::PARAMS_ERROR)->outPut((object)[]);
            }

            $uploadBiz = new UploadBiz();

            // 上传数据
            $fileObj = $uploadBiz->uploadFile($fileObj, UploadBiz::IMAGE);
            if ($fileObj === null) {
                return $this->setSubCode(SubCode::UPLOAD_PATH_ERROR)->outPut((object)[]);
            }

            return $this->outPut([
                'code' => $fileObj->getCode(),
                'url' => $fileObj->getPath(),
                'url_show' => Utils::getWholeUploadUrl($fileObj->getPath())
            ]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->uri(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setSubCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }
}
