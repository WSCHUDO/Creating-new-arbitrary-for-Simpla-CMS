<?php

/**
 * Simpla CMS
 *
 * @copyright	2018 Roman Medvedev
 * @link		http://ichudo.pro/
 * @author		Roman Medvedev
 *
 */

require_once('Simpla.php');

class Newmyvariables extends Simpla
{
	private $vars = array();
	
	
	function __construct()
	{
		parent::__construct();
		
		// Выбираем из базы настройки
		$this->db->query('SELECT name, label FROM __newmyvariables');

		// и записываем их в переменную		
		foreach($this->db->results() as $result)
			if(!($this->vars[$result->name] = @unserialize($result->label)))
				$this->vars[$result->name] = $result->label;
	}
	
	public function __get($name)
	{
		if($res = parent::__get($name))
			return $res;
		
		if(isset($this->vars[$name]))
			return $this->vars[$name];
		else
			return null;
	}
	
	public function __set($name, $label)
	{
		$this->vars[$label[0]] = $label[1];

		if(is_array($label[1]))
			$label[1] = serialize($label[1]);
		else
			$label[1] = (string) $label[1];
			
		$this->db->query('SELECT count(*) as count FROM newmyvariables WHERE name=?', $label[0]);
		if($this->db->result('count')>0)
			$this->db->query('UPDATE __newmyvariables SET label=? WHERE name=?', $label[1], $label[0]);
		else
			$this->db->query('INSERT INTO __newmyvariables SET label=?, name=?', $label[1], $label[0]);
	}
	
	public function get_newmyvariables(){
		$arr = $this->vars;
		return $arr;
	}
}