<?php
namespace Caichuanhai\Drivers;

use \Qiniu\Auth;
use \Qiniu\Config;
use \Qiniu\Storage\UploadManager;
use \Qiniu\Storage\BucketManager;
use \Caichuanhai\DirverInterface;

/**
 * 七牛存储类
 */
class Qiniu implements DirverInterface
{

	private $uploadManager = null;
	private $auth = null;
	private $bucket = null;
	private $uploadToken = null;
	private $config = null;
	private $bucketManager = null;

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
		list($status, $newName) = $this->_getNewFileName($pathToFile, $newName);

		if(!$status) return [false, $newName];

		$uploadToken = $this->_getUploadToken();
		$uploader = $this->_getUploadManager();

		list($result, $error) = $uploader->putFile($uploadToken, $newName, $pathToFile);

		if ($error !== null) return [false, $error];
		return [true, $result['key']];
	}

	function deleteFile($fileName, $bucket = '')
	{
		if(empty($bucket)) $bucket = $this->bucket;

		$bucketManager = $this->_getBucketManager();

		$error = $bucketManager->delete($bucket, $fileName);
		if($error) return [false, $error->message()];
		return [true, ''];
	}

	function batchDeleteFile($fileName, $bucket = '')
	{
		if(count($fileName) > 1000) return [false, '同时删除不能超过1000个'];

		if(empty($bucket)) $bucket = $this->bucket;

		$bucketManager = $this->_getBucketManager();

		$ops = $bucketManager->buildBatchDelete($bucket, $fileName);
		list($result, $error) = $bucketManager->batch($ops);
		if($error) return [false, $error];
		return [true, $result];
	}

	function moveFile($old, $new, $retain = false)
	{
		list($oldBucket, $oldName) = explode(':', $old);
		list($newBucket, $newName) = explode(':', $new);

		if(empty($oldBucket)) $oldBucket = $this->bucket;
		if(empty($newBucket)) $newBucket = $oldBucket;
		if(empty($newName)) $newName = $oldName;

		$bucketManager = $this->_getBucketManager();

		$method = 'move';
		if($retain) $method = 'copy';
		$error = $bucketManager->$method($oldBucket, $oldName, $newBucket, $newName, true);

		if($error) return [false, $error];
		return [true, ''];
	}

	/**
	 * 获取上传之后的文件名，同时检查要上传的文件是否有效
	 * @param  string $pathToFile 要上传的文件的全路径，包含文件名
	 * @param  string $newName    新文件名，包含后缀
	 * @return mixed [bool, msg]， 若为真，msg为新文件名，若为假，则为错误消息
	 */
	private function _getNewFileName($pathToFile, $newName)
	{
		if(is_dir($pathToFile) OR !file_exists($pathToFile)) return [false, '请上传有效的文件'];

		if(empty($newName)) $newName = basename($pathToFile);

		return [true, $newName];
	}

	private function _getUploadManager()
	{
		if(is_null($this->uploadManager)) $this->uploadManager = new UploadManager();

		return $this->uploadManager;
	}

	private function _getConfig()
	{
		if(is_null($this->config)) $this->config = new Config();

		return $this->config;
	}

	private function _getBucketManager()
	{
		if(is_null($this->bucketManager)) $this->bucketManager = new BucketManager($this->auth, $this->_getConfig());

		return $this->bucketManager;
	}

	/**
	 * 获取上传凭证
	 * @return String 上传凭证
	 */
	private function _getUploadToken()
	{
		if(is_null($this->uploadToken)) $this->uploadToken = $this->auth->uploadToken($this->bucket);

		return $this->uploadToken;
	}

}