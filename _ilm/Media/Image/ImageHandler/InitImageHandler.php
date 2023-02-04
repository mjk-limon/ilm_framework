<?php

namespace _ilmComm\Core\Media\Image\ImageHandler;

use _ilmComm\Core\Media\Image\ImageHandler\Traits\Base64Image;
use _ilmComm\Core\Media\Image\ImageHandler\Traits\ResizeImage;
use _ilmComm\Core\Media\Image\ImageHandler\Traits\WatermarkImage;
use _ilmComm\Exceptions\MediaException;
use _ilmComm\Core\Media\Image\ImageInfo;

class InitImageHandler extends ImageHandler
{
    use ResizeImage,
        WatermarkImage,
        Base64Image;

    /**
     * Upload image from temp
     *
     * @param string $tmpfile
     * @param integer $fileKey
     * @return InitImageHandler
     * @throws MediaException
     */
    public function uploadImage(string $fieldName, int $fileKey = null): InitImageHandler
    {
        // Build original from tmpfile
        $tmpFile = $_FILES[$fieldName]['tmp_name'];
        ($fileKey !== null) && $tmpFile = $tmpFile[$fileKey];

        // Build target file
        $this->TargetFile = $this->TargetFile . '-u';

        if (ImageInfo::imageValidate($tmpFile) && move_uploaded_file($tmpFile, $this->getTargetFileFull())) {
            // Image uploaded
            $this->updateOriginalFilePath();
            return $this;
        }

        throw MediaException::create(0);
    }

    /**
     * Move image file
     *
     * @return InitImageHandler
     */
    public function moveImageFile(): InitImageHandler
    {
        if (!pathinfo($this->TargetFile, PATHINFO_EXTENSION)) {
            // New image extension not exists
            // Build extension from original
            $this->TargetFile = "{$this->TargetFile}.{$this->NewImgExt}";
        }

        if (rename($this->getOriginalFileFull(), $this->getTargetFileFull())) {
            // Image moved
            $this->updateOriginalFilePath();
            return $this;
        }

        throw new MediaException("Move error !");
    }

    /**
     * Create image file
     *
     * @param string $c Color name. Eg: #2AC3B0
     * @return InitImageHandler
     */
    public function createImage(string $c): InitImageHandler
    {
        // build rgb color
        $c = "#" . ltrim($c, '#');
        list($r, $g, $b) = sscanf($c, "#%02x%02x%02x");

        // build target file
        $this->TargetFile = $this->TargetFile . '-c';

        // build image from color
        $img = imagecreatetruecolor(1000, 1000);
        imagefill($img, 0, 0, imagecolorallocate($img, $r, $g, $b));
        imagepng($img, $this->getTargetFileFull());
        imagedestroy($img);

        // image created
        $this->updateOriginalFilePath();
        return $this;
    }
}
