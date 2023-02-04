<?php

namespace _ilmComm\Core\Media\Image;

use _ilmComm\Core\Media\Media;

class Image extends Media
{
    protected static function imageFormats(): array
    {
        return array(
            "jpeg" => array(
                "ext" => "jpeg",
                "mime" => "image/jpeg",
                "create_func" => "imagecreatefromjpeg",
                "save_func" => "imagejpeg"
            ),
            "jpg" => array(
                "ext" => "jpg",
                "mime" => "image/jpeg",
                "create_func" => "imagecreatefromjpeg",
                "save_func" => "imagejpeg"
            ),
            "png" => array(
                "ext" => "png",
                "mime" => "image/png",
                "create_func" => "imagecreatefrompng",
                "save_func" => "imagepng"
            ),
            "gif" => array(
                "ext" => "gif",
                "mime" => "image/gif",
                "create_func" => "imagecreatefromgif",
                "save_func" => "imagegif"
            )
        );
    }
}
