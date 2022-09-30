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
        define('dlm_json_url','8.8.4-1.json');
        define('dlm_download_domain','https://cdn.hpccsystems.com/');
    ?>

    <?php new DLMBlock(constant('dlm_json_url')); ?>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
	<script src="src/js/dlm.js"></script>
  </body>
</html>
