<?php
namespace Caichuanhai;

interface DirverInterface
{

	/**
	 * 设置bucket
	 * @param string $bucket
	 */
	public function setBucket($bucket);

	/**
	 * 上传文件
	 * @param  string $pathToFile 要上传文件全路径，包括文件名
	 * @param  string $newName    新文件名，包含后缀，若为空，则用旧文件名
	 * @return mixed [bool, msg] 上传成功，则msg为上传后的文件名，若失败，msg为错误消息
	 */
	public function uploadFile($pathToFile, $newName = '');

	/**
	 * 删除文件
	 * @param  string $fileName 要删除的文件名
	 * @param  string $bucket   bucket，若为空，则用默认bucket
	 * @return mixed [bool, msg] 删除状态，若失败，msg为错误消息
	 */
	public function deleteFile($fileName, $bucket = '');

	/**
	 * 批量删除文件
	 * @param  array $fileName 要删除的文件名数组
	 * @param  string $bucket   bucket，若为空，则用默认bucket
	 * @return mixed [bool, msg] 删除状态，若失败，msg为错误消息
	 */
	public function batchDeleteFile($fileName, $bucket = '');

	/**
	 * 移动文件，可重命名，可在相同或不同bucket中移动
	 * @param  string $old [oldBucket]:oldName 若oldBucket为空，则用默认bucket
	 * @param  string $new [newBucket]:[newName] 若newBucket为空，则用默认bucket，若newName为空，则用旧文件名
	 * @param  bool $retain 是否保留旧文件，若保留，则为复制
	 * @return mixed [bool, msg] 移动状态，若失败，msg为错误消息
	 */
	public function moveFile($old, $new, $retain = false);
}