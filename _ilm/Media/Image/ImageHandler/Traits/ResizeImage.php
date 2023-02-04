<?php

namespace _ilmComm\Core\Media\Image\ImageHandler\Traits;

use _ilmComm\Core\Media\Image\ImageHandler\InitImageHandler;

trait ResizeImage
{
    /**
     * Original image old
     *
     * @var string
     */
    private $Resize_Old = "";

    /**
     * New resized file info
     *
     * @var array
     */
    private $Resize_FileInfo = [];

    /**
     * @param integer $nw
     * @param integer $nh
     * @param boolean $delorg
     * @return InitImageHandler
     */
    public function resizeImage(int $nw, int $nh): InitImageHandler
    {
        /** @var InitImageHandler $this */
        $this->buildResizeInfo($nw, $nh);

        // target image object
        $originalFile = $this->getOriginalFileFull();
        $img = ($this->CreateFunc)($originalFile);

        // new temporary artboard
        $tmp = imagecreatetruecolor($this->Resize_FileInfo["w"], $this->Resize_FileInfo["h"]);
        imagesavealpha($tmp, true);
        imagefill($tmp, 0, 0, imagecolorallocatealpha($tmp, 0, 0, 0, 127));

        // place target image into artboard
        imagecopyresampled($tmp, $img, 0, 0, 0, 0, $this->Resize_FileInfo["w"], $this->Resize_FileInfo["h"], $this->OriginalFileInfo["w"], $this->OriginalFileInfo["h"]);

        // save new image
        ($this->SaveFunc)($tmp, $this->getTargetFileFull());
        $this->updateOriginalFilePath();

        // destroy cache images
        imagedestroy($img);
        imagedestroy($tmp);
        return $this;
    }

    public function deleteResizeOrginal(): InitImageHandler
    {
        if (is_file($this->Resize_Old)) {
            unlink($this->Resize_Old);
        }
        return $this;
    }

    /**
     * Build new resized image info
     *
     * @param integer $new_width
     * @param integer $new_height
     * @return void
     */
    private function buildResizeInfo(int $new_width, int $new_height)
    {
        if (empty($new_height)) {
            // init new height by ratio
            $ofInfo = $this->OriginalFileInfo;
            $new_height = (($ofInfo["h"] / $ofInfo["w"]) * $new_width);
        }

        //build old original
        $this->Resize_Old = $this->getOriginalFileFull();

        // init new resized image target
        $this->TargetFile = $this->TargetFile . '-r';

        // build resized file height and width
        $this->Resize_FileInfo["w"] = $new_width;
        $this->Resize_FileInfo["h"] = $new_height;
    }
}
