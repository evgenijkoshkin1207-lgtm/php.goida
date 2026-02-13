<?php
$file = 'count.txt';
$count = (int)file_get_contents($file);
$count++;
file_put_contents($file, $count);
echo "Счетчик: " . $count;
?>