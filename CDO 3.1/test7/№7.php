<?php
$file = 'test.txt';
$text = file_get_contents($file);
$text .= '!';
file_put_contents($file, $text);
echo "Текст обновлен: " . $text;
?>