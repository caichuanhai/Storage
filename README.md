# storage

## 关于

第三方存储库，将阿里云，七牛云，腾讯云的对象存储功能进一步封装成统一接口，可随意切换，不用修改业务代码，暂支持上传文件，删除文件，批量删除，移动文件，复制文件。后期将加入更多文件管理和bucket管理功能。


## 安装
1. composer安装
```shell
composer require caichuanhai/router
```
2. 普通安装
下载源码压缩包：
[https://github.com/caichuanhai/Storage](https://github.com/caichuanhai/Storage)
解压到您项目的目录中，然后在您的项目中引入 autoloader：
```php
require 'path_to_sdk/autoload.php'
```


## 初始化

```php
use \Caichuanhai\Storage;

// $storage = new Storage('qiniu', array('ak' => 'Access_Key', 'sk' => 'Secret_Key'));
// $storage = new Storage('aliyun', array('ak' => 'yourAccessKeyId', 'sk' => 'yourAccessKeySecret', 'ep' => 'endpoint'));
// $storage = new Storage('tencent', array('si' => 'COS_SECRETID', 'sk' => 'COS_SECRETKEY', 'region' => 'COS_REGION'));

$storage->setBucket('yourBucket');
```

目前在初始始化时可指定qiniu，aliyun，tencent中任意一个，根据所选产品不同，后面所传数组参数也不相同，具体参数数值可在对应产品后台获取。

`setBucket`方法为设置要操作的bucket，在操作文件中途也可再次调用该方法以切换不同bucket。


## 上传文件

```php
$storage->uploadFile(pathToFile, newName)
```

| 参数名 | 必填 | 默认值 | 说明 |
:-: | :-: | :-: | :-:
| pathToFile | 是 | 无 | 要上传文件全路径，包括文件名 |
| newName | 否 | 无 | 新文件名，包含后缀，若为空，则用旧文件名 |

返回值：
[bool, msg] 上传成功，则msg为上传后的文件名，若失败，msg为错误消息


## 删除文件

```php
$storage->deleteFile(fileName, Bucket)
```

| 参数名 | 必填 | 默认值 | 说明 |
:-: | :-: | :-: | :-:
| fileName | 是 | 无 | 要删除的文件名 |
| Bucket | 否 | 无 | bucket，若为空，则用`setBucket`方法所设置的bucket |

返回值：
[bool, msg] 删除状态，若失败，msg为错误消息


## 批量删除文件

```php
$storage->batchDeleteFile(fileNames, Bucket)
```

| 参数名 | 必填 | 默认值 | 说明 |
:-: | :-: | :-: | :-:
| fileNames | 是 | 无 | 要删除的文件名数组，以文件名组成的一维数组 |
| Bucket | 否 | 无 | bucket，若为空，则用`setBucket`方法所设置的bucket |

返回值：
[bool, msg] 删除状态，若失败，msg为错误消息



## 移动复制文件

```php
$storage->moveFile(oldObject, newObject, retain)
```

| 参数名 | 必填 | 默认值 | 说明 |
:-: | :-: | :-: | :-:
| oldObject | 是 | 无 | 源文件，以bucket和文件名组成，以:分隔，[oldBucket]:oldName 若oldBucket为空，则用`setBucket`方法所设置的bucket |
| newObject | 是 | 无 | 移动或复制后的文件，以bucket和文件名组成，以:分隔，[newBucket]:[newName] 若newBucket为空，则用`setBucket`方法所设置的bucket，若newName为空，则用旧文件名 |
| retain | 否 | false | 是否保留旧文件，默认false不保留，等同于移动，若保留，则为复制 |

返回值：
[bool, msg] 移动状态，若失败，msg为错误消息