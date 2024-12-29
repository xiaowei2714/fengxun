<?php

namespace App\Http\Controllers\BackPlatform;

use App\Modules\Base\BizSrc\UserSearchParams;
use App\Modules\Base\BizSrc\VisitSearchParams;
use App\Modules\Base\UserBiz;
use App\Modules\Base\VisitBiz;
use App\Modules\Information\BizSrc\InformationSearchParams;
use App\Modules\Information\InformationBiz;
use App\Utils\SubCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class IndexController extends BackController
{
    /**
     * 汇总数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function summaryData(Request $request): JsonResponse
    {
        try {

            // 获取总访问量
            $visitSearchParamsObj = (new VisitSearchParams());
            $visitCount = (new VisitBiz())->getCount($visitSearchParamsObj);

            // 用户注册数
            $userSearchParamsObj = (new UserSearchParams());
            $userCount = (new UserBiz())->getCount($userSearchParamsObj);

            // 信息
            $informationBiz = new InformationBiz();
            $informationSearchParamsObj = (new InformationSearchParams());

            // 信息汇总数据
            $informationData = $informationBiz->getSummaryData($informationSearchParamsObj);

            // 信息分组汇总数据
            $curTime = time();
            $informationSearchParamsObj
                ->setStartTime(date('Y-m-d H:i:s', $curTime - 7 * 60 * 60))
                ->setEndTime(date('Y-m-d H:i:s', $curTime));
            $groupInformationData = $informationBiz->getGroupSummaryData($informationSearchParamsObj);

            // 信息分类汇总数据
            $categoryInformationData = $informationBiz->getCategorySummaryData($informationSearchParamsObj);

            return $this->outPut([
                'visit_count' => $visitCount,
                'user_count' => $userCount,
                'information_count' => (int)$informationData['total'],
                'information_see_count' => (int)$informationData['see_num'],
                'information_phone_count' => (int)$informationData['phone_num'],
                'information_group' => $this->formatChartOutData($informationSearchParamsObj, $groupInformationData),
                'information_category' => $this->formatCategoryOutData($categoryInformationData)
            ]);

        } catch (Exception $e) {
            Log::channel('exception_log')->error($request->route()->getName(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            return $this->setCode(SubCode::SYSTEM_EXCEPTION_ERROR)->outPut((object)[]);
        }
    }

    /**
     * 格式化图表输出数据
     *
     * @param InformationSearchParams $paramsObj
     * @param $groupData
     * @return array|array[]
     */
    private function formatChartOutData(InformationSearchParams $paramsObj, $groupData): array
    {
        $newData = [
            'x' => [],
            'y_count' => [],
            'y_see_count' => [],
            'y_phone_count' => [],
        ];

        $startTime = strtotime($paramsObj->getStartTime());
        $endTime = strtotime($paramsObj->getEndTime());
        $tmpStartTime = date('Y-m-d H:00:00', $startTime);
        $tmpEndTime = date('Y-m-d H:00:00', $endTime);
        while ($tmpStartTime <= $tmpEndTime) {
            $tmpTime = date('Y-m-d H', strtotime($tmpStartTime));
            $newData['x'][] = date('H:00', strtotime($tmpStartTime));
            $newData['y_count'][] = isset($groupData[$tmpTime]) ? (int)$groupData[$tmpTime]->total : 0;
            $newData['y_see_count'][] = isset($groupData[$tmpTime]) ? (int)$groupData[$tmpTime]->num : 0;
            $newData['y_phone_count'][] = isset($groupData[$tmpTime]) ? (int)$groupData[$tmpTime]->phone_num : 0;

            $tmpStartTime = date('Y-m-d H:00:00', strtotime($tmpStartTime) + 3600);
        }

        return $newData;
    }

    /**
     * 信息分类汇总数据
     *
     * @param $groupData
     * @return array
     */
    private function formatCategoryOutData($groupData): array
    {
        $newData = [];
        foreach ($groupData as $item) {
            $newData[] = [
                'category_code' => $item->category_code,
                'category_name' => $item->category_name,
                'count' => $item->total,
                'see_count' => $item->num,
                'phone_count' => $item->phone_num
            ];
        }

        return $newData;
    }
}
