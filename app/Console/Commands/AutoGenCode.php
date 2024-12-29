<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\DB;
use Exception;

class AutoGenCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:code {table} {dir} {prefix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动生成代码，参数：表名称 目录名称 文件前缀';

    protected $files;
    protected $composer;
    private $table;
    private $dir;
    private $prefix;
    private $tableFields;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $filesystem
     * @param Composer $composer
     */
    public function __construct(Filesystem $filesystem, Composer $composer)
    {
        parent::__construct();

        $this->files = $filesystem;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws Exception
     */
    public function handle(): bool
    {
        try {
            $this->table = $this->argument('table');
            $this->dir = $this->argument('dir');
            $this->prefix = $this->argument('prefix');

            // 自动生成代码文件
            $this->handleGen();

            // 重新生成autoload.php文件
            $this->composer->dumpAutoloads();

            return true;
        } catch (Exception $e) {
            $this->info('ErrorFile:' . $e->getFile() . PHP_EOL . 'ErrorLine:' . $e->getLine() . PHP_EOL . 'ErrorMessage:' . $e->getMessage());
            return false;
        }
    }

    /**
     * @return bool
     * @throws FileNotFoundException
     */
    private function handleGen()
    {
        $this->tableFields();

        // 获取模板数据
        $stubs = $this->getStubs();

        // 进行模板渲染
        foreach ($stubs as $value) {
            if (!$this->createDirectory($value['dir'])) {
                $this->info($value['path'] . ' ERROR');
                continue;
            }

            $tmpPath = $value['dir'] . DIRECTORY_SEPARATOR . $value['path'];
            $tmpContent = $this->getRenderStub($value['vars'], $value['file']);
            $res = $this->files->put($tmpPath, $tmpContent);
            if ($res) {
                $this->info($value['path'] . ' SUCCESS');
            } else {
                $this->info($value['path'] . ' ERROR');
            }
        }

        return true;
    }

    /**
     * 获取模板数据
     *
     * @return array[]
     * @throws FileNotFoundException
     */
    private function getStubs(): array
    {
        return [
            [
                'file' => $this->files->get(resource_path('stubs') . DIRECTORY_SEPARATOR . 'autoCode' . DIRECTORY_SEPARATOR . 'InfoObj.stub'),
                'dir' => 'App' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . $this->dir . DIRECTORY_SEPARATOR . 'BizSrc',
                'path' => $this->prefix . 'Info.php',
                'vars' => [
                    'pNamespace' => 'App\\Modules\\' . $this->dir . '\\BizSrc',
                    'pClassname' => $this->prefix . 'Info',
                    'pContent' => $this->genInfoClassContent()
                ]
            ],
            [
                'file' => $this->files->get(resource_path('stubs') . DIRECTORY_SEPARATOR . 'autoCode' . DIRECTORY_SEPARATOR . 'SaveObj.stub'),
                'dir' => 'App' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . $this->dir . DIRECTORY_SEPARATOR . 'BizSrc',
                'path' => $this->prefix . 'SaveParams.php',
                'vars' => [
                    'pNamespace' => 'App\\Modules\\' . $this->dir . '\\BizSrc',
                    'pClassname' => $this->prefix . 'SaveParams',
                    'pContent' => $this->genSaveClassContent(($this->prefix . 'SaveParams'))
                ]
            ],
            [
                'file' => $this->files->get(resource_path('stubs') . DIRECTORY_SEPARATOR . 'autoCode' . DIRECTORY_SEPARATOR . 'SearchObj.stub'),
                'dir' => 'App' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . $this->dir . DIRECTORY_SEPARATOR . 'BizSrc',
                'path' => $this->prefix . 'SearchParams.php',
                'vars' => [
                    'pNamespace' => 'App\\Modules\\' . $this->dir . '\\BizSrc',
                    'pClassname' => $this->prefix . 'SearchParams'
                ]
            ],
            [
                'file' => $this->files->get(resource_path('stubs') . DIRECTORY_SEPARATOR . 'autoCode' . DIRECTORY_SEPARATOR . 'ControllerObj.stub'),
                'dir' => 'App' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'BackPlatform',
                'path' => $this->prefix . 'Controller.php',
                'vars' => [
                    'pNamespace' => 'App\\Http\\Controllers\\BackPlatform',
                    'pClassname' => $this->prefix . 'Controller',
                    'pDir' => $this->dir,
                    'pInfo' => $this->prefix . 'Info',
                    'pSearch' => $this->prefix . 'SearchParams',
                    'pSave' => $this->prefix . 'SaveParams',
                    'pBiz' => $this->prefix . 'Biz',
                ]
            ],
            [
                'file' => $this->files->get(resource_path('stubs') . DIRECTORY_SEPARATOR . 'autoCode' . DIRECTORY_SEPARATOR . 'BizObj.stub'),
                'dir' => 'App' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . $this->dir,
                'path' => $this->prefix . 'Biz.php',
                'vars' => [
                    'pNamespace' => 'App\\Modules\\' . $this->dir,
                    'pClassname' => $this->prefix . 'Biz',
                    'pDir' => $this->dir,
                    'pModel' => $this->prefix . 'Model',
                    'pInfo' => $this->prefix . 'Info',
                    'pSaveParams' => $this->prefix . 'SaveParams',
                    'pSearchParams' => $this->prefix . 'SearchParams',
                ]
            ],
            [
                'file' => $this->files->get(resource_path('stubs') . DIRECTORY_SEPARATOR . 'autoCode' . DIRECTORY_SEPARATOR . 'ModelObj.stub'),
                'dir' => 'App' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . $this->dir . DIRECTORY_SEPARATOR . 'Models',
                'path' => $this->prefix . 'Model.php',
                'vars' => [
                    'pNamespace' => 'App\\Modules\\' . $this->dir . '\\Models',
                    'pClassname' => $this->prefix . 'Model',
                    'pDir' => $this->dir,
                    'pTable' => $this->table,
                    'pSave' => $this->prefix . 'SaveParams',
                    'pSearch' => $this->prefix . 'SearchParams',
                ]
            ],
        ];
    }

    /**
     * 替换模板变量
     *
     * @param $templateData
     * @param $stub
     * @return array|mixed|string|string[]
     */
    private function getRenderStub($templateData, $stub)
    {
        foreach ($templateData as $search => $replace) {
            $stub = str_replace('$' . $search, $replace, $stub);
        }

        return $stub;
    }

    /**
     * 创建目录
     *
     * @param $directory
     * @return bool
     */
    private function createDirectory($directory): bool
    {
        //检查路径是否存在,不存在创建一个,并赋予775权限
        if (!$this->files->isDirectory($directory)) {
            return $this->files->makeDirectory($directory, 0755, true);
        }

        return true;
    }

    /**
     * 数据表字段
     *
     * @return array
     */
    private function tableFields()
    {
        $tableName = env('DB_PREFIX') . $this->table;
        $tableFields = DB::select('SHOW FULL COLUMNS FROM ' . $tableName);

        $outArr = [
            'id', 'code', 'show', 'usable', 'create_time', 'update_time'
        ];

        $newData = [];
        foreach ($tableFields as $item) {
            if (in_array($item->Field, $outArr)) {
                continue;
            }

            $tmpArr = explode('_', $item->Field);

            $tmpVs = '';
            foreach ($tmpArr as $vKey => $vValue) {
                if ($vKey == 0) {
                    $tmpVs .= $vValue;
                } else {
                    $tmpVs .= ucfirst($vValue);
                }
            }

            $newData[] = [
                'field' => $tmpVs,
                'ori_field' => $item->Field,
                'type' => $this->getType($item->Type),
                'comment' => $item->Comment,
            ];
        }

        return $this->tableFields = $newData;
    }

    private function getType($type)
    {
        if (strpos($type, 'varchar') !== false) {
            preg_match('/\d+/', $type, $match);
            return 'string' . (!empty($match[0]) ? ('|max:' . $match[0]) : '');
        }
        if (strpos($type, 'int') !== false) {
            preg_match('/\d+/', $type, $match);
            return 'numeric' . (!empty($match[0]) ? ('|max:' . ($match[0] - 1)) : '');
        }
        if (strpos($type, 'decimal') !== false) {
            preg_match('/\d+/', $type, $match);
            return 'numeric' . (!empty($match[0]) ? ('|max:' . ($match[0] - 1)) : '');
        }
        if (strpos($type, 'enum') !== false) {
            preg_match('/\((.*)\)/', $type, $match);
            return 'in' . (!empty($match[0]) ? (':' . (str_replace("'", '', $match[1]))) : '');
        }
        if (strpos($type, 'text') !== false) {
            return 'string';
        }
        if (strpos($type, 'datetime') !== false) {
            return 'date';
        }

        return '';
    }

    /**
     * 获取Info类的内容
     *
     * @return string
     */
    private function genInfoClassContent()
    {
        $contentStr = '';
        foreach ($this->tableFields as $value) {
            $contentStr .= '    protected $' . $value['field'] . ';' . PHP_EOL;
        }

        $i = 0;
        foreach ($this->tableFields as $value) {
            if ($i != 0) {
                $contentStr .= PHP_EOL;
            }

            $contentStr .= PHP_EOL;
            $contentStr .= '    /**' . PHP_EOL;
            $contentStr .= '     * @return mixed' . PHP_EOL;
            $contentStr .= '     */' . PHP_EOL;
            $contentStr .= '    public function get' . ucfirst($value['field']) . '()' . PHP_EOL;
            $contentStr .= '    {' . PHP_EOL;
            $contentStr .= '        return $this->' . $value['field'] . ';' . PHP_EOL;
            $contentStr .= '    }';

            $i++;
        }

        return $contentStr;
    }

    /**
     * 获取Save类的内容
     *
     * @return string
     */
    private function genSaveClassContent($className)
    {
        $contentStr = '';
        foreach ($this->tableFields as $value) {
            $contentStr .= '    protected $' . $value['field'] . ';' . PHP_EOL;
        }

        foreach ($this->tableFields as $value) {
            $contentStr .= PHP_EOL;
            $contentStr .= '    /**' . PHP_EOL;
            $contentStr .= '     * @param $' . $value['field'] . PHP_EOL;
            $contentStr .= '     * @return $this' . PHP_EOL;
            $contentStr .= '     */' . PHP_EOL;
            $contentStr .= '    public function set' . ucfirst($value['field']) . '($' . $value['field'] . '): '  . $className. PHP_EOL;
            $contentStr .= '    {' . PHP_EOL;
            $contentStr .= '        $this->' . $value['field'] . ' = $' . $value['field'] . ';' . PHP_EOL;
            $contentStr .= '        return $this;' . PHP_EOL;
            $contentStr .= '    }' . PHP_EOL;
        }

        // func insert setParams
        $contentStr .= PHP_EOL;
        $contentStr .= '    /**' . PHP_EOL;
        $contentStr .= '     * @param $params' . PHP_EOL;
        $contentStr .= '     * @return $this' . PHP_EOL;
        $contentStr .= '     */' . PHP_EOL;
        $contentStr .= '    public function setCreateParams($params): ' . $className . PHP_EOL;
        $contentStr .= '    {' . PHP_EOL;
        $contentStr .= '        if (!empty($params[\'code\'])) {' . PHP_EOL;
        $contentStr .= '            return $this->setErrorMsg(\'新增不应传入编码值\');' . PHP_EOL;
        $contentStr .= '        }' . PHP_EOL;
        $contentStr .= PHP_EOL;
        $contentStr .= '        unset($params[\'code\']);' . PHP_EOL;
        $contentStr .= '        return $this->setParams($params);' . PHP_EOL;
        $contentStr .= '    }' . PHP_EOL;

        // func update setParams
        $contentStr .= PHP_EOL;
        $contentStr .= '    /**' . PHP_EOL;
        $contentStr .= '     * @param $params' . PHP_EOL;
        $contentStr .= '     * @param $code' . PHP_EOL;
        $contentStr .= '     * @return $this' . PHP_EOL;
        $contentStr .= '     */' . PHP_EOL;
        $contentStr .= '    public function setEditParams($params, $code): ' . $className . PHP_EOL;
        $contentStr .= '    {' . PHP_EOL;
        $contentStr .= '        if (empty($code)) {' . PHP_EOL;
        $contentStr .= '            return $this->setErrorMsg(\'编辑时应传入编码值\');' . PHP_EOL;
        $contentStr .= '        }' . PHP_EOL;
        $contentStr .= PHP_EOL;
        $contentStr .= '        $params[\'code\'] = $code;' . PHP_EOL;
        $contentStr .= '        return $this->setParams($params);' . PHP_EOL;
        $contentStr .= '    }' . PHP_EOL;

        // func setParams
        $contentStr .= PHP_EOL;
        $contentStr .= '    /**' . PHP_EOL;
        $contentStr .= '     * @param $params' . PHP_EOL;
        $contentStr .= '     * @return $this' . PHP_EOL;
        $contentStr .= '     */' . PHP_EOL;
        $contentStr .= '    public function setParams($params): ' . $className . PHP_EOL;
        $contentStr .= '    {' . PHP_EOL;
        $contentStr .= '        parent::setParams($params);' . PHP_EOL;
        $contentStr .= '        if (!empty($this->getErrorCode()) || !empty($this->getErrorMsg())) {' . PHP_EOL;
        $contentStr .= '            return $this;' . PHP_EOL;
        $contentStr .= '        }' . PHP_EOL;
        $contentStr .= PHP_EOL;
        foreach ($this->tableFields() as $value) {
            $contentStr .= '        if (isset($params[\'' . $value['ori_field'] . '\'])) {' . PHP_EOL;
            $contentStr .= '            $this->set' . ucfirst($value['field']) . '($params[\'' . $value['ori_field'] . '\']);' . PHP_EOL;
            $contentStr .= '        }' . PHP_EOL;
        }
        $contentStr .= PHP_EOL;
        $contentStr .= '        return $this;' . PHP_EOL;
        $contentStr .= '    }' . PHP_EOL;

        // func checkRules
        $contentStr .= PHP_EOL;
        $contentStr .= '    /**' . PHP_EOL;
        $contentStr .= '     * 检查规则' . PHP_EOL;
        $contentStr .= '     *' . PHP_EOL;
        $contentStr .= '     * @return string[]' . PHP_EOL;
        $contentStr .= '     */' . PHP_EOL;
        $contentStr .= '    protected function checkRules(): array' . PHP_EOL;
        $contentStr .= '    {' . PHP_EOL;
        $contentStr .= '        $newData = [' . PHP_EOL;
        foreach ($this->tableFields() as $value) {
            $contentStr .= '            \'' . $value['ori_field'] . '\' => \'' . $value['type'] . '\',' . PHP_EOL;
        }
        $contentStr .= '        ];' . PHP_EOL;
        $contentStr .= PHP_EOL;
        $contentStr .= '        return array_merge(parent::checkRules(), $newData);' . PHP_EOL;
        $contentStr .= '    }' . PHP_EOL;

        // func checkMessage
        $contentStr .= PHP_EOL;
        $contentStr .= '    /**' . PHP_EOL;
        $contentStr .= '     * 错误提示' . PHP_EOL;
        $contentStr .= '     *' . PHP_EOL;
        $contentStr .= '     * @return string[]' . PHP_EOL;
        $contentStr .= '     */' . PHP_EOL;
        $contentStr .= '    protected function checkMessage(): array' . PHP_EOL;
        $contentStr .= '    {' . PHP_EOL;
        $contentStr .= '        $newData = [' . PHP_EOL;
        foreach ($this->tableFields() as $value) {
            $contentStr .= '            \'' . $value['ori_field'] . '.*\' => \'传入' . $value['comment'] . '值异常\',' . PHP_EOL;
        }
        $contentStr .= '        ];' . PHP_EOL;
        $contentStr .= PHP_EOL;
        $contentStr .= '        return array_merge(parent::checkMessage(), $newData);' . PHP_EOL;
        $contentStr .= '    }';

        return $contentStr;
    }
}
