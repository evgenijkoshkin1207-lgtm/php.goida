<?php
$arr=['1.txt', '2.txt', '3.txt'];
$str='';
foreach($arr as $file){
   $str .=file_get_contents($file);
}
file_put_contents('new.txt', $str);
?>