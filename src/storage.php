<?php
namespace Caichuanhai;

use \Caichuanhai\DirverInterface;

/**
 * 存储类
 */
class Storage
{

	private $driver = null;

	function __construct($driver, $option = array())
	{
		$driver = ucfirst($driver);
		$driverFile = __DIR__.'\Drivers\\'.$driver.'.php';

		if(!file_exists($driverFile)) die($driver.'存储驱动文件不存在');

		$class = '\Caichuanhai\Drivers\\'.$driver;
		$this->driver = new $class($option);

		if(!$this->driver instanceof DirverInterface) die('存储驱动必须实现DirverInterface接口');
	}

	public function __call($method, $args)
	{
		return call_user_func_array(array($this->driver, $method), $args);
	}

}