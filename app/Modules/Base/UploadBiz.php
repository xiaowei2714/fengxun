<?php

namespace App\Modules\Base;

use App\Modules\Base\BizSrc\FileSaveParams;
use App\Modules\BaseBiz;
use App\Utils\Utils;
use Exception;
use Intervention\Image\Facades\Image;

class UploadBiz extends BaseBiz
{
    const IMAGE = 'IMAGE';
    const UPLOAD_FILE_MAX_SIZE = 5242880;       // 5M

    /**
     * 文件上传
     *
     * @param $fileObj
     * @param $type
     * @return FileSaveParams|null
     * @throws Exception
     */
    public function uploadFile($fileObj, $type)
    {
        // 检查文件
        $checkRes = $this->checkFile($fileObj, $type);
        if (!$checkRes) {
            return null;
        }

        // 文件存储
        $storeDir = Utils::uploadDir();
        $path = $fileObj->store($storeDir);
        if (empty($path)) {
            return null;
        }

        $width = 600;
        $height = 300;

        $oriSize = getimagesize($fileObj);
        if (!empty($oriSize[0]) && !empty($oriSize[1])) {
            $height = ceil($oriSize[1] / $oriSize[0] * $width);
            if ($width > $oriSize[0]) {
                $width = $oriSize[0];
                $height = $oriSize[1];
            }
        }

        // 修改指定图片的大小
        $img = Image::make($fileObj)->resize($width, $height);

        // 插入水印, 水印位置在原图片的右下角, 距离下边距 10 像素, 距离右边距 15 像素
        $img->insert('image/watermark.png', 'bottom-right', 10, 5);

        $storePath = env('STORE_FILE_PUBLIC_PATH') . '/sl/' . $path;

        // 将处理后的图片重新保存到其他路径
        $img->save($storePath);

        // 销毁图片资源
        $img->destroy();

        // 数据库存储
        $fileParamsObj = (new FileSaveParams())
            ->setName($fileObj->getClientOriginalName())
            ->setPath($storePath)
            ->setMime($fileObj->getMimeType())
            ->setSize($fileObj->getSize());

        $res = (new FileBiz())->insertData($fileParamsObj);
        if ($res > 0) {
            return $fileParamsObj;
        }

        return null;
    }

    /**
     * 检查文件
     *
     * @param $fileObj
     * @param $type
     * @return bool
     */
    private function checkFile($fileObj, $type): bool
    {
        // 获取文件mime type类型
        $mimeType = $fileObj->getMimeType();

        // 文件大小
        $fileSize = $fileObj->getSize();

        switch ($type) {
            case self::IMAGE:
                if (!in_array($mimeType, self::allowImageMimeType())) {
                    $this->setErrorMsg('不支持的文件类型');
                    return false;
                }
                if ($fileSize > self::UPLOAD_FILE_MAX_SIZE) {
                    $this->setErrorMsg('文件不能大于5M');
                    return false;
                }

                return true;

            default:
                return false;
        }
    }

    /**
     * 允许上传的mime type
     *
     * @return string[]
     */
    private static function allowImageMimeType(): array
    {
        return [
            'IMAGE',
            'image/gif',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp'
        ];
    }
}
