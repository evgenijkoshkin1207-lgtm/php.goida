<!DOCTYPE html>
<html lang="ru">
<head> 
<body>
    <form action="led.php" method="get">
        <input type="text" name="СелоМолочное" id="">
        <button type="submit">goyda</button>
    </form>
</body>
</html>

<?php
    $XVI="Иван Васильевич";
    $XVIII="Пётр Алексеевич";
    $XIX="Николай Павлович";
            if(isset($_GET['СелоМолочное'])){
                $led = $_GET['СелоМолочное'];
                echo 'в '. $led .' веке царствовал '. $$led;
            }
    ?>



