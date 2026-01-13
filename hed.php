<?php
    $url = "https://wikipedia.ru";
    echo "<textarea>";
        print_r(get_headers($url));
    echo "</textarea>";