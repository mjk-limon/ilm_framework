<?php

namespace _ilmComm\Core\Media\Image;

use _ilmComm\Exceptions\MediaException;

class ImageInfo extends Image
{
    /**
     * Get image info
     *
     * @param string $image
     * @return array keys: 0-width 1-height 2-extension 3-file size
     */
    public static function getImageInfo(string $image): array
    {
        if (file_exists($image) && is_file($image)) {
            $size = filesize($image);

            if ($info = getimagesize($image)) {
                switch ($info['mime']) {
                    case 'image/jpeg':
                        $ext = 'jpg';
                        break;

                    case 'image/png':
                        $ext = 'png';
                        break;

                    case 'image/gif':
                        $ext = 'gif';
                        break;

                    default:
                        return array(0, 0, 'Unknown', 0);
                }

                list($width, $height) = $info;
                return array($width, $height, $ext, $size);
            }
        }

        return array(0, 0, 'Unknown', 0);
    }

    /**
     * Image validation
     *
     * @param string $image
     * @throws MediaException
     * @return boolean
     */
    public static function imageValidate(string $image): bool
    {
        if (!empty($image) && exif_imagetype($image)) {
            $img_info = static::getImageInfo($image);

            if ($img_info[3] > (20 * 1000 * 1000)) {
                throw MediaException::create(2);
            }

            if (!in_array(strtolower($img_info[2]), array("jpg", "png", "jpeg", "gif"))) {
                throw MediaException::create(3);
            }

            return true;
        }

        throw MediaException::create(1);
    }
}
