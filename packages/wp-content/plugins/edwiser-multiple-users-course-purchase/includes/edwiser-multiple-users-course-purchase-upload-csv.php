<?php

require_once dirname(dirname(dirname(dirname(__DIR__)))).'/wp-load.php';
$csv_file = $_FILES['wdm_user_csv']['tmp_name'];
echo $csv_file;
die();
