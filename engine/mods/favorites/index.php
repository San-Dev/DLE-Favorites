<?php
/**
 * Основной обработчик, подключать в engine/modules/main.php
 */

if (!$favmod) {
	require_once ENGINE_DIR . '/mods/favorites/class.favorites.php';
	$favmod = new Sandev\Favorites;
}
$favmod->setContent($tpl->result['main']);
