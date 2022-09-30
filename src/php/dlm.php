<?php

namespace DLM;

$dlm_instances = [];
$dlm_slug_cache = [];

global $dlm_slug_cache;
global $dlm_instances;

require_once('classes/dlm-block.php');
require_once('classes/dlm-transformers.php');
require_once('classes/dlm-utils.php');
require_once('classes/dlm-generate.php');