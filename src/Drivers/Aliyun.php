<?php
namespace caichuanhai\Drivers;

use \OSS\OssClient;
use \OSS\Core\OssException;
use \caichuanhai\DirverInterface;

/**
 * 阿里云存储类
 */
class Aliyun implements DirverInterface
{

	private $ossClient = null;
	private $bucket = null;

	function __construct($option = array('ak' => '', 'sk' => '', 'ep' => ''))
	{
		$this->ossClient = new OssClient($option['ak'], $option['sk'], $option['ep']);
	}

	function setBucket($bucket)
	{
		if($bucket == $this->bucket) return true;

		$this->bucket = $bucket;
	}

	function uploadFile($pathToFile, $newName = null)
	{
		list($status, $newName) = $this->_getNewFileName($pathToFile, $newName);
		if(!$status) return [false, $newName];

		try
		{
			$this->ossClient->uploadFile($this->bucket, $newName, $pathToFile);
			return [true, $newName];
		}
		catch(OssException $e)
		{
			return [false, $e->getMessage()];
		}
	}

	function deleteFile($fileName, $bucket = '')
	{
		if(empty($bucket)) $bucket = $this->bucket;

		try
		{
			$this->ossClient->deleteObject($bucket, $fileName);
			return [true, ''];
		}
		catch(OssException $e)
		{
			return [false, $e->getMessage()];
		}
	}

	function batchDeleteFile($fileName, $bucket = '')
	{
		if(empty($bucket)) $bucket = $this->bucket;

		try
		{
			$this->ossClient->deleteObjects($bucket, $fileName);
			return [true, ''];
		}
		catch(OssException $e)
		{
			return [false, $e->getMessage()];
		}
	}

	function moveFile($old, $new, $retain = false)
	{
		list($oldBucket, $oldName) = explode(':', $old);
		list($newBucket, $newName) = explode(':', $new);

		if(empty($oldBucket)) $oldBucket = $this->bucket;
		if(empty($newBucket)) $newBucket = $oldBucket;
		if(empty($newName)) $newName = $oldName;

		try
		{
			$this->ossClient->copyObject($oldBucket, $oldName, $newBucket, $newName);

			if(!$retain) return $this->deleteFile($oldName, $oldBucket);

			return [true, ''];
		}
		catch(OssException $e)
		{
			return [false, $e->getMessage()];
		}
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

}