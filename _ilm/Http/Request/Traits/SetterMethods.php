<?php

namespace _ilmComm\Core\Http\Request\Traits;

use _ilmComm\Core\Http\Request\Url;

trait SetterMethods
{
    /**
     * @param string $scheme
     * @return Url
     */
    public function setScheme(string $scheme): Url
    {
        $this->Scheme = strtolower($scheme);
        return $this;
    }

    /**
     * @param string $host
     * @return Url $this
     */
    public function setHost(string $host): Url
    {
        $this->Host = strtolower($host);
        return $this;
    }

    /**
     * @param string $path
     * @return Url
     */
    public function setPath(string $path): Url
    {
        $this->Path = static::normalizePath($path);
        return $this;
    }

    /**
     * @param string $fragment
     * @return Url
     */
    public function setFragment(string $fragment): Url
    {
        $this->Fragment = $fragment;
        return $this;
    }

    /**
     * Set the query from an already url encoded query string
     *
     * @param string $query encoded query string
     * @return Url
     */
    public function setQuery(string $query): Url
    {
        $this->Query = $query;
        parse_str($this->Query, $this->QueryArray);
        return $this;
    }

    /**
     * @param array $query_array
     * @return Url
     */
    public function setQueryFromArray(array $query_array): Url
    {
        $this->QueryArray = $query_array;
        $this->Query = http_build_query($this->QueryArray);
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return Url
     */
    public function setQueryParameter(string $name, $value): Url
    {
        $this->QueryArray[$name] = $value;
        $this->Query = http_build_query($this->QueryArray);
        return $this;
    }

    /**
     * @param string $segment
     * @return Url
     */
    public function appendPathSegment(string $segment): Url
    {
        if (substr($this->Path, -1) != Url::PATH_SEGMENT_SEPARATOR) {
            $this->Path .= Url::PATH_SEGMENT_SEPARATOR;
        }
        if (substr($segment, 0, 1) == Url::PATH_SEGMENT_SEPARATOR) {
            $segment = substr($segment, 1);
        }
        $this->Path .= $segment;
        return $this;
    }

    /**
     * Clean path
     *
     * @return Url
     */
    public function cleanPath(): Url
    {
        $this->CleanPath = true;
        return $this;
    }
}
