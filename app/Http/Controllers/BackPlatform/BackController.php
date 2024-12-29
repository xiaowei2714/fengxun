<?php

namespace App\Http\Controllers\BackPlatform;

use App\Utils\SubCode;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BackController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $code = '0000';
    private $msg = '';

    private $pageNum = 1;
    private $limitNum = 10;
    private $countNum = 0;
    private $infoData = [];
    private $contentData = [];

    /**
     * 设置 系统返回码
     *
     * @param $ret
     * @return $this
     */
    protected function setCode($code)
    {
        $this->code = (int)$code;
        $this->msg = (string)(SubCode::ERROR_MSG[$code] ?? '');
        return $this;
    }

    /**
     * 设置 错误信息
     *
     * @param $msg
     * @return $this
     */
    protected function setMsg($msg)
    {
        if (empty($msg)) {
            return $this;
        }

        $this->msg = (string)$msg;
        return $this;
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    protected function outPut($data): JsonResponse
    {
        $newData = [
            'code' => $this->code,
            'msg' => !empty($this->msg) ? $this->msg : ($this->code == SubCode::SUB_CODE_SUCCESS ? SubCode::ERROR_MSG[SubCode::SUB_CODE_SUCCESS] : ''),
            'data' => $data
        ];

        return response()->json($newData);
    }

    /**
     * 获取登录token
     *
     * @param Request $request
     * @return string|null
     */
    protected function getToken(Request $request)
    {
        return $request->headers->get('token');
    }

    /**
     * @return int
     */
    public function getPageNum(): int
    {
        return $this->pageNum;
    }

    /**
     * @param int $pageNum
     * @return $this
     */
    public function setPageNum(int $pageNum): BackController
    {
        $this->pageNum = $pageNum;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimitNum(): int
    {
        return $this->limitNum;
    }

    /**
     * @param int $limitNum
     * @return $this
     */
    public function setLimitNum(int $limitNum): BackController
    {
        $this->limitNum = $limitNum;
        return $this;
    }

    /**
     * @return int
     */
    public function getCountNum(): int
    {
        return $this->countNum;
    }

    /**
     * @param int $countNum
     * @return $this
     */
    public function setCountNum(int $countNum): BackController
    {
        $this->countNum = $countNum;
        return $this;
    }

    /**
     * @return array
     */
    public function getContentData(): array
    {
        return $this->contentData;
    }

    /**
     * @param array $contentData
     * @return $this
     */
    public function setContentData($contentData): BackController
    {
        $this->contentData = $contentData;
        return $this;
    }

    /**
     * @return array
     */
    public function getInfoData(): array
    {
        return $this->infoData;
    }

    /**
     * @param array $infoData
     * @return $this
     */
    public function setInfoData(array $infoData): BackController
    {
        $this->infoData[] = $infoData;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setAllData(array $data): BackController
    {
        $this->infoData = $data;
        return $this;
    }

    /**
     * @return array
     */
    protected function listResponse(): array
    {
        return [
            'current' => $this->getPageNum(),
            'size' => $this->getLimitNum() > 0 ? ceil($this->getCountNum() / $this->getLimitNum()) : 0,
            'total' => $this->getCountNum(),
            'records' => $this->getInfoData() ?: $this->getContentData(),
        ];
    }

    /**
     * @return JsonResponse
     */
    protected function outPutList(): JsonResponse
    {
        $newData = [
            'code' => $this->code,
            'msg' => !empty($this->msg) ? $this->msg : ($this->code == SubCode::SUB_CODE_SUCCESS ? SubCode::ERROR_MSG[SubCode::SUB_CODE_SUCCESS] : ''),
            'data' => $this->listResponse()
        ];

        return response()->json($newData);
    }
}
