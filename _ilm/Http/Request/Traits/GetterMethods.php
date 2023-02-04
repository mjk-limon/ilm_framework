<?php

namespace _ilmComm\Core\Http\Request\Traits;

trait GetterMethods
{
    /**
     * @return string
     */
    public function getFragment(): string
    {
        return $this->Fragment;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->Host;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        $Path = $this->Path;

        if ($this->CleanPath) {
            $prefix = PROJECT_FOLDER;
            $this->CleanPath = false;

            if (substr($Path, 0, strlen($prefix)) == $prefix) {
                $Path = substr($Path, strlen($prefix));
                $Path = "/" . ltrim($Path, "/");
            }
        }

        return $Path;
    }

    /**
     * @return string The url encoded query string
     */
    public function getQuery(): string
    {
        return $this->Query;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->Scheme;
    }

    /**
     * Get the filename from the path (the last path segment as returned by basename())
     *
     * @return string
     */
    public function getFilename(): string
    {
        return static::filename($this->Path);
    }

    /**
     * Get the directory name from the path
     *
     * @return string
     */
    public function getDirname(): string
    {
        return static::dirname($this->Path);
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function getQueryParameter(string $name)
    {
        return rec_arr_val($this->QueryArray, $name, null);
    }

    /**
     * Get the query parameters as array
     *
     * @return array
     */
    public function getQueryArray(): array
    {
        return $this->QueryArray;
    }
}
