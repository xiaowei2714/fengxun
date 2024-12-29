<?php

namespace App\Http\Controllers;

use App\Utils\SubCode;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $ret = 0;
    private $sub = 0;
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
    protected function setRetCode($ret)
    {
        $this->ret = (int)$ret;

        return $this;
    }

    /**
     * 设置 业务返回码
     *
     * @param $sub
     * @return $this
     */
    protected function setSubCode($sub)
    {
        $this->sub = (int)$sub;
        $this->msg = (string)(SubCode::ERROR_MSG[$sub] ?? '');
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
            'ret' => $this->ret,
            'sub' => $this->sub,
            'msg' => !empty($this->msg) ? $this->msg : ($this->sub == SubCode::SUB_CODE_SUCCESS ? SubCode::ERROR_MSG[SubCode::SUB_CODE_SUCCESS] : ''),
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
    public function setPageNum(int $pageNum): Controller
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
    public function setLimitNum(int $limitNum): Controller
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
    public function setCountNum(int $countNum): Controller
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
    public function setContentData($contentData): Controller
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
    public function setInfoData(array $infoData): Controller
    {
        $this->infoData[] = $infoData;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setAllData(array $data): Controller
    {
        $this->infoData = $data;
        return $this;
    }

    /**
     * @return array
     */
    protected function summeryResponse(): array
    {
        return $responseData = [
            'total_num' => 0,
            'incomplete_num' => 0,
            'week_total_num' => 0
        ];
    }

    /**
     * @return array
     */
    protected function listResponse(): array
    {
        return [
            'total' => [
                'total_num' => $this->getCountNum(),
                'total_page' => $this->getLimitNum() > 0 ? ceil($this->getCountNum() / $this->getLimitNum()) : 0,
                'page_num' => $this->getPageNum(),
                'limit_num' => $this->getLimitNum()
            ],
            'data' => $this->getInfoData() ?: $this->getContentData(),
        ];
    }

    /**
     * @return JsonResponse
     */
    protected function outPutList(): JsonResponse
    {
        $newData = [
            'ret' => $this->ret,
            'sub' => $this->sub,
            'msg' => !empty($this->msg) ? $this->msg : ($this->sub == SubCode::SUB_CODE_SUCCESS ? SubCode::ERROR_MSG[SubCode::SUB_CODE_SUCCESS] : ''),
            'data' => $this->listResponse()
        ];

        return response()->json($newData);
    }
}
