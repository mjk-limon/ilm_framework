<?php

namespace _ilmComm\Core\Http\Request;

use _ilmComm\Core\Http\Request\Traits\GetterMethods;
use _ilmComm\Core\Http\Request\Traits\SetterMethods;

class Url
{
    use SetterMethods,
        GetterMethods;

    /**
     * Path segment separator
     */
    const PATH_SEGMENT_SEPARATOR = '/';

    /**
     * Write url original
     */
    const WRITE_FLAG_AS_IS = 0;

    /**
     * Write url without scheme
     */
    const WRITE_FLAG_OMIT_SCHEME = 1;

    /**
     * Write url without host
     */
    const WRITE_FLAG_OMIT_HOST = 2;

    /**
     * Original url
     *
     * @var string
     */
    protected $OriginalUrl = "";

    /**
     * Url scheme, https/http/ftp...
     *
     * @var string
     */
    protected $Scheme = "";

    /**
     * Url host
     *
     * @var string
     */
    protected $Host = "";

    /**
     * Url path
     *
     * @var string
     */
    protected $Path = "";

    /**
     * Query string
     *
     * @var string
     */
    protected $Query = "";

    /**
     * Url fragment, #....
     *
     * @var string
     */
    protected $Fragment = "";

    /**
     * Query parameter array
     *
     * @var array
     */
    protected $QueryArray = array();

    /**
     * Clean project folder from path
     *
     * @var boolean
     */
    protected $CleanPath = false;

    /**
     * Url Constructor
     *
     * @param null|string $url
     */
    public function __construct($url = null)
    {
        $this->buildUrl($url);
        $this->buildUrlData();
    }

    /**
     * Build url
     *
     * @param null|string $url
     * @return void
     */
    private function buildUrl($url)
    {
        if (isset($_POST['skeleton_LOAD'])) {
            $RqUri = PROJECT_FOLDER . ltrim($_POST['page'], "/");
        } else {
            $RqUri = $_SERVER['REQUEST_URI'];
        }

        $this->OriginalUrl = ($url === null) ? PROTOCOL . HTTP_HOST . $RqUri : trim($url);
    }

    private function buildUrlData()
    {
        $urlo = parse_url($this->OriginalUrl);

        if (isset($urlo['scheme']) && !$this->is_protocol_relative()) {
            $this->Scheme = strtolower($urlo['scheme']);
        }
        if (isset($urlo['host'])) {
            $this->Host = strtolower($urlo['host']);
        }
        if (isset($urlo['path'])) {
            $this->Path = static::normalizePath($urlo['path']);
        }
        if (isset($urlo['query'])) {
            $this->Query = $urlo['query'];
        }
        if ($this->Query != '') {
            parse_str($this->Query, $this->QueryArray);
        }
        if (isset($urlo['fragment'])) {
            $this->Fragment = $urlo['fragment'];
        }
    }

    /**
     * Write url
     *
     * @param integer $write_flags
     * @return string
     */
    public function write(int $write_flags = self::WRITE_FLAG_AS_IS): string
    {
        $show_scheme = $this->Scheme && (!($write_flags & self::WRITE_FLAG_OMIT_SCHEME));
        $show_authority = $this->Host && (!($write_flags & self::WRITE_FLAG_OMIT_HOST));

        $url = ($show_scheme ? $this->Scheme . ':' : '');
        ($show_authority || $this->Scheme == 'file') && $url .= '//';
        $show_authority && $url .= $this->getHost();
        $this->Path && $url .= $this->getPath();
        $this->Query && $url .= '?' . $this->getQuery();
        $this->Fragment && $url .= '#' . $this->getFragment();
        return $url;
    }

    /**
     * Write url without scheme and host
     *
     * @return string
     */
    public function writeTiny(): string
    {
        return $this->write(
            static::WRITE_FLAG_OMIT_SCHEME |
                static::WRITE_FLAG_OMIT_HOST
        );
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasQueryParameter($name): bool
    {
        return isset($this->QueryArray[$name]);
    }

    /**
     * Check whether the path is within another path
     *
     * @param string $another_path
     * @return bool True if $this->Path is a subpath of $another_path
     */
    public function isInPath($another_path): bool
    {
        $p = static::normalizePath($another_path);
        if ($p == $this->Path) {
            return true;
        }
        if (substr($p, -1) != self::PATH_SEGMENT_SEPARATOR) {
            $p .= self::PATH_SEGMENT_SEPARATOR;
        }
        return (strlen($this->Path) > $p && substr($this->Path, 0, strlen($p)) == $p);
    }

    /**
     * @param string $path
     * @return string
     */
    public static function normalizePath($path)
    {
        return $path;
    }

    /**
     * @param string $path
     * @return string
     */
    public static function filename(string $path): string
    {
        if (substr($path, -1) == self::PATH_SEGMENT_SEPARATOR) {
            return '';
        }
        return basename($path);
    }

    /**
     * @param string $path
     * @return string
     */
    public static function dirname(string $path): string
    {
        if (substr($path, -1) == self::PATH_SEGMENT_SEPARATOR) {
            return substr($path, 0, -1);
        }

        $d = dirname($path);
        if ($d == DIRECTORY_SEPARATOR) {
            $d = self::PATH_SEGMENT_SEPARATOR;
        }
        return $d;
    }

    /**
     * @param string $url
     * @return Url
     */
    public static function parse($url)
    {
        return new static($url);
    }

    public function is_protocol_relative()
    {
        return (substr($this->OriginalUrl, 0, 2) == '//');
    }

    public function __toString()
    {
        return $this->write();
    }
}
