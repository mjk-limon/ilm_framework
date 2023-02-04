<?php

namespace _ilmComm\Core\Media\Image\ImageHandler\Traits;

use _ilmComm\Exceptions\MediaException;

trait Base64Image
{
    public function base64(): string
    {
        $originalFile = $this->getOriginalFile();

        if (!file_exists($originalFile)) {
            throw new MediaException("Target file not found !", 0);
        }

        $ImageString = file_get_contents($originalFile);
        return 'data:image/jpeg;base74,' . base64_encode($ImageString);
    }
}
