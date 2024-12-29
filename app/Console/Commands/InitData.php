<?php

namespace App\Console\Commands;

use App\Modules\Base\BizSrc\CompanyKeys;
use App\Modules\Base\BizSrc\CompanySaveParams;
use App\Modules\Base\CompanyBiz;
use App\Utils\Utils;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;

class InitData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '平台初始化数据';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 初始化单位数据
        $this->initCompanyData();

        return true;
    }

    /**
     * 初始化单位数据
     *
     * @return bool
     */
    public function initCompanyData(): bool
    {
        try {

            // 所有键
            $allKeys = CompanyKeys::getAllKeys();
            $newData = [];
            foreach ($allKeys as $key => $value) {
                $newData[$key] = '';
            }

            if (isset($newData['usable'])) {
                $newData['usable'] = Utils::USABLE_SHOW;
            }

            // 设置参数 并 校验参数
            $paramsObj = (new CompanySaveParams())->setEditParams($newData);
            if (!empty($paramsObj->getErrorCode()) || !empty($paramsObj->getErrorMsg())) {
                $errorMsg = !empty($paramsObj->getErrorMsg()) ? $paramsObj->getErrorMsg() : '';
                throw new Exception('保存参数异常：' . $errorMsg);
            }

            $companyBiz = new CompanyBiz();

            // 保存
            $res = $companyBiz->saveData($paramsObj);
            if (!$res) {
                throw new Exception('保存参数异常：' . $companyBiz->getErrorMsg());
            }

            echo '单位数据初始化成功' . PHP_EOL;
            return true;

        } catch (Exception $e) {
            Log::channel('base_error')->error('InitError', ['type' => '单位数据', 'file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]);
            echo '单位数据初始化失败：' . $e->getMessage() . PHP_EOL;
            return false;
        }
    }
}
