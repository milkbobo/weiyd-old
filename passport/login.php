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
//var_dump($domainInfo);
analyser_insert_view(Analyser::M_USER, 'userId');

InfoCenter::checkSite($isFrontend);

$mods = InfoCenter::getMods(1);
//InfoCenter::checkMod($mods, $modSn);

if (!$isFrontend) {
    session_start();
    require_once 'modules/Common/UserCenter.php';
    /* @var $passport Passport global */
    $passport = UserCenter::getVali();
    $passport->redirect('index.php', true);
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
    $loader = new BoLoader();

    // setup inviter
    $inviter = $loader->getGet('inviter', null);
    if ($inviter) {
        setcookie('inviter', $inviter, time() + 3600 * 24 * 360, '/');
        !isset($_COOKIE['referer']) && setcookie('referer', getenv('HTTP_REFERER'), time() + 3600 * 24 * 360, '/');
    }
    $user = new User();
    $user->loginer();

    $tpl = new Tpl();
    $tplCenter = new TplCenter();
    $tplParams = array();

    $tplCenter->setActionFiles(array(
        'Login' => 'user.Login.passport.tpl.html'
        , 'Display' => 'user.ChangePsw.public.tpl.html'
        , 'ChangePsw' => 'user.ChangePsw.public.tpl.html'
        , 'List' => 'user.List.tpl.html'
        , 'PhoneVerify' => 'user.PhoneVerify.tpl.html'
        , 'EmailVerify' => 'user.EmailVerify.tpl.html'
        , 'Add' => 'user.Reg.tpl.html'
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
//    require_once 'lang/Common/zh-cn/Common.Notifaction.php';
    require_once 'lang/User/zh-cn/User.Lang.php';
//    require_once 'lang/User/zh-cn/User.Notifaction.php';
    /* @var $status Status */
    $status = $supervisor->status;
    $tplParams['_SITE_MODS'] = $mods;
    $tplParams['Domain'] = get_object_vars($domainInfo);
    $tplParams['status'] = $status;

    $target = 'login.php';
    $status_name = $status->getName();

    if ($status_name == 'error.nopower') {
        $target = null;
    } else if (in_array($status_name, array('error.logout', 'success.logout'))) {
        $target = 'login.php';
    } else if (in_array($status_name, array('success.login'))) {
        $target = 'index.php';
    }

    $tplParams['vali_state'] = ValiError::state();
    $tplParams['status_brief'] = $status->getBrief();
    $tplParams['status_target'] = $target;
    $tplParams['status_name'] = $status_name;
    $tplParams['ticket'] = isset($GLOBALS['ticket']) ? $GLOBALS['ticket'] : null;

    $tplParams['Post'] = $supervisor->info;
    $tplParams['Lang'] = $lang;
    $tpl->assignAll($tplParams);
}
$tpl->show($tplPath, $cacheId, $compileId);
?>

