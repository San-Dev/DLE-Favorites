<?php
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
ini_set('display_errors', true);
ini_set('html_errors', false);

define('DATALIFEENGINE', true);
define('ROOT_DIR', realpath(__DIR__ . '/../../..'));
define('ENGINE_DIR', ROOT_DIR . '/engine');

include_once __DIR__ . '/loader.php';

header("Content-type: text/html; charset=" . $config['charset']);
date_default_timezone_set($config['date_adjust']);
setlocale(LC_NUMERIC, "C");

require_once DLEPlugins::Check(ENGINE_DIR . '/modules/functions.php');
dle_session();
require_once DLEPlugins::Check(ENGINE_DIR . '/modules/sitelogin.php');
if (!$is_logged) {
	$member_id['user_group'] = 5;
}

require_once __DIR__ . '/class.favorites.php';
$favmod = new Sandev\Favorites;

$error = $favmod->setId($_POST['newsid']);
echo $error ?: json_encode(['result' => true]);
