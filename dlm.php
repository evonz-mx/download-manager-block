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
    <link rel="stylesheet" href="src/css/styles.css">
  </head>
  <body>

    <?php
        define('dlm_json_url','');
        define('base_download_path','');
    ?>

    <?php

    new DLMBlock("8.8.4-1.json", [
      "base_download_path" => 'https://cdn.hpccsystems.com/',
      "js_callback" => "some_js_function",
      "js_callback_data" => [
        "form_id" => 111
      ]
    ]);

    ?>

    <script>
      /**
       * Javascript callback for the download manager
       */
      function some_js_function(download_paths, download_trigger, data) {
        console.log(download_paths); // paths of files to download
        console.log(data); // data passed to the js_callback_data variable
        download_trigger(); // triggers the download
      }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
	<script src="src/js/dlm.js"></script>
  </body>
</html>
