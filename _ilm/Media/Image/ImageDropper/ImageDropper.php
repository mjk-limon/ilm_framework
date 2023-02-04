<?php

namespace _ilmComm\Core\Media\Image\ImageDropper;

use _ilmComm\Core\Media\Image\Image;

abstract class ImageDropper extends Image
{
    protected $ImageArr = array();

    protected $TargetFileIndex = 1;

    protected $TotalUploaded = 0;

    public function setDropperImages(array $images)
    {
        $this->ImageArr = array_filter($images);
        return $this;
    }

    public function setTargetFileIndex(int $index)
    {
        $this->TargetFileIndex = $index;
        return $this;
    }

    public function getImgFldVal()
    {
        return implode(",", $this->drpImgCount);
    }
}
