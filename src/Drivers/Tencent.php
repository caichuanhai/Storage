<?php
namespace Caichuanhai\Drivers;

use \Qcloud\Cos\Client;
use \Caichuanhai\DirverInterface;

/**
 * 腾讯云存储类
 */
class Tencent implements DirverInterface
{

	private $cosClient = null;
	private $bucket = null;
	private $region = null;

	function __construct($option = array('si' => '', 'sk' => '', 'region' => ''))
	{
		$this->cosClient = new Client(array('region' => $option['region'], 'credentials'=> array('secretId'  => $option['si'] , 'secretKey' => $option['sk'])));
		$this->region = $option['region'];
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
			$result = $this->cosClient->Upload($this->bucket, $newName, fopen($pathToFile, 'rb'));
			return [true, $newName];
		}
		catch (\Exception $e)
		{
			return [false, $e->getMessage()];
		}
	}

	function deleteFile($fileName, $bucket = '')
	{
		if(empty($bucket)) $bucket = $this->bucket;

		try
		{
			$result = $this->cosClient->deleteObject(array('Bucket' => $bucket, 'Key' => $fileName));
			return [true, ''];
		}
		catch (\Exception $e)
		{
			return [false, $e->getMessage()];
		}
	}

	function batchDeleteFile($fileName, $bucket = '')
	{
		if(empty($bucket)) $bucket = $this->bucket;

		try
		{
			$Objects = [];
			foreach($fileName as $v)
			{
				$Objects[] = array('Key' => $v);
			}
			$result = $this->cosClient->deleteObjects(array('Bucket' => $bucket, 'Objects' => $Objects));
			return [true, ''];
		}
		catch (\Exception $e)
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
			$result = $this->cosClient->Copy($newBucket, $newName, array('Region' => '', 'Bucket' => $oldBucket, 'Key' => $oldName));

			if(!$retain) return $this->deleteFile($oldName, $oldBucket);

			return [true, ''];
		}
		catch (\Exception $e)
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