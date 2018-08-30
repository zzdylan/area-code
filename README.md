安装：composer require zzdylan/area-code  
使用  
getJson.php  
```
<?php
require './vendor/autoload.php';  
$code = new \ZzDylan\AreaCode\Code();  
$code->save();
```
命令行运行  
php getJson.php
默认会将json保存在当前路径