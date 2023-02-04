<?php

namespace _ilmComm\Core\Media\Image\ImageHandler;

use _ilmComm\Exceptions\MediaException;
use _ilmComm\Core\Media\Image\Image;

abstract class ImageHandler extends Image
{
    /**
     * Old image parse function
     *
     * @var string
     */
    protected $CreateFunc = "imagecreatefromjpeg";

    /**
     * New image save function
     *
     * @var string
     */
    protected $SaveFunc = "imagejpeg";

    /**
     * New image save extension
     *
     * @var string
     */
    protected $NewImgExt = "";

    /**
     * Forced type
     *
     * @var string
     */
    protected $ForcedType = "";

    /**
     * @param string $type
     * @return InitImageHandler
     */
    public function setForcedType(string $type): ImageHandler
    {
        $this->ForcedType = $type;
        return $this;
    }

    protected function updateOriginalFilePath(): ImageHandler
    {
        $this->setOriginalPath($this->TargetPath);
        $this->setOriginalFile($this->TargetFile);
        $this->processOriginal();
        return $this;
    }

    /**
     * Process original image
     *
     * @return InitImageHandler
     */
    public function processOriginal(): ImageHandler
    {
        if (($originalFile = $this->getOriginalFileFull()) && is_file($originalFile)) {
            // Parse file info
            $ImgInfo = @getimagesize($originalFile);

            // Build original file info
            $this->setOriginalFileInfo(array(
                "w" => rec_arr_val($ImgInfo, '0', 0),
                "h" => rec_arr_val($ImgInfo, '1', 0),
                "m" => rec_arr_val($ImgInfo, 'mime')
            ));

            // Build object info
            $this->buildHandlerInfo($this->OriginalFileInfo['m']);
        }

        return $this;
    }

    /**
     * Build handler info
     *
     * @param string $originalImgMime
     * @throws MediaException
     * @return InitImageHandler
     */
    protected function buildHandlerInfo(string $originalImgMime): ImageHandler
    {
        // Get image format array
        $imgInfoArr = static::imageFormats();

        // Get image mime array
        $imgMimesArr = array_combine(array_column($imgInfoArr, "mime"), $imgInfoArr);

        if ($Formats = rec_arr_val($imgMimesArr, $originalImgMime)) {
            // Format found
            // Build create function
            $this->CreateFunc = $Formats["create_func"];

            // Buid save funciton
            $this->SaveFunc = $Formats["save_func"];

            // Build new image extension
            $this->NewImgExt = $Formats["ext"];

            if ($this->ForcedType) {
                // Forced type exists
                // Build new image extion from force type
                $this->NewImgExt = $this->ForcedType;

                // Build save function
                $this->SaveFunc = rec_arr_val($imgInfoArr, [$this->ForcedType, "save_func"]);
            }

            return $this;
        }

        throw MediaException::create(3);
    }
}
