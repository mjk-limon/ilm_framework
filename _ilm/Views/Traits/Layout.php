<?php

namespace _ilmComm\Core\Views\Traits;

trait Layout
{
    /**
     * Layout
     *
     * @param string $filename
     * @param array $vars
     * @return void
     */
    public function layout(string $filename, array $vars = [], int $type = 1)
    {
        foreach ($vars as $vname => $vval) {
            ${$vname} = $vval;
        }

        if ($type == static::LAYOUT_HTML) {
            $LayoutPath = $this->getLayoutPath($filename, 'layouts/');
            include $LayoutPath;
            return;
        } 
        
        if ($type == static::LAYOUT_ASSETS_CSS) {
            $LayoutPath = $this->getLayoutPath($filename, 'assets/', false, '.css');
            if (file_exists($LayoutPath)) {
                echo sprintf('<link rel="stylesheet" href="%s" />', asset($LayoutPath));
            }
            return;
        }
        
        if ($type == static::LAYOUT_ASSETS_JAVASCRIPT) {
            $LayoutPath = $this->getLayoutPath($filename, 'assets/', false, '.js');
            $TextJavascript = file_exists($LayoutPath) ? file_get_contents($LayoutPath) : '';
            echo sprintf('<script type="text/javascript" src="%s"></script>', $TextJavascript);
            return;
        }
    }

    public function extend(string $filename)
    {
        foreach (get_defined_vars() as $vname => $vval) {
            ${$vname} = $vval;
        }

        include $this->getLayoutPath($filename, 'layouts/', true);
    }

    /**
     * Get layout path
     *
     * @param string $f
     * @param string $subf
     * @param boolean $force_root
     * @return string
     */
    public function getLayoutPath(string $f, string $subf = "", bool $force_root = false, string $ext = '.php'): string
    {
        $BranchName = static::getCurrentBranch();
        $RootPath = "public/{$this->PanelName}/{$subf}";
        $CustomRootPath = "public/lib/branches/{$BranchName}/{$this->PanelName}/{$subf}";
        $PathSuffix = implode("/", explode(".", $f));

        if (!$force_root && file_exists($CustomRootPath . $PathSuffix . $ext)) {
            return $CustomRootPath . $PathSuffix . $ext;
        }

        return $RootPath . $PathSuffix . $ext;
    }

    private static function getCurrentBranch(): string
    {
        $BranchName = "master";
        $FilePath = doc_root('public/lib/etc/branch');
        file_exists($FilePath) && $BranchName = trim(file_get_contents($FilePath));
        return $BranchName;
    }
}
