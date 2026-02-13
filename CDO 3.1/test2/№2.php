<?php
$file = 'test.txt';
$text = '12345';

if (file_put_contents($file, $text) !== false) {
    echo "Текст успешно записан в файл.";
} else {
    echo "Ошибка записи в файл.";
}
?>