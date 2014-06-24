<?php

    include 'config.php';

    if (isset($_REQUEST['action'])) {
        switch ($_REQUEST['action']) {
            case 'installation':
                installation(); break;
            default:
                main();
        }
    } else {
        main();
    }

    function main() {
        global $CONFIG;

        if (isset($CONFIG['dbuser']) && isset($CONFIG['dbpassword']) && isset($CONFIG['db'])) {
            // Check to see if the installation has already been completed:
            $result = @mysql_query("SELECT * FROM " . $CONFIG['tblPrefix'] . "users");
            if (@mysql_num_rows($result) == 0) {
                $title = 'vbsNews - Installation';
                include_once 'header.html';

                echo 'Welcome to the <i>vbsNews</i> installation script! This script is designed to help you install your copy of <i>vbsNews</i> quickly. To start, fill out the information below and click next. Afterwards, six tables will be created in your MySQL database, and <i>vbsNews</i> will be ready to use.
                <br /><br />
                <form action="install.php" method="post">
                    <input type="hidden" name="action" value="installation" />
                    <div style="text-align: center;">
                        <table cellpadding="2" cellspacing="0" style="margin-left: auto; margin-right: auto; text-align: left; width: 400px;">
                            <tr>
                                <td colspan="2" style="background-color: #ffcc00; border: 1px solid #000000;">
                                    <b>User Information</b>:
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    In order to use <i>vbsNews</i>, you must create a user login for yourself. You will use this information to log into the <i>vbsNews</i> Administration Section.
                                    <br /><br />
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 45%;">
                                    <b>User Name</b>:
                                </td>
                                <td style="width: 55%;">
                                    <input type="text" name="user" maxlength="16" style="width: 100%;" />
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 45%;">
                                    <b>Password</b>:
                                </td>
                                <td style="width: 55%;">
                                    <input type="password" name="pass" maxlength="16" style="width: 100%;" />
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 45%;">
                                    <b>Confirm Password</b>:
                                </td>
                                <td style="width: 55%;">
                                    <input type="password" name="cpass" maxlength="16" style="width: 100%;" />
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 45%;">
                                    <b>E-mail</b>:
                                </td>
                                <td style="width: 55%;">
                                    <input type="text" name="email" maxlength="50" style="width: 100%;" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <!-- spacer --> &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="background-color: #ffcc00; border: 1px solid #000000;">
                                    <b>Required Settings</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    Below are required settings for <i>vbsNews</i> to run. The site name is what <i>vbsNews</i> will be refered to itself as. The script directory is that path that you installed <i>vbsNews</i> at. Other system settings can be modified after the installation is complete.
                                    <br /><br />
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 45%;">
                                    <b>Site Name</b>:
                                </td>
                                <td style="width: 55%;">
                                    <input type="text" name="sitename" style="width: 100%;" />
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 45%;">
                                    <b>News Script Directory</b>:
                                </td>
                                <td style="width: 55%;">
                                    <input type="text" name="scriptdir" value="http://www.yoursite.com/news/" style="width: 100%;" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <br />
                                    <input type="submit" value=" Complete Installation " />
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>';

                include_once 'footer.html';
            } else {
                die(do_error('The installation process has already been completed.'));
            }
        } else {
            $error = 'Configuration variables are not set. Please open <i>config.php</i> and set them.';
            die(do_error($error));
        }
    }

    function installation() {
        global $CONFIG;

        if (strlen($_POST['user']) < 3) {
            die(do_error('The user name must be greater than 2 characters.'));
        } elseif ($_POST['pass'] == '' || $_POST['pass'] != $_POST['cpass']) {
            die(do_error('The passwords given are either blank or do not match.'));
        } elseif (strlen($_POST['scriptdir']) < 9) {
            die(do_error('<i>News Script Directory</i> is invalid.'));
        }

        require_once 'db_install.php';

        $sql[] = "INSERT INTO " . $CONFIG['tblPrefix'] . "users (usn, password, email, up) VALUES ('" . $_POST['user'] . "', '" . md5($_POST['pass']) . "', '" . $_POST['email'] . "', 1)";
        $sql[] = "UPDATE " . $CONFIG['tblPrefix'] . "config SET value='" . $_POST['sitename'] ."' WHERE name='sitename'";
        $sql[] = "UPDATE " . $CONFIG['tblPrefix'] . "config SET value='" . $_POST['scriptdir'] ."' WHERE name='scriptdir'";

        for ($i = 0; $i < count($sql); $i++) {
            $result = mysql_query($sql[$i]) or die(do_error(mysql_error(), 1));
        }

        $title = 'vbsNews - Installation';
        include_once 'header.html';

        echo 'The installation of <i>vbsNews</i> is now complete. To begin using the program, <a href="index.php">click here</a>. Thank you for choosing <i>vbsNews</i>!<br /><br />
        Support can be found at <a href="http://www.vbshelf.com/" target="_blank">vbShelf.com</a>.<br />';

        include_once 'footer.html';
    }
?>