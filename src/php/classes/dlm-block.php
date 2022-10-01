<?php

namespace DLM;

Class DLMBlock {

    public static $default_options = [
        "json_file_path" => null,
        "base_download_path" => null,
        "download_callback" => null,
        "remove_from_title" => [ "HPCC", "Plugin" ]
    ];

    public $dlm_id;    
    public $options;
    public $os_list = [];
    public $files_list = [];

    /**
     * constructor
     */
    public function __construct($json_file_path, $options = [])
    {
        global $dlm_instances;

        $this->dlm_id = uniqid('dlm-form-');

        $this->options = array_merge([], $this::$default_options);
        $this->options['json_file_path'] = $json_file_path;

        $options['base_download_path'] = isset($options['base_download_path']) ? $options['base_download_path'] : '/';

        foreach ($options as $key => $val) {
            $this->options[$key] = $val;
        }
        
        $dlm_instances[$this->dlm_id] = $this->init();

        echo DLMBlock_Generator::generate($this);
    }

    /**
     * gets a file list (optionally filtered by type)
     */
    public function get_file_list($type = null) {
        $list = $this->files_list;
        if ($type) {
            $list = array_filter($list, ["DLM\DLMBlock_Utils", "filter_only_" . $type]);
        }
        return $list;
    }

    public static function title_only($obj) {
        return $obj->title;
    }

    public static function slug_only($obj) {
        return $obj->slug;
    }

    /**
     * initializes a block component
     */
    private function init() {
        $data_str = file_get_contents($this->options['json_file_path']);
        $data = json_decode($data_str);

        foreach ($data->files as $file) {
            $mapped_array = array_map([$this, 'title_only'], $this->os_list);
            
            if (!in_array($file->OS, $mapped_array)) {     
                $os_object = new \stdClass;
                $os_object->title = $file->OS;
                $os_object->slug = DLMBlock_Utils::slugify($file->OS, 'os');
                $this->os_list[] = $os_object;
            }

            $this->files_list[] = DLMBlock_Transformers::transform_file($file, $this);           
        }

        usort($this->os_list, ["DLM\DLMBlock_Utils", "sort_by_title"]);
        usort($this->files_list, ["DLM\DLMBlock_Utils", "sort_by_title"]);
        
        
        /*
        echo "PLATFORMS:<pre>";
        var_dump($this->get_file_list('platform'));
        echo "</pre><br><br>";

        echo "TOOLS:<pre>";
        var_dump($this->get_file_list('tool'));
        echo "</pre><br><br>";

        echo "PLUGINS:<pre>";
        var_dump($this->get_file_list('plugin'));
        echo "</pre><br><br>";

        echo "OS:<pre>";
        var_dump($this->os_list);
        echo "</pre><br><br>";     
        */       
        
        return $this;
    }

}
