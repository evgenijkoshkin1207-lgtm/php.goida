<?php
    if(isset($_POST{'create'})){
        $sql = "INSERT INTO `privat`(`id`, `first_name`, `name`, `last_name`, `phone`, `email`, `adres`, `user_id`, `created_at`)
        VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]','[value-6]','[value-7]','[value-8]','[value-9]')"
    }