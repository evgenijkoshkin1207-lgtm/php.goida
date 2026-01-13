<?php require_once ("huder.php"); ?>
<?php require_once ("db.php"); ?>
<?php 
    $sql = "SELECT * FROM privat";
    $result = mysqli_query($mysqli, $sql);
    if(mysqli_errno($mysqli)) echo mysqli_error();

?>
            <table>
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Фамилия</th>
                        <th>Имя</th>
                        <th>Отчество</th>
                        <th>Телефон</th>
                        <th>Email</th>
                        <th>Адрес</th>
                    </tr>
                <?php while ($row = mysqli_fetch_assoc($result)):?>
                    <tr>
                        <th>№</th>
                        <th><?php echo $row['first_name'];?></th>
                        <th><?php echo $row['name'];?></th>
                        <th><?php echo $row['last_name'];?></th>
                        <th><?php echo $row['phone'];?></th>
                        <th><?php echo $row['email'];?></th>
                        <th><?php echo $row['adres'];?></th>
                    </tr>
                <?php endwhile;?>
                </thead>
            </table>
<?php require_once ("vuter.php"); ?>