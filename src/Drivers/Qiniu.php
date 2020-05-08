<?php
namespace caichuanhai\driver;

use caichuanhai\DirverInterface;
use \Qiniu\Auth;
use \Qiniu\Storage\UploadManager;

/**
 * 七牛存储类
 */
class Qiniu implements DirverInterface
{

	private $bucket, $auth;

	private $uploadToken = null;

	function __construct($option = array('ak' => '', 'sk' => ''))
	{
		$this->auth = new Auth($option['ak'], $option['sk']);
	}

	function setBucket($bucket)
	{
		if($bucket == $this->bucket) return true;

		$this->bucket = $bucket;
		$this->uploadToken = null;
	}

	function uploadFile($pathToFile, $newName = null)
	{
		
	}

	/**
	 * 获取上传凭证
	 * @return String 上传凭证
	 */
	private function _getUploadToken()
	{
		if(is_null($this->uploadToken))
		{
			$this->uploadToken = $this->auth->uploadToken($this->bucket);
		}
		return $this->uploadToken;
	}

	function __destruct()
	{
		unset($this->auth);
	}
}