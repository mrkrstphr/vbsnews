<?php

    //////////////////////////////////////////////////////////////////////////
    // DESCRIPTION:
    // commlogin.php - Handle comments user login/logout
    ////////////////////////////////////////////////////////////////////////////
    // WARNING:
    // It is not recommended to modify anything below this line!
    ////////////////////////////////////////////////////////////////////////////
    // Include the configuration file:
    include 'config.php';
    // Decide what to do:
    if ($_REQUEST['action'] == 'logout') {
        logout();
    } else {
        login();
    }

    ////////////////////////////////////////////////////////////////////////////
    // LOGOUT - Handle user logout:
    ////////////////////////////////////////////////////////////////////////////
    function logout() {
        setcookie('cvbsNews_usn', '', time() - 7*24*60*60, '/');
        setcookie('cvbsNews_upass', '', time() - 7*24*60*60, '/');

        if ($_SERVER['HTTP_REFERER'] != '') {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            echo '<script type="text/javascript">history.back();</script>';
        }

        //NOTE: Some browsers allow HTTP_REFERER to be manually set. If a user keeps getting sent
        //to a weird webpage after logging out, they need to correct this setting in their browser
    }

    ////////////////////////////////////////////////////////////////////////////
    // LOGIN - Handle user login:
    ////////////////////////////////////////////////////////////////////////////
    function login() {
        global $CONFIG;

        if ($CONFIG['allowcomments'] == 'yes') {
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE usn='" . $_POST['c_name'] . "' ";
            $sql .= "AND password='" . md5($_POST['c_password']) . "'";
            $users = mysql_query($sql) or die(mysql_error());

            if ($user = mysql_fetch_array($users)) {
                if ($user['up'] == 5) {
                    echo 'You have been banned from using this comment system. If you believe this is a mistake, please
                        contact the <a href="mailto:' . $CONFIG['adminmail'] . '">administrator</a>.';

                    exit;
                }

                // Set cookies:
                setcookie('cvbsNews_usn', $_POST['c_name'], time() + 7*24*60*60, '/');
                setcookie('cvbsNews_upass', md5($_POST['c_password']), time() + 7*24*60*60, '/');

                header('Location: ' . $_POST['referto']);
            } else {
                die(displayerror('Invalid information. Please go back.'));
            }
        } else {
            die(displayerror('Commenting has been disabled on this site.'));
        }
    
    }

    ////////////////////////////////////////////////////////////////////////////
    // DISPLAYERROR - Show an error message
    ////////////////////////////////////////////////////////////////////////////
    function displayerror($error) {
        echo "<b>Error</b>: $error";
    }
?>