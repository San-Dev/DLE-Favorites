<?php

use Sandev\Favorites;

error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);

define('DATALIFEENGINE', true);
define('ROOT_DIR', dirname(__DIR__, 3));
define('ENGINE_DIR', ROOT_DIR . '/engine');

include_once __DIR__ . '/loader.php';

header("Content-type: text/html; charset=" . $config['charset']);
date_default_timezone_set($config['date_adjust']);
setlocale(LC_NUMERIC, "C");

require_once DLEPlugins::Check(ENGINE_DIR . '/modules/functions.php');
dle_session();
require_once DLEPlugins::Check(ENGINE_DIR . '/modules/sitelogin.php');
$is_logged || $member_id = ['user_group' => 5];

require_once __DIR__ . '/class.favorites.php';

$error = (new Favorites)->setId($_POST['newsid']);
echo $error ?: json_encode(['result' => true]);
