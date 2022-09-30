<?php 

namespace DLM;

/**
 * 
 */
Class DLMBlock_Transformers {

    /**
     * gets the options
     */
    public static function get_options($p = null) {
        return $p ? $p->options : DLMBlock::$default_options;
    }

    /**
     * transforms file properties into displayable object
     */
    public static function transform_file($file, $p = null) {
        $t = new DLMBlock_Transformers();

        $mapped_array_title = array_map([$p, 'title_only'], $p->os_list);
        $mapped_array_slug = array_map([$p, 'slug_only'], $p->os_list);

        $return_file = new \stdClass();
        $return_file->dlm_id = uniqid('dlm-file-');
        $return_file->title = $t->transform_file_title($file->Display_Name, $p);
        $return_file->slug = DLMBlock_Utils::slugify($return_file->title, 'file');
        $return_file->url = $t->transform_file_url($file->Edge_Cast_Path, $p);
        $return_file->version = $file->Version_Number;
        $return_file->md5 = $file->MD5;
        $return_file->os = $file->OS;
        $return_file->os_slug = $mapped_array_slug[array_search($file->OS, $mapped_array_title)];
        $return_file->type = $t->transform_file_type($file->Type, $p);
        $return_file->size = $file->Download_Size;

        return $return_file;
    }

    /**
     * detects the type (category) of file
     */
    public function transform_file_type($type, $p = null) {
        $type = strtolower($type);
        switch (true) {
            case $type === "platform":
                $type = "platform";
                break;
            case strpos($type, 'plugin') === 0:
                $type = "plugin";
                break;
            default:
                $type = "tool";
        }
        return $type;
    }

    /**
     * transforms file title to display (trim prefix, ect)
     */
    public static function transform_file_title($title, $p = null) {    
        $options = DLMBlock_Transformers::get_options($p);
        foreach ($options['remove_from_title'] as $remove_str) {
            $title = DLMBlock_Utils::remove_prefix($remove_str . ' ', $title);
        }
        return $title;
    }

    /**
     * creates download url
     */
    public static function transform_file_url($edge_cast_path, $p = null) {
        $options = DLMBlock_Transformers::get_options($p);
        return $options['base_download_path'] . $edge_cast_path;
    }
}