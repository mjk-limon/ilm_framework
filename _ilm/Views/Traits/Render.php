<?php

namespace _ilmComm\Core\Views\Traits;

trait Render
{
    /**
     * Renderable Panel Name
     *
     * @var string
     */
    private $PanelName = "";

    /**
     * Head data
     *
     * @var array
     */
    private $HeadData = array();

    /**
     * Build panel
     *
     * @param string $file
     * @return void
     */
    private function buildPanel(string $file)
    {
        $this->PanelName = $file;
    }

    /**
     * Build sub view info
     *
     * @param string|array $subview
     * @return void
     */
    private function buildSubViewInfo($subview)
    {
        if (is_array($subview)) {
            $subfile = rec_arr_val($subview, "file");
            $this->subView = $this->getLayoutPath($subfile);
            $this->onlyLoad = "";

            if ($Layout = rec_arr_val($subview, "layout")) {
                $this->subView = $this->getLayoutPath($Layout, 'layouts/');

                if ($OnlyLoad = rec_arr_val($subview, "partial_load")) {
                    $this->onlyLoad = $OnlyLoad;
                }
            }
            return;
        }

        $this->subView = $this->getLayoutPath($subview);
    }

    /**
     * Build render menifest
     *
     * @return void
     */
    protected function buildRenderMenifest()
    {
        if ($this->HeadMeta) {
            $this->HeadData = array(
                "title" => $this->HeadMeta->getPageMetaTitle(),
                "description" => $this->HeadMeta->getPageMetaContent(),
                "keywords" => $this->HeadMeta->getPageMetaKeywords(),
                "oginfo" => array(
                    'title'  => $this->HeadMeta->getOgTitle(),
                    'description' => $this->HeadMeta->getOgDescription(),
                    'image' => $this->HeadMeta->getOgImage(),
                    'image_ext' => $this->HeadMeta->getOgImageInfo('e'),
                    'image_width' => $this->HeadMeta->getOgImageInfo('w'),
                    'image_height' => $this->HeadMeta->getOgImageInfo('h')
                ),
                "ref" => $this->HeadMeta->getReference(),
                "newurl" => $this->HeadMeta->getRedirectedUrl(),
                "extra_info" => $this->HeadMeta->getExtraInfo()
            );
        }
    }

    /**
     * Render content
     *
     * @var string $file
     * @var array $options
     * @return void
     */
    public function render(string $file, array $options = [])
    {
        $this->buildPanel($file);
        $this->buildRenderMenifest();
        
        // Init render
        if ($SubView = rec_arr_val($options, "sub_view")) {
            // Subview exists
            // Init subview file
            $this->buildSubViewInfo($SubView);

            if (rec_arr_val($options, "omitpanel") || rec_arr_val($options, "skeleton")) {
                // Skeleton request
                // Start output buffering
                ob_start();
                include doc_root($this->subView);

                if (rec_arr_val($options, "omitpanel")) {
                    return;
                }

                // Clear buffer and put content to variable
                $this->HeadData["content"] = ob_get_clean();
                @ob_clean();

                // Print json encoded variable
                // header('Content-Type: application/json; charset=utf-8');
                echo json_encode($this->HeadData);
                return;
            }
        }

        // Include template file
        include doc_root("public/" . $this->PanelName . ".php");
    }
}
