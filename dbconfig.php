<?php

    ////////////////////////////////////////////////////////////////////////////
    // DESCRIPTION:
    // dbconfig.php - stores the database information for script access
    ////////////////////////////////////////////////////////////////////////////
    // IMPORTANT:
    // In order for the scripts to run properly, it is required that the 
    // following database variables be set:
    //
    $CONFIG['dbuser'] = '';
    $CONFIG['dbpassword'] = '';
    $CONFIG['db'] = '';
    $CONFIG['tblPrefix'] = 'vbsNews_'; // default value
    $CONFIG['dbhost'] = 'localhost'; // default value
    //
    // If you are unsure what these values should be, contact your system 
    // administrator or webhosting provider.
    ////////////////////////////////////////////////////////////////////////////
    // WARNING:
    // It is not recommended to modify anything below this line!
    ////////////////////////////////////////////////////////////////////////////

    // Connect to database:
    $db = @mysql_connect($CONFIG['dbhost'], $CONFIG['dbuser'], $CONFIG['dbpassword']) or die('An error has occurred: Could not connect to the MySQL server.');
    mysql_select_db($CONFIG['db']) or die('An error has occured: Could not connect to the database.');

?>