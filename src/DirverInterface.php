<?php
namespace caichuanhai;

interface DirverInterface
{

	public setBucket($bucket);

	public uploadFile($pathToFile, $newName = '');
}