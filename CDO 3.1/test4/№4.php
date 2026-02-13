<?php
$files = ['1.txt', '2.txt', '3.txt'];

foreach ($files as $file) {
    file_put_contents($file, file_get_contents($file) . '!');
}
?>