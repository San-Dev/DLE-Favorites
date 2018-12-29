<?php
/**
 * Работа с закладками/избранными новостями
 *
 * @link https://sandev.pro/
 * @author Sander <oleg.sandev@gmail.com>
 */

namespace Sandev;

class Favorites
{

	/**
	 * Текущий список ID новостей закладок
	 * @var array
	 */
	private $list = [];

	/**
	 * Конфиг модуля
	 * @var array
	 */
	private $config = [];


	/**
	 * Конструктор класса
	 */
	public function __construct()
	{
		$this->loadConfig();
		$this->loadList();
	}


	/**
	 * Основной обработчик контента
	 * @param string &$content [description]
	 * @return void
	 */
	public function setContent(&$content = '')
	{
		$content = preg_replace_callback("#\\[(" . $this->config['tag_name'] . ")=(\d+)\\](.+?)\\[/\\1\\]#", [&$this, 'isFavorite'], $content);
	}


	/**
	 * Добавление/удаление новости в закладки
	 * @param  integer $id ID новости
	 * @return string текст ошибки
	 */
	public function setId($id = 0)
	{
		global $is_logged,$member_id,$db;
		$id = (int)$id;
		if ($id < 1) {
			return 'Не задан ID новости';
		}

		if (!$is_logged && !$this->config['allow_guest']) {
			return 'Гости не могут использовать закладки';
		}

		if ($this->list[$id]) {
			unset($this->list[$id]);
			$list = array_keys($this->list);
		} else {
			$list = array_keys($this->list);
			array_unshift($list, $id);
		}

		if ($this->config['limit'] > 0 && count($list) > $this->config['limit']) {
			return 'Вы можете сохранить не более ' . $this->config['limit'] . ' новостей';
		}
		$list = join(',', $list);

		if ($is_logged) {
			$db->query("UPDATE ".USERPREFIX."_users SET favorites = '$list' WHERE user_id = {$member_id['user_id']}");
		} else {
			set_cookie('dle_favorites', $list, false);
		}
	}


	/**
	 * Получение текущего массива ID закладок
	 * @return array
	 */
	public function getList()
	{
		$list = array_keys($this->list);
		if (!$list) {
			//Костыль для DLE
			$list[] = 0;
		}
		return $list;
	}


	/**
	 * Проверка является ли новость избранной
	 * @param  array  $m 
	 * @return bool
	 */
	private function isFavorite($m = [])
	{
		return $this->list[$m[2]] ? $m[3] : '';
	}


	/**
	 * Получение конфига
	 * @return void
	 */
	private function loadConfig()
	{
		global $member_id;
		$this->config = include __DIR__ . '/config.php';
		$this->config['limit'] = (int)$this->config['limit'];
		if ($this->config['group'][$member_id['user_group']]) {
			$this->config['limit'] = (int)$this->config['group'][$member_id['user_group']];
		}
	}


	/**
	 * Получение списка закладок
	 * @return void
	 */
	private function loadList()
	{
		global $is_logged,$member_id;
		if (!$is_logged && !$this->config['allow_guest']) {
			return;
		}
		$list = $is_logged ? $member_id['favorites'] : $_COOKIE['dle_favorites'];
		$list = $list ? explode(',', $list) : [];
		if ($list) {
			foreach ($list as $v) {
				$v = (int)$v;
				if ($v > 0) {
					$this->list[$v] = true;
				}
			}
			if ($this->list && $this->config['limit'] > 0) {
				$this->list = array_slice($this->list, 0, $this->config['limit'], true);
			}
		}
	}

}
