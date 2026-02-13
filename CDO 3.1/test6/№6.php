<?php
$file = 'test';
$number = (int)file_get_contents($file);
$number = $number * $number;
file_put_contents($file, $number);
echo "Число возведено в квадрат. Результат: " . $number;
?>