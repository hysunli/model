<?php
namespace hysunli\model;
class Model{
	private static $config;

	public function __call( $name, $arguments ) {
		return self::parseAction($name,$arguments);
	}
	public static function __callStatic( $name, $arguments ) {
		return self::parseAction($name,$arguments);
	}

	private static function parseAction($name, $arguments ){
		//system\model\Article
		//获得哪个类调用本类
		$table = get_called_class();
		$table = strtolower(ltrim(strrchr($table,'\\'),'\\'));
		return call_user_func_array([new Base(self::$config,$table),$name],$arguments);
	}


	public static function setConfig($config){
		self::$config = $config;
	}
}







