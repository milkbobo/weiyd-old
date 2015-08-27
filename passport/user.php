<?php

require_once '../../bootstrap.php';
require_once 'config/init.php';
require_once 'modules/Common/InfoCenter.php';
require_once 'modules/Common/PowerCenter.php';
/* 模块名 */
$modSn = 'User';
/* 1 前台 0 后台 */
$isFrontend = 0;

/* @var $domainInfo DomainInfo global */
$domainInfo = InfoCenter::getDomainInfo(getDomain());
analyser_insert_view(Analyser::M_USER, 'userId');
InfoCenter::checkSite($isFrontend);
$mods = InfoCenter::getMods(1);
//InfoCenter::checkMod($mods, $modSn);

if (!$isFrontend) {
    session_start();
    require_once 'modules/Common/UserCenter.php';
    /* @var $passport Passport global */
    $passport = UserCenter::getVali();
    $passport->redirect('login.php');
}

/**
 * 模块主要类
 */
if (true) {
    require_once 'modules/Smarty/Tpl.php';
    require_once 'modules/Common/TplCenter.php';

    require_once 'modules/User/User.php';
    /* @var $supervisor Supervisor global */
    $supervisor = new Supervisor();

    $user = new User();
    $user->editor();

    $tpl = new Tpl();
    $tplCenter = new TplCenter();
    $tplParams = array();

    $tplCenter->setActionFiles(array(
        'Login' => 'user.Login.private.tpl.html'
        , TplCenter::SN_CHANGE_PSW => 'user.ChangePsw.public.tpl.html'
        , 'Display' => 'user.ChangePsw.public.tpl.html'
        , 'List' => 'user.List.tpl.html'
        , 'Noti' => 'user.Notifaction.tpl.html'
    ));
    $tplSn = $supervisor->tplSn;
    if ($isFrontend) {
        /* @var $tplInfo TplInfo */
        $tplInfo = $tplCenter->getInfo(User::modSn, $tplSn);
        $tplParams[Config::TPL_PARAMS_KEY] = $tplInfo->tplParams;
    }

    $tpl->setCCacheDir(getDomainFolder());

    $cacheId = $isFrontend ? getCurrentCacheId() : null;

    $tplPath = $tplCenter->getPath($modSn
                    , $isFrontend ?
                            Config::TPL_DIR_SITES : Config::TPL_DIR_BOOEN
                    , $tplSn);
    
    $compileId = dechex(crc32($tplPath));

    $isCached = $tpl->is_cached($tplPath, $cacheId, $compileId);
}

if (!$isCached) {
    require_once 'lang/Common/zh-cn/Common.Lang.php';
    require_once 'lang/User/zh-cn/User.Lang.php';

    /* @var $status Status */
    $status = $supervisor->status;
    $tplParams['_SITE_MODS'] = $mods;
    $tplParams['Domain'] = get_object_vars($domainInfo);
    $tplParams['status'] = $status;

    $target = 'index.php';
    if ($status->getName() == 'error.nopower') {
        $target = 'index.php';
    } else if (in_array($status->getName(), array('error.edit', 'success.edit'))) {
        $target = 'user.php?userAction=display';
    }

    $tplParams['status_brief'] = $status->getBrief();
    $tplParams['status_target'] = $target;

    $tplParams['Post'] = $supervisor->info;
    $tplParams['Lang'] = $lang;
    $tpl->assignAll($tplParams);
}
$tpl->show($tplPath, $cacheId, $compileId);
?>