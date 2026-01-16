<!DOCTYPE html>
<html lang="ru">
<head> 
<body>
    <form action="i.php" method="get">
        <input type="text" name="вСелоМолочность" id="">
        <button type="submit">goyda</button>
    </form>
</body>
</html>

<?php
    if (isset($_GET['вСелоМолочность'])){
        $massiv_slov = explode(' ', $_GET['вСелоМолочность']);
        chelofek($massiv_slov);
        echo implode(' ', $massiv_slov);
    }
    
    function chelofek(&$massiv_slov){
        for($i = 0; $i < count($massiv_slov); $i++){
            if(($i % 2) > 0 ){
                $massiv_slov[$i] = strtoupper ($massiv_slov[$i]);
            }
        }
    }

    ?>