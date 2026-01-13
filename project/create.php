<?php require_once ('huder.php'); ?>
    <form action="query.php" method="post">
    <input type="hidden" name="create">    
            <label for="IDfirst_name">enter first name</label>
        <input type="text" name="first_name" id="IDfirst_name">
            <label for="IDname">enter name</label>
        <input type="text" name="name" id="IDname">
            <label for="IDlast_name">enter last name</label>
        <input type="text" name="last_name" id="IDlast_name">
                <label for="IDemail">enter email</label>
        <input type="email" name="email" id="IDemail">
            <label for="IDphone">enter phone</label>
        <input type="tel" name="phone" id="IDphone">
            <label for="IDadress">enter adress</label>
        <textarea name="adress" id="IDadress" cols="30" rows="10"></textarea>
<button type="submit">sent</button>

</form>
<?php require_once ('vuter.php'); ?>