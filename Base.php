<?php
namespace hysunli\model;
use PDO;
use PDOException;

class Base{
	private static $pdo = NULL;
	private $table;
	private $where = '';


	public function __construct($config,$table) {
		$this->connect($config);
		$this->table = $table;
	}

	/**
	 * 链接数据库
	 * @param $config
	 */
	private function connect($config){
		//如果属性$pdo已经链接过数据库了，不需要重复链接了
		if(!is_null(self::$pdo)) return;
		try{
			$dsn = "mysql:host=" . $config['db_host'] . ";dbname=" . $config['db_name'];
			$user = $config['db_user'];
			$password = $config['db_password'];
			$pdo = new PDO($dsn,$user,$password);
			//设置错误
			$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			//设置字符集
			$pdo->query("SET NAMES " . $config['db_charset']);
			//存到静态属性中
			self::$pdo = $pdo;

		}catch (PDOException $e){
			exit($e->getMessage());
		}
	}

	public function where($where){
		$this->where = " WHERE {$where}";
		return $this;
	}

	/**
	 * 获取全部数据
	 */
	public function get(){

		$sql = "SELECT * FROM {$this->table} {$this->where}";
		return $this->q($sql);
	}

	public function find($pri){
		//获得主键字段，比如cid还是aid
		//如果是Article::find(4)，那么现在$priField它是aid
		$priField = $this->getPri();
		//经过$this->where方法之后，那么$this->where的值是 WHERE aid=4
		$this->where("{$priField}={$pri}");
		$sql = "SELECT * FROM {$this->table} {$this->where}";
//		echo $sql;
		$data = $this->q($sql);
//		p($data);
		//把原来的二维数组变为一维数组
		$data = current($data);
//		p($data);
		$this->data = $data;
		return $this;
	}

	public function findArray($pri){
		$obj = $this->find($pri);
		return $obj->data;
	}


	public function toArray(){
		return $this->data;
	}


	/**
	 * 获得表的主键
	 */
	public function getPri(){
		$desc = $this->q("DESC {$this->table}");
		//打印desc看结果调试
		//p($desc);
		$priField = '';
		foreach ($desc as $v){
			if($v['Key'] == 'PRI'){
				$priField = $v['Field'];
				break;
			}
		}
		return $priField;
	}

	public function count($field='*'){
		$sql = "SELECT count({$field}) as c FROM {$this->table} {$this->where}";
		$data = $this->q($sql);
//		p($data);
		return $data[0]['c'];
	}


	/**
	 * 执行有结果集操作
	 * @param $sql [sql语句]
	 *
	 * @return mixed
	 */
	public function q($sql){
		try{
			$result = self::$pdo->query($sql);
			$data = $result->fetchAll(PDO::FETCH_ASSOC);
			return $data;
		}catch (PDOException $e){
			exit($e->getMessage());
		}

	}

	/**
	 * 执行无结果集操作例如：增删改
	 * @param $sql
	 */
	public function e($sql){
		try{
			return self::$pdo->exec($sql);

		}catch (PDOException $e){
			exit($e->getMessage());
		}
	}














}