<?php

namespace DLM;
require_once('src/php/dlm.php');

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Download Manager Block</title>
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>

    <?php
        define('dlm_json_url','8.8.4-1.json');
        define('dlm_download_domain','https://cdn.hpccsystems.com/');

        
        $data = json_decode(file_get_contents(constant('dlm_json_url')));
        $enc_data = json_encode($data);
        $os_names = [];

        $files_indexed = [
            "platform" => [],
            "tool" => [],
            "plugin" => [],
        ];

        function remove_prefix($prefix, $str) {
            if (substr($str, 0, strlen($prefix)) == $prefix) {
                $str = substr($str, strlen($prefix));
            }
            return $str;
        }

        foreach ($data->files as $file) {            
            if (!in_array($file->OS, $os_names)) {
                $os_names[] = $file->OS;
            }

            $file->Type = $file->Type === "platform" ? "platform" : (substr($file->Type, 0, strlen("plugin")) === "plugin" ? "plugin" : "tool");
            
            $file->Display_Name = remove_prefix('HPCC ', $file->Display_Name);
            $file->Display_Name = remove_prefix('Plugin ', $file->Display_Name);

            $files_indexed[$file->Type][] = $file;
        }

        /*
        function cmp($a, $b) {
            return strcmp($b->Display_Name, $a->Display_Name);
        }        

        usort($files_indexed['platform'], "cmp");
        usort($files_indexed['tool'], "cmp");
        usort($files_indexed['plugin'], "cmp");
        */

        sort($os_names);

        function get_file_list($type, $files_indexed) {
            foreach ($files_indexed[$type] as $file) {

                $download_path = constant('dlm_download_domain') . $file->Edge_Cast_Path;

                echo <<<EOT
                    <label data-dlm-type="$file->Type" data-dlm-os="$file->OS" for="$file->MD5">
                        <input type="checkbox" name="$file->MD5">
                        <a href="$download_path">
                            $file->Display_Name
                        </a>
                        <div class="dlm-download-details">
                            <span><b>Version:</b> $file->Version_Number</span><br>
                            <span><b>MD5:</b> $file->MD5</span><br>
                            <span><b>Size:</b> $file->Download_Size</span>                            
                        </div>
                    </label>
                EOT;
            }
        }
    ?>

    <div class="dlm-block">
        <div class="dlm-block__container">
    
            <div class="dlm-table dlm-table__top">

                <div class="dlm-table__col dlm_version">
                    <div class="dlm-table__col__header">
                        <h5>VERSION</h5>
                    </div>
                    <div class="dlm-table__col__body">

                        <label for="download-version-gold">
                            <input type="radio" name="download-version" id="download-version-gold" value="gold" checked="checked">
                            Gold
                        </label>
                        <p>
                            Visit the <a href="/download/archive">Version Archive</a> to<br>access previous releases.
                        </p>

                    </div>
                </div>

                <div class="dlm-table__col">
                    <div class="dlm-table__col__header">
                        <h5>OPERATING SYSTEM</h5>
                    </div>
                    <div class="dlm-table__col__body">
                        <select class="download-os" name="download-os">
                            <option disabled="" selected=""> - Choose an OS - </option>
                            <?php foreach ($os_names as $os) { ?>
                                <option value="slug"><?php echo $os ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
    
            <div class="dlm-table dlm-table__body">
                <div class="dlm-table__col">
                    <div class="dlm-table__col__header">
                        <h5>PLATFORM</h5>
                    </div>
                    <div class="dlm-table__col__body">
                        
                        <?php 
                            get_file_list('platform', $files_indexed);
                        ?>

                    </div>
                </div>
                <div class="dlm-table__col">
                    <div class="dlm-table__col__header">
                        <h5>TOOLS</h5>
                    </div>
                    <div class="dlm-table__col__body">

                        <?php 
                            get_file_list('tool', $files_indexed);
                        ?>

                    </div>
                </div>
                <div class="dlm-table__col">
                    <div class="dlm-table__col__header">
                        <h5>PLUGINS</h5>
                    </div>

                    <div class="dlm-table__col__body">

                        <?php 
                            get_file_list('plugin', $files_indexed);
                        ?>

                    </div>
                </div>
            </div>
    
            <div class="dlm-table dlm-table__button">
                <div class="dlm-table__col">
                    <button>DOWNLOAD</button>
                </div>
            </div>
    
        </div>
    </div>


    <?php new DLMBlock(constant('dlm_json_url')); ?>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
	<script src="scripts.js"></script>
  </body>
</html>


<?php 



?>