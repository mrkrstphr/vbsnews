<?php

    ////////////////////////////////////////////////////////////////////////////
    // DESCRIPTION:
    // login.php - login/logout functiosn for members
    ////////////////////////////////////////////////////////////////////////////
    // WARNING:
    // It is not recommended to modify anything below this line!
    ////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////
    // SHOWLOGIN - Show the login form:
    ////////////////////////////////////////////////////////////////////////////
    function showlogin($message = '') {
        global $CONFIG;
        
        $title = $CONFIG['sitename'] . ' - vbsNews Login';
        include 'header.html';
        echo '<div align="center">
            <form action="' . $CONFIG['scriptdir'] . 'index.php" method="post">
            <input type="hidden" name="action" value="login">
            <table align="center" cellpadding="2" cellspacing="0" style="width: 325px;">
                <tr>
                    <td colspan="2" class="bars" style="border: 1px solid #000000;"><b>User Login</b></td>
                </tr>
                <tr>
                    <td colspan="2">
						This program requires login before it can be used. Please enter your login information below.';
       
        if ($message != '') {
            echo '<br><br><b>' . $message . '</b>';
        }

		echo '<br><br></td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>User</b>:</td>
                    <td style="width: 70%;"><input type="text" name="usern" maxlength="16" style="width: 100%;" /></td>
                </tr>
                <tr>
                    <td><b>Password</b>:</td>
                    <td><input type="password" name="userp" maxlength="16" style="width: 100%;" /></td>
                </tr>
                <tr>
                    <td align="center" colspan="2"><input type="submit" value=" Login "><br /><br />
                    [ <a href="' . $CONFIG['scriptdir'] . 'index.php?action=forgotpass">Lost Password?</a> ]</td>
                </tr>
            </table>
            </form>
        </div>';
        
        include 'footer.html';
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // LOGIN - Log the user in:
    ////////////////////////////////////////////////////////////////////////////
    function login() {
        global $CONFIG;
         
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE usn='" . $_POST['usern'] . "'";
        $users = mysql_query($sql)  or die(mysql_error());
        
        if ($user = mysql_fetch_array($users)) {
            if ($user['password'] == md5($_POST['userp'])) {
                setcookie('cvbsNews_usn', $user['usn'], time() + 7*24*60*60, '/');
                setcookie('cvbsNews_upass', $user['password'], time() + 7*24*60*60, '/');

                header("Location: " . $CONFIG['scriptdir'] . "index.php");
            } else {
                showlogin("Invalid password!");
            }            
        } else {
            showlogin("Invalid username!");
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // LOGOUT - Log the user out:
    ////////////////////////////////////////////////////////////////////////////
    function logout() {
        setcookie('cvbsNews_usn', '', time() - 7*24*60*60, '/');
        setcookie('cvbsNews_upass', '', time() - 7*24*60*60, '/');
        
        showlogin("You have been logged out.");
    }

    ////////////////////////////////////////////////////////////////////////////
    // FORGOTPASS - Main password reset function:
    ////////////////////////////////////////////////////////////////////////////
    function forgotpass() {
        global $CONFIG;
        
        $title = $CONFIG['sitename'] . ' - vbsNews Password Reset';
        include 'header.html';

        if ($_REQUEST['s'] == 2) {
            create_ID();
        } elseif ($_REQUEST['s'] == 3) {
            reset_pass();
        } else {
            pass_form();
        }
        
        include 'footer.html';
    }

    ////////////////////////////////////////////////////////////////////////////
    // PASS_FORM - Show forgotten password form:
    ////////////////////////////////////////////////////////////////////////////
    function pass_form() {
        echo '<form action="index.php" method="post">
        <input type="hidden" name="action" value="forgotpass">
        <input type="hidden" name="s" value="2">
        <table align="center" cellpadding="2" cellspacing="0" style="width: 300px;">
            <tr>
                <td class="header2" colspan="2" style="border-bottom: 1px solid black;"><b>Password Reset</b></td>
            </tr>
            <tr>
                <td colspan="2">
                    The following form is a request to reset the password for your account. A verification email will
                    be sent to you with more instructions on how to reset your password.<br><br>
                </td>
            </tr>
            <tr>
                <td style="width: 25px;">
                    <b>Email</b>:
                </td>
                <td style="width: 275px;">
                    <input type="text" name="email" maxlength="50" style="width: 100%;">
                </td>
            </tr>
            <tr>
                <td align="center" colspan="2"><input type="submit" value=" Submit "></td>
            </tr>
        </table>';
    }

    ////////////////////////////////////////////////////////////////////////////
    // create_ID - create a reset ID:
    ////////////////////////////////////////////////////////////////////////////
    function create_ID() {
        global $CONFIG;

        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE email='" . $_POST['email'] . "'";
        $users = mysql_query($sql) or die(do_error('MySQL ERROR - ' . mysql_error()));

        if ($user = mysql_fetch_array($users)) {
            // Create ID:
            for ($i = 0; $i < 24; $i++) {
                $r_ID .= rand(0, 9);
            }
            $r_ID = substr(md5($r_ID), 0, 8);

            // Update database with ID:
            $sql = "UPDATE " . $CONFIG['tblPrefix'] . "users SET pass_reset='$r_ID' WHERE id='" . $user['id'] . "'";
            $result = mysql_query($sql) or die(do_error('MySQL ERROR - ' . mysql_error()));

            if ($result) {
                // Send email:
                $message = "You (or somebody else) has requested that your password be reset at " . $CONFIG['sitename'] . ".\n\n";
                $message .= "If you wish to have your password reset, follow the link below:\n\n";
                $message .= $CONFIG['scriptdir'] . 'index.php?action=forgotpass&s=3&email=';
                $message .= $user['email'] . '&id=' . $r_ID;
    
                mail($user['email'], $CONFIG['sitename'] . ' Password Reset', $message, 'From: vbsNews <' . $CONFIG['adminmail']) . '>';

                echo 'A confirmation email has been sent to you at <b>' . $_POST['email'] . '</b>. Inside this email
                    is further instructions on how to reset your password.';
            } else {
                echo '<b>Error</b>: Failed to setup password reset.';
            }
        } else {
            echo '<b>Error</b>: Invalid email specified.';
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // RESET_PASS - Reset the password:
    ////////////////////////////////////////////////////////////////////////////
    function reset_pass() {
        global $CONFIG;

        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE email='" . $_REQUEST['email'] . "' AND pass_reset='" . $_REQUEST['id'] . "'";
        $users = mysql_query($sql) or die(do_error('MySQL ERROR - ' . mysql_error()));

        if ($user = mysql_fetch_array($users)) {
            // Create new password:
            for ($i = 0; $i < 24; $i++) {
                $r_ID .= rand(0, 9);
            }
            $r_ID = substr(md5($r_ID), 0, 9);

            $sql = "UPDATE " . $CONFIG['tblPrefix'] . "users SET password='" . md5($r_ID) . "', pass_reset='' WHERE id=" . $user['id'];
            $result = mysql_query($sql) or die(do_error('MySQL ERROR - ' . mysql_error()));

            if ($result) {
                // Send email:
                $message = "Your password has been reset at " . $CONFIG['sitename'] . ".\n";
                $message .= "Your user name is: " . $user['usn'] . "\n";
                $message .= "Your new password is: $r_ID\n\n";
                $message .= "You can change this at any time.";

                mail($user['email'], $CONFIG['sitename'] . ' Password Reset', $message, 'From: vbsNews <' . $CONFIG['adminmail']) . '>';

                echo 'Your password has been reset and the new one has been emailed to you at <b>' . $user['email'] . '</b>.
                    You can change this new password at any time.';
            } else {
                echo '<b>Error</b>: Failed to create a new password.';
            }
        } else {
            echo '<b>Error</b>: Invalid information specified.';
        }
    }

?>