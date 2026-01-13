<?php

    // print_r($_POST)
    $tema=$_POST['flexRadioDefault'];
    $text=$_POST['text'];
    $email=$_POST['email'];
    echo "спасибо за ваше обращение с темой $tema и текстом $text.ответ отправлен на почту $email";