<?php

    ////////////////////////////////////////////////////////////////////////////
    // DESCRIPTION:
    // comments.php - Comment functions for display.php
    ////////////////////////////////////////////////////////////////////////////
    // WARNING:
    // It is not recommended to modify anything below this line!
    ////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////
    // _COMMENTS - Handle the comments request:
    ////////////////////////////////////////////////////////////////////////////
    function _comments() {
        if (isset($_REQUEST['s'])) {
            switch($_REQUEST['s']) {
                case 'add':
                    addcomment(); break;

                case 'removeconfirm':
                    comments_removeconfirm(); break;
                case 'remove':
                    comments_remove(); break;

                case 'login':
                    comments_login_form(); break;
                case 'dologin':
                    // Make sure user isn't banned:
                    if (!isIPbanned()) {
                        comments_dologin(); break;
                    }

                case 'register':
                    comments_register(); break;
                case 'doregister':
                    comments_doregister(); break;

                case 'options':
                    comments_options(); break;
                case 'save':
                    comments_save(); break;

                case 'banconfirm':
                    comments_banconfirm(); break;
                case 'ban':
                    comments_banuser(); break;

                case 'login':
                    comments_login(); break;
                case 'logout':
                    comments_logout(); break;

                default:
                    showcomments();
            }
        } else {
            showcomments();
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // SHOWCOMMENTS - Show news comments:
    ////////////////////////////////////////////////////////////////////////////
    function showcomments() {
        global $CONFIG;

        // Get original post:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE id=" . $_REQUEST['id'];
        echo show_comment($sql, 0);

        if (isset($_REQUEST['s'])) {
            $start = $_REQUEST['s'];
        } else {
            $start = 1;
        }
        
        // Get comments:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "comments WHERE par_id='" . $_REQUEST['id'] . "'";
        $total = mysql_num_rows(mysql_query($sql));
        $sql .= " ORDER BY postedOn ASC LIMIT " . (($start - 1) * $CONFIG['numcomments']) . ", " . $CONFIG['numcomments'];

        echo show_comment($sql, 1);
        
        if ($total > $CONFIG['numcomments']) {
            include_once $CONFIG['absolute_path'] . 'functions/function.createlinks.php';

            $url = '?action=comments&id=' . $_REQUEST['id'] . '&s=';
            $links = createlinks($total, $CONFIG['numcomments'], $start, $url);

            echo str_replace('<links>', $links, gettemplate('pagelinks')) . '<br />';
        }

        // Show the comments form:
        comm_form($_REQUEST['id'], '', '');
    }

    ////////////////////////////////////////////////////////////////////////////
    // ADDCOMMENT - Add a news item comment:
    ////////////////////////////////////////////////////////////////////////////
    function addcomment() {
        global $CONFIG;

        if (isIPbanned()) {
            exit;
        }

        if ($CONFIG['up'] == 5) {
            die(displayerror('You have been banned from using this comment system. If you believe this is a mistake, please
                contact the <a href="mailto:' . $CONFIG['adminmail'] . '">administrator</a>.'));
        }

        // General error checking:
        if ($CONFIG['allowcomments'] == 'no') {
            die(displayerror('The comment feature is currently disabled.'));
        } elseif (strlen($_POST['c_subject']) < 2 || strlen($_POST['c_subject']) > 30) {
            die(displayerror('Comment subject must be between 2 and 30 characters.'));
        } elseif (strlen($_POST['c_message']) < 4 || strlen($_POST['c_message']) > $CONFIG['maxcomlen']) {
            die(displayerror('Comment message must be between 3 and ' . $CONFIG['maxcomlen'] . ' characters.'));
        }

        if (!isset($CONFIG['user'])) {
            die(displayerror('You are not currently logged in!'));
        } else {
            $sql = "INSERT INTO " . $CONFIG['tblPrefix'] . "comments (par_id, uID, postedOn, subject, body) VALUES ";
            $sql .= "(" . $_POST['id'] . ", '" . $CONFIG['uID'] . "', '" . date('Y-m-d h:m:s') . "', ";
            $sql .= "'" . addslashes($_POST['c_subject']) . "', ";
            $sql .= "'" . addslashes($_POST['c_message']) . "')";

            $result = mysql_query($sql) or die(displayerror(mysql_error()));

            $ips = explode('.', $_SERVER['REMOTE_ADDR']);
            $ip = $ips[0] . '.' . $ips[1] . '.x.x';

            $sql = "UPDATE " . $CONFIG['tblPrefix'] . "users SET lastIP='$ip' WHERE usn='" . $CONFIG['user'] . "'";
            mysql_query($sql) or die(displayerror( mysql_error()));

            $msg = 'Your comment has been added. Click <a href="?action=comments&id=' . $_POST['id'] . '">here</a> to
                go back.';

            echo str_replace('<message>', $msg, gettemplate('comments_message'));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // SHOW_COMMENT - show news comments based on SQL query
    ////////////////////////////////////////////////////////////////////////////
    function show_comment($sql, $type) {
        global $CONFIG;

        $comments = mysql_query($sql) or die(displayerror(mysql_error()));
        if ($comment = mysql_fetch_array($comments)) {
            $comm = '';

            include_once $CONFIG['absolute_path'] . 'functions/function.formatmsg.php';

            do {
                $c_t = '<a name="' . $comment['id'] . '"></a>' . gettemplate('comments');

                // Parse IDs:
                if ($type == 1) {
                    // Is comment
                    $c_t = str_replace('<id>', $comment['id'], $c_t);
                } else {
                    // Is original post
                    $c_t = str_replace('<id>', $comment['id'], $c_t);
                }

                $subject = htmlspecialchars(stripslashes($comment['subject']));
                if (isset($CONFIG['up']) && $CONFIG['up'] < 3) {
                    if ($type == 1) {
                        $subject .= ' <span style="font-size: 8pt; font-weight: normal;">
                            [ <a href="?action=comments&s=removeconfirm&id=' . $comment['id'] . '&postID=' . $_REQUEST['id'] . '">
                                Remove</a> ]
                        </span>';
                    }
                }

                $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE id='" . $comment['uID'] . "'";
                $users = mysql_query($sql) or die(displayerror(mysql_error()));

                if ($tbl_user = mysql_fetch_array($users)) {
                    $user = htmlspecialchars(stripslashes($tbl_user['usn']));
                    if ($tbl_user['hidemail'] == 'no') {
                        $user = '<a href="mailto:' . $tbl_user['email'] . '">' . $user . '</a>';
                    }

                    if ($tbl_user['up'] == 4 && $CONFIG['up'] < 3) {
                        $user .= ' <span style="font-size: 8pt; font-weight: normal;">
                            [ <a href="?action=comments&s=banconfirm&user=' . $tbl_user['usn'] . '">Ban</a> ]
                        </span>';
                    } elseif ($tbl_user['up'] == 5) {
                        $user .= ' <span style="font-size: 8pt; font-weight: normal;">
                            [ Banned ]
                        </span>';
                    }
                }

                // Parse tags:
                $c_t = str_replace('<user>', $user, $c_t);
                $c_t = str_replace('<subject>', $subject, $c_t);
                $c_t = str_replace('<datetime>', date($CONFIG['timeformat'], strtotime($comment['postedOn'])), $c_t);
                $c_t = str_replace('<item>', formatmsg($comment['body']), $c_t);

                $comm .= $c_t;
            } while ($comment = mysql_fetch_array($comments));

            return $comm;
        }

        return '';
    }

    ////////////////////////////////////////////////////////////////////////////
    // COMMENTS_REGISTER - Shows the user registration form for comments:
    ////////////////////////////////////////////////////////////////////////////
    function comments_register() {
        global $CONFIG;

        if (isIPbanned()) {
            exit;
        }

        if (isset($CONFIG['user'])) {
            die(displayerror('You are already registered as <b>' . $CONFIG['user'] . '</b>'));
        }

        echo '<form action="" method="post">
        <input type="hidden" name="action" value="comments">
        <input type="hidden" name="s" value="doregister">';

        echo gettemplate('comments_register');
        
        echo '</form>';
    }

    ////////////////////////////////////////////////////////////////////////////
    // COMMENTS_DOREGISTER - Register the comments user:
    ////////////////////////////////////////////////////////////////////////////
    function comments_doregister() {
        global $CONFIG;

        if (isIPbanned()) {
            exit;
        }

        if (strlen($_POST['c_name']) < 3 || strlen($_POST['c_name']) > 16) {
            die(displayerror('User names must be between 3 and 16 characters. Please try again!'));
        }

        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE usn='" . $_POST['c_name'] . "'";
        if (mysql_num_rows(mysql_query($sql)) > 0) {
            die(displayerror('This user name is already taken. Please try again!'));
        }

        if ($_POST['c_email'] <> $_POST['cc_email']) {
            die(displayerror('E-mail addresses do not match. Please try again!'));
        }

        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE email='" . $_POST['c_email'] . "'";
        $users = mysql_query($sql) or die(displayerror('MySQL Error - ' . mysql_error()));
        if ($user = mysql_fetch_array($users)) {
            if ($user['up'] == 5) {
                die(displayerror('You have been banned from using this comment system. If you believe this is a mistake, please
                    contact the <a href="mailto:' . $CONFIG['adminmail'] . '">administrator</a>.'));
            } else {
                die(displayerror('A user name has already been registred with this email address.'));
            }
        }

        // Generate random password:
        for ($i = 0; $i < 6; $i++) {
            $pass .= rand(0, 9);
        }
        $pass = substr(md5($pass), 8, 5);

        $sql = "INSERT INTO " . $CONFIG['tblPrefix'] . "users (usn, password, email, up) VALUES ";
        $sql .= "('" . $_POST['c_name'] . "', '" . md5($pass) . "', '" . $_POST['c_email'] . "', '4')";

        $result = mysql_query($sql) or die(mysql_error());
        if ($result) {
            $message = "Thank you for registering at " . $CONFIG['sitename'] . ".\n\n";
            $message .= "Your user name is: " . $_POST['c_name'] . "\n";
            $message .= "Your password is: " . $pass . "\n\n";
            $message .= "You may change this at any time.";

            $from = 'From: ' . $CONFIG['sitename'] . '<' . $CONFIG['adminmail'] . '>';

            mail($_POST['c_email'], $CONFIG['sitename'] . " Registration", $message, 'From: ' . $from);

            $msg = 'You have successfully signed up for <b>' . $CONFIG['sitename'] . '</b>. Please check the
                verification email sent to you.';

            echo str_replace('<message>', $msg, gettemplate('comments_message'));
        } else {
            displayerror('Failed to add information.');
        }

    }

    ////////////////////////////////////////////////////////////////////////////
    // COMMENTS_LOGIN - Show the login form for comments:
    ////////////////////////////////////////////////////////////////////////////
    function comments_login_form() {
        global $CONFIG;

        if (isIPbanned()) {
            exit;
        }

        echo '<form action="' . $CONFIG['scriptdir'] . 'commlogin.php" method="post">
        <input type="hidden" name="action" value="login">
        <input type="hidden" name="referto" value="' . $_SERVER['SCRIPT_NAME'] . '?action=comments&id=' . $_REQUEST['id'] . '">';

        echo gettemplate('comments_login');

        echo '</form>';
    }

    ////////////////////////////////////////////////////////////////////////////
    // COMM_FORM - Shows the 'add comment' form:
    ////////////////////////////////////////////////////////////////////////////
    function comm_form($id, $subject, $message) {
        global $CONFIG;

        // Make sure the user isn't banned:
        if (isIPbanned()) {
            exit;
        }

        if (!isset($CONFIG['user'])) {
            echo '<div align="center">
                [ <a href="?action=comments&s=login&id=' . $id . '">Login</a> | 
                  <a href="?action=comments&s=register">Register</a> ]
            </div>';

            exit;
        }

        // Setup the comments form from template:
        $tmpl = gettemplate('comments_form');
        $tmpl = str_replace('<options>', '<a href="?action=comments&id=' . $_REQUEST['id'] . '&s=options">Options</a>', $tmpl);
        $tmpl = str_replace('<logout>', '<a href="' . $CONFIG['scriptdir'] . 'commlogin.php?action=logout">Log Out</a>', $tmpl);
        $tmpl = str_replace('<id>', $id, $tmpl);
        $tmpl = str_replace('<user>', $CONFIG['user'], $tmpl);
        $tmpl = str_replace('<subject>', $subject, $tmpl);
        $tmpl = str_replace('<message>', $message, $tmpl);

        echo '<a name="commform"></a>
        <form action="" method="post">
            <input type="hidden" name="action" value="comments">
            <input type="hidden" name="s" value="add">
            <input type="hidden" name="id" value="' . $id . '">' . $tmpl . '</form>';
    }

    ////////////////////////////////////////////////////////////////////////////
    // COMMENTS_OPTIONS - Show the comments user options:
    ////////////////////////////////////////////////////////////////////////////
    function comments_options() {
        global $CONFIG;

        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE usn='" . $CONFIG['user'] . "'";
        $users = mysql_query($sql) or die(display_error('MySQL ERROR - ' . mysql_error()));

        if ($user = mysql_fetch_array($users)) {
            include_once $CONFIG['absolute_path'] . 'functions/function.getdefault.php';

            $c_options = '<form action="" method="post">
                <input type="hidden" name="action" value="comments">
                <input type="hidden" name="s" value="save">
                <input type="hidden" name="id" value="' . $_REQUEST['id'] . '">';

            $c_options .= gettemplate('comments_options') . '</form>';

            $c_options = str_replace('<user>', $user['usn'], $c_options);
            $c_options = str_replace('<email>', $user['email'], $c_options);
            $c_options = str_replace('<user>', $user['usn'], $c_options);

            $hidemail = '<input type="radio" name="hidemail" value="yes"' . getdefault($user['hidemail'], 'yes', 'check') . '>Yes &nbsp;
            <input type="radio" name="hidemail" value="no"' . getdefault($user['hidemail'], 'no', 'check'). '>No';

            $c_options = str_replace('<hidemail>', $hidemail, $c_options);

            echo $c_options;
        } else {
            die(display_error('Failed to query database.'));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // COMMENTS_SAVE - Save the user options:
    ////////////////////////////////////////////////////////////////////////////
    function comments_save() {
        global $CONFIG;

        // Error checking:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE email='" . $_POST['c_email'] . "' AND usn != '" . $CONFIG['user'] . "'";
        if (mysql_num_rows(mysql_query($sql)) > 0) {
            die(displayerror('A user name has already been registred with this email address.'));
        }

        if ($_POST['c_pass'] != $_POST['c_confirmpass']) {
            if ($_POST['c_pass'] != '') {
                die(displayerror('The passwords do not match. Please go back.'));
            }
        }

        // Create the SQL:
        $sql = "UPDATE " . $CONFIG['tblPrefix'] . "users SET email='" . $_POST['c_email'] . "', ";
        if ($_POST['c_pass'] != '') {
            $sql .= "password='" . md5($_POST['c_pass']) . "', ";
        }
        $sql .= "hidemail='" . $_POST['hidemail'] . "' WHERE usn='" . $CONFIG['user'] . "'";

        $result = mysql_query($sql) or die(mysql_error());
        if ($result) {
            if ($_POST['c_pass'] != '') {
                // Mail the new password:
                $message = "Your password has been changed at " . $CONFIG['sitename'] . ".\n";
                $message .= "Your new password is: " . $_POST['c_pass'] . "\n\n";
                $message .= "You may log in with it at any time.";

                mail($_POST['c_email'], $CONFIG['sitename'] . ' New Passowrd', $message, 
                    'From: vbsNews <' . $CONFIG['adminmail']) . '>';
            }

            $msg = 'Your user information has been updated. <a href="?action=comments&id=' . $_POST['id'] . '">Go back</a> to the
                news comments.';

            echo str_replace('<message>', $msg, gettemplate('comments_message'));
        } else {
            displayerror('Failed to update information.');
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // COMMENTS_BANCONFIRM - Confirm user banning:
    ////////////////////////////////////////////////////////////////////////////
    function comments_banconfirm() {
        if ($CONFIG['up'] < 3) {
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE usn='" . $_REQUEST['user'] . "'";
            $users = mysql_query($sql) or die(displayerror('MySQL ERROR - ' . mysql_error()));

            if ($user = mysql_fetch_array($users)) {
                echo 'Are you sure you want to ban this user: <b>' . $_REQUEST['user'] . '</b>?<br />
                <form action="" method="post">
                    <input type="hidden" name="action" value="comments" />
                    <input type="hidden" name="s" value="ban" />
                    <input type="hidden" name="user" value="' . $_REQUEST['user'] . '" />
                    <input type="button" name="no" value=" Go Back " onclick="history.back();" />
                    <input type="submit" name="remove" value=" Ban User " /> 
                </form>';
            } else {
                die(displayerror('Invalid user name specified.'));
            }
        } else {
            die(displayerror('You do not have permission to ban users.'));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // COMMENTS_BANUSER - Ban the user:
    ////////////////////////////////////////////////////////////////////////////
    function comments_banuser() {
        if ($CONFIG['up'] < 3) {
            $sql = "UPDATE " . $CONFIG['tblPrefix'] . "users SET up=5 WHERE usn='" . $_POST['user'] . "'";
            $result = mysql_query($sql) or die(displayerror('MySQL ERROR - ' . mysql_error()));

            if ($result) {
                $msg = 'The user <b>' . $_POST['user'] . '</b> has been banned.';

                echo str_replace('<message>', $msg, gettemplate('comments_message'));
            }
        } else {
            die(displayerror('You do not have permission to ban users.'));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // COMMENTS_REMOVECONFIRM - Confirm comment removal:
    ////////////////////////////////////////////////////////////////////////////
    function comments_removeconfirm() {
        global $CONFIG;

        if ($CONFIG['up'] < 3) {
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "comments WHERE id=" . $_REQUEST['id'];
            $comments = mysql_query($sql) or die(displayerror('MySQL ERROR - ' . mysql_error()));

            if ($comment = mysql_fetch_array($comments)) {
                echo 'Are you sure you want to delete this comment: <b>' . stripslashes($comment['subject']) . '</b>?<br />
                <form action="" method="post">
                    <input type="hidden" name="action" value="comments" />
                    <input type="hidden" name="s" value="remove" />
                    <input type="hidden" name="postID" value="' . $_REQUEST['postID'] . '" />
                    <input type="hidden" name="id" value="' . $_REQUEST['id'] . '" />
                    <input type="button" name="no" value=" Go Back " onclick="history.back();" />
                    <input type="submit" name="remove" value=" Remove Comment " /> 
                </form>';
            } else {
                die(displayerror('Invalid user name specified.'));
            }
        } else {
            die(displayerror('You do not have permission to ban users.'));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // COMMENTS_REMOVE - Remove the comment:
    ////////////////////////////////////////////////////////////////////////////
    function comments_remove() {
        global $CONFIG;

        if ($CONFIG['up'] < 3) {
            $sql = "DELETE FROM " . $CONFIG['tblPrefix'] . "comments WHERE id=" . $_POST['id'];
            $result = mysql_query($sql) or die(displayerror('MySQL ERROR - ' . mysql_error()));

            if ($result) {
                $msg = 'The comment has been removed. Click <a href="?action=comments&id=' . $_POST['postID'] . '">here</a> to
                    go back.';

                echo str_replace('<message>', $msg, gettemplate('comments_message'));
            } else {
                die(displayerror('Failed to remove comment'));
            }
        } else {
            die(displayerror('You do not have permission to ban users.'));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // ISIPBANNED - Detect whether or not the user's IP has been banned:
    ////////////////////////////////////////////////////////////////////////////
    function isIPbanned() {
        global $CONFIG;

        if ($CONFIG['banips'] == 'yes') {
            $ips = explode('.', $_SERVER['REMOTE_ADDR']);
            $ip = $ips[0] . '.' . $ips[1] . '.x.x';

            $users = mysql_query("SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE lastIP='$ip' AND up=5") 
                or die(displayerror('MySQL Error - ' . mysql_error()));

            if ($user = mysql_fetch_array($users)) {
                echo 'You have been banned from using this comment system. If you believe this is a mistake, please
                    contact the <a href="mailto:' . $CONFIG['adminmail'] . '">administrator</a>.';

                return true;
            }
        }

        return false;
    }

?>