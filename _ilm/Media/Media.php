<?php

namespace _ilmComm\Core\Media;

use _ilmComm\Core\Media\Image\ImageDropper\InitImageDropper;
use _ilmComm\Core\Media\Image\ImageHandler\InitImageHandler;

abstract class Media
{
    const KEEP_OLD_TARGET_PATH = 0;
    const DELETE_OLD_TARGET_PATH = 1;

    /**
     * Original path
     *
     * @var string
     */
    protected $OriginalPath = "";

    /**
     * Original file prefix
     *
     * @var string
     */
    protected $OriginalFilePrefix = "";

    /**
     * Original file
     *
     * @var string
     */
    protected $OriginalFile = "";

    /**
     * Original file info 
     *
     * @var array
     */
    protected $OriginalFileInfo = array();

    /**
     * Target path
     *
     * @var string
     */
    protected $TargetPath = "";

    /**
     * Target file prefix
     *
     * @var string
     */
    protected $TargetFilePrefix = "";

    /**
     * Target file
     *
     * @var string
     */
    protected $TargetFile = "";

    /**
     * constructor
     */
    public function __construct()
    {
        $this->TargetFile = round(microtime(true)) . rand(10, 99);
        $this->TargetPath = $this->OriginalPath = doc_root('proimg/_tmp_upload/');
    }

    /**
     * Set original path
     *
     * @param string $path
     * @return InitImageHandler|InitImageDropper
     */
    public function setOriginalPath(string $path): Media
    {
        $this->OriginalPath = rtrim($path, "\\/") . DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * Set original file prefix
     *
     * @param string $prefix
     * @return InitImageHandler|InitImageDropper
     */
    public function setOriginalFilePrefix(string $prefix): Media
    {
        $this->OriginalFilePrefix = $prefix;
        return $this;
    }

    /**
     * Set original file
     *
     * @param string $file
     * @return InitImageHandler|InitImageDropper
     */
    public function setOriginalFile(string $file): Media
    {
        $this->OriginalFile = ltrim($file, "\\/");
        return $this;
    }

    /**
     * Set original file info
     *
     * @param array $info Associative array, Keys:-
     *              w - width,
     *              h - height,
     *              m - mime,
     *              s - file size
     * @return InitImageHandler|InitImageDropper
     */
    public function setOriginalFileInfo(array $info): Media
    {
        $this->OriginalFileInfo = $info;
        return $this;
    }

    /**
     * Set target path
     *
     * @param string $path
     * @return InitImageHandler|InitImageDropper
     */
    public function setTargetPath(string $path): Media
    {
        $this->TargetPath = rtrim($path, "\\/") . DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * Set target file prefix
     *
     * @param string $prefix
     * @return InitImageHandler|InitImageDropper
     */
    public function setTargetFilePrefix(string $prefix): Media
    {
        $this->TargetFilePrefix = $prefix;
        return $this;
    }

    /**
     * Set target file
     *
     * @param string $file
     * @return InitImageHandler|InitImageDropper
     */
    public function setTargetFile(string $file): Media
    {
        $this->TargetFile = ltrim($file, "\\/");
        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalFile(): string
    {
        return $this->OriginalFile;
    }

    /**
     * @return string
     */
    public function getOriginalFileFull(): string
    {
        $originalFile = str_replace("/", DIRECTORY_SEPARATOR, $this->OriginalFile);
        return $this->OriginalPath . $originalFile;
    }

    /**
     * @return string
     */
    public function getTargetFile(): string
    {
        $target = '';

        if(($directory = pathinfo($this->TargetFile, PATHINFO_DIRNAME)) != '.') {
            // Directory name exists
            $target .= $directory . '/';
        }

        $target .= $this->TargetFilePrefix;
        $target .= pathinfo($this->TargetFile, PATHINFO_FILENAME);

        if ($ext = pathinfo($this->TargetFile, PATHINFO_EXTENSION)) {
            $target .= '.' . $ext;
        }

        return $target;
    }

    /**
     * @return string
     */
    public function getTargetFileFull(): string
    {
        $targetFile = str_replace('/', DIRECTORY_SEPARATOR, $this->getTargetFile());
        return $this->TargetPath . $targetFile;
    }

    /**
     * Process path
     *
     * @return InitImageHandler|InitImageDropper
     */
    public function processPath(bool $cleanOldTarget = self::KEEP_OLD_TARGET_PATH): Media
    {
        if ($cleanOldTarget && $this->TargetPath && is_dir($this->TargetPath)) {
            // Delete target directory
            deleteDir($this->TargetPath);
        }

        if ($this->TargetPath && !is_dir($this->TargetPath)) {
            // Target directory not exists
            // Make directory
            mkdir($this->TargetPath, 0777, true);
        }

        return $this;
    }
}
