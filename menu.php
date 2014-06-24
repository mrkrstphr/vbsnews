<?php
    if(isset($CONFIG['user'])) {
        echo '<a href="' . $CONFIG['scriptdir'] . 'index.php?action=submit">Submit News</a> | 
        <a href="' . $CONFIG['scriptdir'] . 'index.php?action=modify">Modify News</a> | 
        <a href="' . $CONFIG['scriptdir'] . 'index.php?action=userinfo">Modify User Information</a> | 
        <a href="' . $CONFIG['scriptdir'] . 'index.php?action=logout">Logout</a>';
    } else {
        echo 'Submit News | Modify News | 
        Modify User Information | 
        Logout';
    }
?>