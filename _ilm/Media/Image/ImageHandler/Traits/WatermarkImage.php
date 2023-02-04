<?php

namespace _ilmComm\Core\Media\Image\ImageHandler\Traits;

use _ilmComm\Core\Media\Image\ImageHandler\InitImageHandler;
use _ilmComm\Exceptions\MediaException;
use _ilmComm\Core\Media\Image\ImageInfo;

trait WatermarkImage
{
    /**
     * Watermark info
     *
     * @var array
     */
    private $WatermarkInfo = array();

    /**
     * Watermark image
     *
     * @param string $wtrmrk_file
     * @return InitImageHandler
     */
    public function watermarkImage(string $wtrmrk_file): InitImageHandler
    {
        /** @var InitImageHandler $this */

        // target image
        $originalFile = $this->getOriginalFileFull();

        // target image object
        $img = ($this->CreateFunc)($originalFile);

        // new temporary artboard from watermark file
        $wtmrk = imagecreatefrompng($wtrmrk_file);
        imagealphablending($wtmrk, false);
        imagesavealpha($wtmrk, true);

        // watermark
        $this->buildWatermarkInfo($wtrmrk_file);
        list($dst_x, $dst_y, $src_w, $src_h) = $this->WatermarkInfo;
        imagecopy($img, $wtmrk, $dst_x, $dst_y, 0, 0, $src_w, $src_h);

        // save new image
        imagejpeg($img, $originalFile, 100);

        // destroy cache images
        imagedestroy($img);
        imagedestroy($wtmrk);
        return $this;
    }

    /**
     * Build watermark info
     *
     * @param string $wtmrk
     * @return void
     */
    private function buildWatermarkInfo(string $wtmrk)
    {
        $ImgInfo = ImageInfo::getImageInfo($wtmrk);

        if (rec_arr_val($ImgInfo, '0')) {
            $this->WatermarkInfo = [
                (($this->TargetFileInfo["w"] / 2) - ($ImgInfo[0] / 2)),
                (($this->TargetFileInfo["h"] / 2) - ($ImgInfo[1] / 2)),
                $ImgInfo[0],
                $ImgInfo[1]
            ];

            return;
        }

        throw MediaException::create(3);
    }
}
