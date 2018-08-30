安装：composer require zzdylan/area-code  
使用  
<?php
require './vendor/autoload.php';  
$code = new \ZzDylan\AreaCode\Code();  
$code->save();  
默认会将json保存在当前路径