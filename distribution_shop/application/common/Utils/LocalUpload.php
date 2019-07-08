<?php

namespace app\common\Utils;

use app\common\Constants\Constants;
use app\common\Mapping\AbstractUpload;

class LocalUpload extends AbstractUpload
{

    public function singleUpload($fileStream, $prefix, $full = true)
    {
        $filePath = $this->filePath($prefix);

        $targetPath = UPLOAD_PATH . $filePath;
        $this->validateSavePath($targetPath);

        $ext = pathinfo($fileStream['name'], PATHINFO_EXTENSION);
        $this->isImage($ext);

        $fileName = $this->randomName($ext);

        $fullFileName = $targetPath . $fileName;

        if (!move_uploaded_file($fileStream['tmp_name'], $fullFileName)) {
            throw new \RuntimeException(sprintf('Uploaded file could not be move to %s', $fullFileName));
        }

        $key = 'static' . DIRECTORY_SEPARATOR . $filePath . $fileName;
        if ($full) {
            $key = Constants::APP_URL . $key;
        }
        return $key;
    }


    public function validateSavePath($targetPath)
    {
        if (is_dir($targetPath)) {
            return true;
        }

        if (mkdir($targetPath, 0755, true)) {
            return true;
        }
        throw new \RuntimeException("Cannot create directory");
    }
}