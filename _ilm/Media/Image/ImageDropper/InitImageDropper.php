<?php

namespace _ilmComm\Core\Media\Image\ImageDropper;

use _ilmComm\Core\Media\Image\ImageHandler\InitImageHandler;

class InitImageDropper extends ImageDropper
{
    public function uploadDropper()
    {
        $img = new InitImageHandler;

        // process tmpmoves
        foreach ($this->ImageArr as $ival) {
            $originalFile = $this->OriginalPath . $ival;
            $tmpFile = doc_root("proimg/_tmp_upload/tmpmoves{$ival}");

            if (is_file($originalFile)) {
                rename($originalFile, $tmpFile);
            }
        }

        // tmpmoves to final file
        foreach ($this->ImageArr as $ikey => $ival) {
            $img->setOriginalPath(doc_root())
                ->setOriginalFile("proimg/_tmp_upload/tmpmoves{$ival}")
                ->processOriginal()
                ->setTargetPath($this->TargetPath)
                ->setTargetFilePrefix($this->TargetFilePrefix)
                ->setTargetFile($ikey + $this->TargetFileIndex)
                ->moveImageFile();
        }
    }

    public function uploadProimgDropper($drp_Arr, $thumb = false)
    {
        $this->drpImgCount = array();

        foreach ($drp_Arr as $dkey => $dname) {
            $postDrpKey = ($dname) ? 'total_image_' . restyle_url($dname, true) : 'total_image';
            $img_Arr = !empty($_POST[$postDrpKey]) ? explode("_-_", $_POST[$postDrpKey]) : array("");

            $this->tprefix = restyle_url($dname, true);

            $this->drpImgCount[] = count($img_Arr);
            $this->uploadDropper($img_Arr, 1, ($thumb && !$dkey));
        }
        return true;
    }

    public function uploadVariantTextures($variant_Arr)
    {
        $txtr_Arr = $_POST["clr_texture"];
        foreach ($txtr_Arr as $tkey => $timg) {
            if ($timg) {
                $org = doc_root($timg);
                $txtr = $this->target . restyle_url($variant_Arr[$tkey], true) . "-texture.png";
                file_exists($org) && rename($org, $txtr);
            }
        }
    }
}
