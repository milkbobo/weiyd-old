<?php

/*
 * MoXie (SysTem128@GMail.Com) 2009-8-3 18:17:05
 * $Id$
 *
 */
require_once '../../bootstrap.php';
require_once 'config/init.php';

include_once 'api/lib/util/Encryptor.php';
include_once 'api/lib/sso/SessionConveyer.php';
session_start();
if (isset($_GET['ticket'])) {
    $sessionConveyer = new SessionConveyer();
    $ticket = $_GET['ticket'];
    $sessionConveyer->parse($ticket, '9ddbedba2863d61be00042e03846a543');
    $udata = array();
    if ($_SESSION['id'] > 0) {
        die("logined");
    }
    if ($sessionConveyer->vali(null)) {
        $udata = json_decode($sessionConveyer->getData(), true);
        /**
         * 初始化系统引擎
         */
        $_SESSION['id'] = $udata['id'];
        $_SESSION['account'] = $udata['userName'];
        $_SESSION['password'] = $udata['password'];
    }
}
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'logout') {
    session_destroy();
}
ob_end_flush();
?>
