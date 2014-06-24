<?php

    ////////////////////////////////////////////////////////////////////////////
    // DESCRIPTION:
    // users.php - Handles all user functions
    ////////////////////////////////////////////////////////////////////////////
    // WARNING:
    // It is not recommended to modify anything below this line!
    ////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////
    // MANAGEMAIN - Main manage user function, figure out what to do:
    ////////////////////////////////////////////////////////////////////////////
    function managemain() {
        if (isset($_POST['s'])) {
            switch($_POST['s']) {          
                case 'add':
                    adduser(); break;
                case 'removecon':
                    removeusercon(); break;
                case 'remove':
                    removeuser(); break;
                case 'showupdate':
                    update(); break;
                case 'updateuser':
                    updateuser(); break;

                default:
                    manageusers();
            }
        } else {
            manageusers();
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // USERSMAIN - Main user info function, figure out what to do:
    ////////////////////////////////////////////////////////////////////////////
    function usersmain() {
        if (isset($_REQUEST['s']) && $_REQUEST['s'] == 'save') {
            saveuserinfo();
        } else {
            modifyuserinfo();
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // MANAGEUSERS - Manage user form:
    ////////////////////////////////////////////////////////////////////////////
    function manageusers() {
        global $CONFIG;
        
        // Show page:
        if ($CONFIG['up'] == 1) {
            $title = $CONFIG['sitename'] . ' - Manage Users';
            include 'header.html';

            // Create user select box (used twice later):
            $users = mysql_query("SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE up < 4");  
            if ($user = mysql_fetch_array($users)) {
                $user_sb = '<select name="user" style="width: 100%;" class="formfield">';

                do {
                    $user_sb .= '<option value="' . $user['usn'] . '">' . $user['usn'] . '</option>';
                } while ($user = mysql_fetch_array($users));

                $user_sb .= '</select>';

            } else {    
                $user_sb = 'There are no users configured.';
            }

    	    echo '<form method="post" action="' . $CONFIG['scriptdir'] . 'index.php" style="margin: 0px;">
            <input type="hidden" name="action" value="manageusers" />
            <input type="hidden" name="s" value="" />
    	    <table cellpadding="2" cellspacing="0" style="margin-left: auto; margin-right: auto; width: 555px;">
                <tr>
                    <td class="bars" colspan="2">
                        <b>Add User</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        Enter the information for the new user below. After the user is added, a random password will
                        be generated and emailed to them. The password may be changed at any time.<br /><br />
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>User Name</b>:</td>
                    <td style="width: 70%;">
                        <input type="text" name="username" maxlength="16" class="formfield" style="width: 100%;" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>E-mail Address</b>:</td>
                    <td style="width: 70%;">
                        <input type="text" name="email" maxlength="50" class="formfield" style="width: 100%;" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>User Permissions</b>:</td>
                    <td style="width: 70%;">
                        <select name="up" class="formfield" style="width: 100%;">
                            <option value="3">Normal - Can edit own posts only</option>
                            <option value="2">Moderator - Can edit all posts</option>
                            <option value="1">Administrator - Can edit all posts and settings</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" value=" Add User " onclick="this.form.s.value=\'add\'" />
                        <br /><br />
                    </td>
                </tr>

                <tr>
                    <td class="bars" colspan="2">
                        <b>Remove</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        Removing a user will cancel their ability to access the news program. After deleting a user, you will
                        be given an option to remove all of their news posts as well.<br /><br />
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>User Name</b>:</td>
                    <td style="width: 70%;">' . $user_sb . '</td>
                </tr>
                <tr>
                    <td align="center" colspan="2">
                        <input type="submit" value=" Delete User " onclick="this.form.s.value=\'removecon\'" />
                        <br /><br />
                    </td>
                </tr>

                <tr>
                    <td class="bars" colspan="2">
                        <b>Update User Information</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        Select a user to modify his or her name and/or abilities. This includes
                        user status (moderator, admin, or user), and category permissions.<br /><br />
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>User Name</b>:</td>
                    <td style="width: 70%;">' . str_replace('user', 'user_name', $user_sb) . '</td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" value=" Update User " onclick="this.form.s.value=\'showupdate\'" />
                    </td>
                </tr>
            </table>
            </form>';

            include 'footer.html';
        } else {
            die(do_error('You do not have permission to modify the settings.'));
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // ADDUSER - Add a user to the system:
    ////////////////////////////////////////////////////////////////////////////
    function adduser() {
        global $CONFIG;
       
        if ($CONFIG['up'] == 1) {
            // Error checking:
            if (strlen($_POST['username']) < 3) {
                die(do_error('The user name must be greater than 2 characters.'));
            }

            include_once 'functions/function.validateemail.php';

            // Validate email:
            if (validateemail($_POST['email']) === false) {
                die(do_error('The email address <b>' . $_POST['email'] . '</b> is not valid.'));
            }

            // Make sure name isn't registered:
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users where usn='" . $_POST['username'] . "'";
            $users = mysql_query($sql) or die(do_error(mysql_error(), 1));
            if ($user = mysql_fetch_array($users)) {
                die(do_error('The user name <b>' . $user['usn'] . '</b> is already in use.'));
            }
            
            // Generate random password:
            $u_p = '';
            for ($i = 0; $i < 12; $i++) {
                $u_p .= rand(0, 9);
            }
            $u_p = substr(md5($u_p), 0, 9);

            // Add user:
            $sql = "INSERT INTO " . $CONFIG['tblPrefix'] . "users (usn, password, email, up) VALUES ";
            $sql .= "('" . $_POST['username'] . "', '" . md5($u_p) . "', '";
            $sql .= $_POST['email'] . "', '" . $_POST['up'] . "')";
            
            $result = mysql_query($sql) or die(do_error('MySQL ERROR - ' . mysql_error()));
            if ($result) {
                // Mail user password:
                $message = "An account has been setup for you at " . $CONFIG['sitename'] . "\n\n";
                $message .= "User Name: " . $_POST['username'] . "\n";
                $message .= "Password: " . $u_p . "\n\n";
                $message .= "You may change your password at anytime. To login, go to:\n";
                $message .= $CONFIG['scriptdir'] . "index.php";

                mail($_POST['email'], $CONFIG['sitename'] . ' Account', $message, 
                    'From: ' . $CONFIG['sitename'] . ' <' . $CONFIG['adminmail']) . '>';

                // Create message:
                $msg = 'User <b>' . $_POST['username'] . '</b> created successfully!<br /><br />
                <a href="' . $CONFIG['scriptdir'] . 'index.php">Click here</a> to return to the main page.';
                // Display success message:
                do_message('User Added', $msg);
            } else {
                die(do_error('Failed to create user.'));;
            }
        } else {
            die(do_error('You do not have permission to modify the settings.'));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // REMOVEUSERCON - Confirmation for user removal:
    ////////////////////////////////////////////////////////////////////////////
    function removeusercon() {
        global $CONFIG;
               
        $msg = '<form action="' . $CONFIG['scriptdir'] . 'index.php" method="post" name="form1">
            <input type="hidden" name="action" value="manageusers" />
            <input type="hidden" name="s" value="remove" />
            <input type="hidden" name="user" value="' . $_POST['user'] . '" />
            Are you sure you want to remove user <b>' . $_POST['user'] . '</b>?<br /><br />
            <input type="checkbox" name="dp" value="yes">Delete user\'s posts.<br /><br />

            <div style="text-align: center;">
                <input type="submit" value=" Yes " /> 
                <input type="button" name="no" value=" No " onClick="history.back();" />
            </div>
        </form>';

        do_message('Confirm User Deletion', $msg);
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // REMOVEUSER - Remove a user from the system:
    ////////////////////////////////////////////////////////////////////////////
    function removeuser() {
        global $CONFIG;

        if ($CONFIG['up'] == 1) {
            $msg = '';
            // If we want to delete the user's posts:
            if (isset($_REQUEST['dp'])) {
                // Get user's ID:
                $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE usn='" . $_POST['user'] . "'";
                $users = mysql_query($sql) or die(do_error(mysql_error(), 1));

                if ($user = mysql_fetch_array($users)) {
                    $sql = "DELETE FROM " . $CONFIG['tblPrefix'] . "news WHERE uID='" . $user['id'] . "'";
                    $result = mysql_query($sql);
                    
                    if ($result) {
                        $msg = 'User\'s posts deleted successfully.<br />';
                    } else {
                        die(do_error('Failed to delete user\'s posts.'));
                    }
                }
            }

            // Delete user:               
            $sql = "DELETE FROM " . $CONFIG['tblPrefix'] . "users WHERE usn='" . $_POST['user'] . "'";
            $result = mysql_query($sql);
            
            if ($result) {
                $msg .= 'User deleted successfully.<br />';
            } else {
                die(do_error('Failed to delete user.'));
            }

            $msg .= '<br /><a href="' . $CONFIG['scriptdir'] . 'index.php">Click here</a> to go to the main page.';

            do_message('User Deleted', $msg);
        } else {
            die(do_error('You do not have permission to remove users.'));
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // UPDATE - Update user info:
    ////////////////////////////////////////////////////////////////////////////
    function update() {
        global $CONFIG;

        // Show page:
        if ($CONFIG['up'] == 1) {
            include_once 'functions/function.getdefault.php';

            $title = $CONFIG['sitename'] . ' - Update User Information';
            include 'header.html';

    	    echo '<form method="post" action="' . $CONFIG['scriptdir'] . 'index.php">
            <input type="hidden" name="action" value="manageusers" />
            <input type="hidden" name="s" value="updateuser" />
            <input type="hidden" name="user" value="' . $_POST['user_name'] . '" />
    	    <table cellpadding="2" cellspacing="0" style="margin-left: auto; margin-right: auto; width: 555px;">
                <tr>
                    <td class="bars" colspan="2" style="border: 1px solid #000000;">
                        <b>Update User Information</b>: ' . $_POST['user_name'] . '</td>
                </tr>
                <tr>
                    <td colspan="2">
                        The <i>User Status</i> defines the permissions of the user. They can be Normal, Moderator, or
                        Administrator. <i>News Categories</i> are the categories that this user will be allowed to
                        post in. To make multiple selects, hold down the CTRL key while selecting.<br /><br />
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>User Status</b>: </td>
                    <td style="width: 70%;">';

            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE usn='" . $_POST['user_name'] . "'";
            $users = mysql_query($sql) or die(do_error(mysql_error(), 1));

            $user = mysql_fetch_array($users);

            echo '<select name="u_up" class="formfield" style="width: 100%;">
                            <option value="3"' . getdefault($user['up'], 3, 'select') . '>
                                Normal - Can edit own posts only
                            </option>
                            <option value="2"' . getdefault($user['up'], 2, 'select') . '>
                                Moderator - Can edit all posts
                            </option>
                            <option value="1"' . getdefault($user['up'], 1, 'select') . '>
                                Administrator - Can edit all posts and settings
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%;" valign="top"><b>News Categories</b>: </td>
                    <td style="width: 70%;">
                        <select name="u_cats[]" class="formfield" style="width: 100%;" multiple="multiple">
                            <option value="all"';
                            
                            if ($user['cat_restrictions'] == 'all') {
                                echo ' selected="selected"';
                            }
                            
                            echo '>All Categories</option>';
                            
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "cats";
            $cats = mysql_query($sql) or die(do_error(mysql_error(), 1));

            if ($cat = mysql_fetch_array($cats)) {
                do {

                    echo '<option value="' . $cat['key_name'] . '"';
                    if (array_search($cat['key_name'], explode(', ', $user['cat_restrictions'])) !== false) {
                        echo ' selected="selected"';
                    }
                    
                    echo '>' . $cat['name'] . '</option>';
                } while ($cat = mysql_fetch_array($cats));
            } else {

            }

            echo '</select></td>
                </tr>
                <td colspan="2" style="text-align: center;">
                    <br />
                    <input type="submit" value=" Update " />
                </td>
            </table>';

            include 'footer.html';
        } else {
            die(do_error('You do not have permission to modify user settings.'));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // UPDATEUSER - Save user info:
    ////////////////////////////////////////////////////////////////////////////
    function updateuser() {
        global $CONFIG;

        // Make sure some news categories are selected:
        if (!isset($_POST['u_cats'])) {
            die(do_error('No categories were selected for user'));
        }

        // Build the SQL statement for categroies:
        $sql = "UPDATE " . $CONFIG['tblPrefix'] . "users SET ";
        if ($_POST['u_cats'][0] == 'all') {
            $sql .= "cat_restrictions='all'";
        } else {
            $sql .= "cat_restrictions='";
            for ($i = 0; $i < count($_POST['u_cats']); $i++) {
                $sql .= $_POST['u_cats'][$i];

                if ($i == (count($_POST['u_cats']) - 1)) {
                    $sql .= "'";
                } else {
                    $sql .= ", ";
                }
            }
        }

        // Build the rest of the SQL statement:
        $sql .= ", up='" . $_POST['u_up'] . "' WHERE usn='" . $_POST['user'] . "'";

        $result = mysql_query($sql) or die(do_error(mysql_error(), 1));

        if ($result) {
            $title = $CONFIG['sitename'] . ' - User Information Updated';
            include 'header.html';

            echo 'User information has been updated for <b>' . $_POST['user'] . '</b>.<br /><br />
            <a href="' . $CONFIG['scriptdir'] . 'index.php?action=manageusers">Click here</a> to return to the 
                User Management page.';

            include 'footer.html';
        } else {
            die(do_error('Failed up update user information'));
        }
    }


    ////////////////////////////////////////////////////////////////////////////
    // MODIFYUSERINFO - Show user modify info form:
    ////////////////////////////////////////////////////////////////////////////
    function modifyuserinfo() {
        global $CONFIG;

        $title = $CONFIG['sitename'] . ' - Modify User Information';
        include 'header.html';
        
        $users = mysql_query("SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE usn='" . $CONFIG['user'] . "'") or die(do_error(mysql_error(), 1));

        if ($user = mysql_fetch_array($users)) {
            include_once 'functions/function.getdefault.php';

            echo '<form action="' . $CONFIG['scriptdir'] . 'index.php" method="post">
            <input type="hidden" name="action" value="userinfo" />
            <input type="hidden" name="s" value="save" />
            <table cellpadding="2" cellspacing="0" style="margin-left: auto; margin-right: auto; width: 555px;">
                <tr>
                    <td class="bars" colspan="2">
                        <b>Modify User Information</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        You will receive an email confirming your new password (if changed) shortly after submission. 
                        The changes, however, will take effect right away.<br /><br />
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%;">
                        <b>Username</b>:
                    </td>
                    <td style="width: 60%;">';
                        if ($CONFIG['modifyusn'] == 'yes') {
                            echo '<input type="text" name="username" value="' . $CONFIG['user'] . '" style="width: 100%;" maxlength="16" />';
                        } else {
                            echo $CONFIG['user'];
                        }
            echo '</td>
                </tr>
                <tr>
                    <td style="width: 40%;">
                        <b>New Password</b>:
                    </td>
                    <td style="width: 60%;">
                        <input type="password" name="newpass" maxlength="16" style="width: 100%;" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%;">
                        <b>Confirm New Password</b>:
                    </td>
                    <td style="width: 60%;">
                        <input type="password" name="cnewpass" maxlength="16" style="width: 100%;" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%;">
                        <b>Email address</b>:
                    </td>
                    <td style="width: 60%;">
                        <input type="text" name="email" maxlength="50" value="' . $user['email'] . '" style="width: 100%;" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%;">
                        <b>Hide Email In Posts?</b>
                    </td>
                    <td style="width: 60%;">
                        <input type="radio" name="hidemail" value="yes"' . getdefault($user['hidemail'], 'yes', 'check') . ' />Yes&nbsp;
                        <input type="radio" name="hidemail" value="no"' . getdefault($user['hidemail'], 'no', 'check') . ' />No
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="2" style="width: 100%;">
                        <br />
                        <input type="submit" value=" Save Information " class="formfield" />
                    </td>
                </tr>
            </table>
            </form>';
        } else {
            die(do_error('You are an invalid user.'));
        }
        
        include 'footer.html';
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // SAVEUSERINFO - Save modified user information:
    ////////////////////////////////////////////////////////////////////////////
    function saveuserinfo() {
        global $CONFIG;
      
        $sql = "UPDATE " . $CONFIG['tblPrefix'] . "users SET ";
        
        // See if we need to update the username:
        if ($CONFIG['modifyusn'] == 'yes') {
            if (strlen($_POST['username']) < 3) {
                die(do_error('Username must be more than 2 characters long.'));
            } else {
                if ($_POST['username'] != $CONFIG['user']) {
                    $sql .= "usn='" . $_POST['username'] . "', ";

                    setcookie('cvbsNews_usn', $_POST['username'], time() + 7*24*60*60, "/");
                }
            }
        }
       
        // See if we need to update the password:
        if ($_POST['newpass'] != '') {
            if ($_POST['newpass'] == $_POST['cnewpass'] && strlen($_POST['newpass']) > 3) {
                $sql .= "password='" . md5($_POST['newpass']) . "', ";

                // Mail the new password:
                $message = "Your password has been changed at " . $CONFIG['sitename'] . ".\n";
                $message .= "Your new password is: " . $_POST['newpass'] . "\n\n";
                $message .= "You may log in with it at any time.";

                mail($user['email'], $CONFIG['sitename'] . ' New Passowrd', $message, 
                    'From: ' . $CONFIG['sitename'] . ' <' . $CONFIG['adminmail']) . '>';

                setcookie('cvbsNews_upass', md5($_POST['newpass']), time() + 7*24*60*60, '/');
            } else {
                die(do_error('New passwords do not match or are not longer than 3 characters.'));
            }
        }

        include_once 'functions/function.validateemail.php';
    
        // Update the email:
        if (validateemail($_POST['email']) === false) {
            die(do_error('The email address <b>' . $_POST['email'] . '</b> is not valid.'));
        } else {
            $sql .= "email='" . $_POST['email'] . "' ";
        }

        $sql .= ", hidemail='" . $_POST['hidemail'] . "' WHERE usn='" . $CONFIG['user'] . "'";

        $result = mysql_query($sql) or die(mysql_error());
        if ($result) {
            $msg = 'User information has been saved.<br /><br />
            <a href="' . $CONFIG['scriptdir'] . 'index.php">Click here</a> to return to the main page.';

            do_message('User Information Updated', $msg);
        } else {
            die(do_error('Failed to save user information.'));
        }
    }
?>