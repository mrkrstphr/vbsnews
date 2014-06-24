<?php

    ////////////////////////////////////////////////////////////////////////////
    // DESCRIPTION:
    // config.php - loads the configuration variables into the $CONFIG array
    // used in every file to access configuration information
    ////////////////////////////////////////////////////////////////////////////
    
    require 'dbconfig.php';

    ////////////////////////////////////////////////////////////////////////////
    // WARNING:
    // It is not recommended to modify anything below this line!
    ////////////////////////////////////////////////////////////////////////////

    // File detection:
    $thisfile = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') + 1);
    if ($thisfile != 'install.php') {
        // Setup all configuration variables:
        $configs = mysql_query("SELECT * FROM " . $CONFIG['tblPrefix'] . "config");
        if ($config = mysql_fetch_array($configs)) {
            do {
                $CONFIG[$config['name']] = $config['value'];
            } while ($config = mysql_fetch_array($configs));
        }

        // If user is logged in, setup $CONFIG information:
        if (isset($_COOKIE['cvbsNews_usn'])) {
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE usn='" . $_COOKIE['cvbsNews_usn'] . "'";
            $users = mysql_query($sql);

            $var = 'http://' . $_SERVER['SERVER_NAME'] . substr($_SERVER['SCRIPT_NAME'], 0, 
                strrpos($_SERVER['SCRIPT_NAME'], '/') + 1);

            if ($user = mysql_fetch_array($users)) {
                if ($user['password'] == $_COOKIE['cvbsNews_upass']) {
                    
                    // Setup the config variables:
                    $CONFIG['user'] = $user['usn'];
                    $CONFIG['up'] = $user['up'];
                    $CONFIG['uID'] = $user['id'];
                    $CONFIG['cats'] = $user['cat_restrictions'];

                    if ((isset($skip_cookies) === false)) {
                        // Update user cookies to keep logged in:
                        setcookie('cvbsNews_usn', $user['usn'], time() + 7*24*60*60, '/');
                        setcookie('cvbsNews_upass', $user['password'], time() + 7*24*60*60, '/');
                    }
                } else {
                    if ((isset($skip_cookies) === false)) {
                        // Bad information, clear cookies:
                        setcookie('cvbsNews_usn', '', time() - 7*24*60*60, '/');
                        setcookie('cvbsNews_upass', '', time() - 7*24*60*60, '/');
                    }
                }
            }
        }
    }

    // Store current vbsNews version:
    $CONFIG['version'] = '0.62';

    ////////////////////////////////////////////////////////////////////////////
    // DO_ERROR - Global error function used in all scripts:
    ////////////////////////////////////////////////////////////////////////////
    function do_error($msg, $mysql = 0) {
        $error_text = '<b>Error</b>: <i>' . $msg . '</i>';
        if ($mysql == 1) {
            $error_text .= ' (MySQL Error)';
        }
        $error_text .= '<br /><br />
            If you do not understand why you are receiving this error, please check the help files and FAQ, or contact 
            <a href="http://www.vbshelf.com/" target="_blank">vbShelf</a> for further assistance.';

        do_message('An Error Has Occured', $error_text);        
    }

    ////////////////////////////////////////////////////////////////////////////
    // DO_MESSAGE - Global message function used in all scripts:
    ////////////////////////////////////////////////////////////////////////////
    function do_message($msg_title, $msg) {
        global $CONFIG;

        if (isset($CONFIG['sitename'])) {
            $title = $CONFIG['sitename'] . ' - ' . $msg_title;
        } else {
            $title = 'vbsNews - ' . $msg_title;
        }

        include_once 'header.html';

        echo '<table border="0" cellpadding="2" cellspacing="0" style="width: 100%;">
                <tr>
                    <td class="bars">
                        <b>' . $msg_title . '</b>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left;">' . $msg . '</td>
                </tr>
        </table>';

        include_once 'footer.html';
    }

?>