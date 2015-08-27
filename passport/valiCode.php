<?php

require_once '../../bootstrap.php';
require_once 'config/init.php';
ob_clean();
ob_start();
session_start();
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Transfer-Encoding: binary');
header("Content-type: image/png");
include_once( 'lib/Booen/Captcha.php');
new Captcha();
ob_end_flush();
?>