<?php
    
    ////////////////////////////////////////////////////////////////////////////
    // DESCRIPTION:
    // index.php - Main news management file. Handles just about everything
    ////////////////////////////////////////////////////////////////////////////
    // WARNING:
    // It is not recommended to modify anything below this line!
    ////////////////////////////////////////////////////////////////////////////
    // include the configuration file:
    include 'config.php';
    
    ////////////////////////////////////////////////////////////////////////////
    // If user is logged in:
    if (isset($CONFIG['user'])) {
        // Figure out what to do:
        if (isset($_REQUEST['action'])) {
            switch ($_REQUEST['action']) {
                case 'logout': //handle logout:
                    include 'login.php';
                    logout(); break;
                    
                case 'submit': //handle submit:
                    include 'post.php';
                    create_form(); break;
                case 'post': //handle submit:
                    include 'post.php';
                    savepost(); break; 

                case 'modify': //handle modification:
                    include 'modify.php';
                    modifymain(); break;

                case 'manageusers': //handle user management:
                    include 'users.php';
                    managemain(); break;

                case 'settings': //handle settings:
                    include 'settings.php';
                    settingsmain(); break;

                case 'userinfo': //handle user info:
                    include 'users.php';
                    usersmain(); break;

                case 'managecats': //handle categories:
                    include 'categories.php';
                    catsmain(); break;

                case 'templates': //handle templates:
                    include 'templates.php';
                    tempmain(); break;

                default: //go to main page:
                    mainpage();
            }
        } else {
            mainpage();
        }
    ////////////////////////////////////////////////////////////////////////////
    // Else user is not logged in:
    } else {
        include 'login.php';
        
        // Figure out what to do:
        if (isset($_REQUEST['action'])) {
            if ($_REQUEST['action'] == 'login') {
                login();
            } elseif ($_REQUEST['action'] == 'forgotpass') {
                forgotpass();
            } else {
                showlogin();
            }
        } else {
            showlogin();
        }
    }
    
    //////////////////////////////////////////////////////////////////////////
    // MAINPAGE - Display the main navigation page:
    //////////////////////////////////////////////////////////////////////////
    function mainpage() {
        global $CONFIG;
        
        $title = $CONFIG['sitename'] . ' - vbsNews';
	    include 'header.html'; 

        // Get user post count:
        $url = $CONFIG['scriptdir'] . 'index.php?action=';
        $total = @mysql_num_rows(mysql_query("SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE uID='" . $CONFIG['uID'] . "'"));

        // Figure out user status:
        switch($CONFIG['up']) {
            case 1:
                $type = 'Administrator'; break;
            case 2:
                $type = 'Moderator'; break;
            case 3:
                $type = 'User'; break;
        }

        // Welcome message and news options:
	    echo 'Hello, <b>' . $CONFIG['user'] . '</b>! You have contributed <b>' . $total . '</b> news posts to the database.
        Your current user status is <b>' . $type . '</b>. Choose from the following options below:<br /><br />
        <table cellpadding="2" cellspacing="0" style="margin-left: auto; margin-right: auto; width: 555px;">
            <tr>
                <td colspan="3"><b>News Options</b>:</td>
            </tr>
            <tr>
                <td style="width: 16px;">&nbsp;</td>
                <td style="text-align: center; width: 20px;"><img alt="" border="0" src="images/bullet.gif" /></td>
                <td>
                    <a href="' . $CONFIG['scriptdir'] . 'index.php?action=submit">Submit News Item</a>: Add a news item to the database.
                </td>
            </tr>
            <tr>
                <td style="width: 16px;">&nbsp;</td>
                <td style="text-align: center; width: 20px;"><img alt="" border="0" src="images/bullet.gif" /></td>
                <td>
                    <a href="' . $CONFIG['scriptdir'] . 'index.php?action=modify">Modify News Items</a>: Modify a news item in the database.
                </td>
            </tr>';
        
        // Admin options (if applicable):
        if ($CONFIG['up'] == 1) {
            echo '<tr>
                    <td colspan="3">
                        <br />
                        <b>Administrator Options</b>:
                    </td>
                </tr>
                </tr>
                    <td style="width: 16px;">&nbsp;</td>
                    <td style="text-align: center; width: 20px;"><img alt="" border="0" src="images/bullet.gif" /></td>
                    <td><a href="' . $CONFIG['scriptdir'] . 'index.php?action=manageusers">Manage Users</a>: Add or Remove
                    users from the database.
                    </td>
                </tr>
                </tr>
                    <td style="width: 16px;">&nbsp;</td>
                    <td style="text-align: center; width: 20px;"><img alt="" border="0" src="images/bullet.gif" /></td>
                    <td><a href="' . $CONFIG['scriptdir'] . 'index.php?action=settings">Change Settings</a>: Modify vbsNews 
                    settings.
                    </td>
                </tr>
                </tr>
                    <td style="width: 16px;">&nbsp;</td>
                    <td style="text-align: center; width: 20px;"><img alt="" border="0" src="images/bullet.gif" /></td>
                    <td><a href="' . $url . 'managecats">Manage Categories</a>: Add or remove news categories.
                    </td>
                </tr>
                </tr>
                    <td style="width: 16px;">&nbsp;</td>
                    <td style="text-align: center; width: 20px;"><img alt="" border="0" src="images/bullet.gif" /></td>
                    <td><a href="' . $url . 'templates">Update Templates</a>: Update the system templates used to 
                    display news.
                    </td>
                </tr>';
        }

        echo '<tr>
            <td colspan="3">
                <br />
                <b>User Options</b>:
            </td>
        </tr>
        <tr>
            <td style="width: 16px;">&nbsp;</td>
            <td style="text-align: center; width: 20px;"><img alt="" border="0" src="images/bullet.gif" /></td>
            <td>
                <a href="' . $CONFIG['scriptdir'] . 'index.php?action=userinfo">Modify User Information</a>: Modify your user information.
            </td>
        </tr>
        <tr>
            <td style="width: 16px;">&nbsp;</td>
            <td style="text-align: center; width: 20px;"><img alt="" border="0" src="images/bullet.gif" /></td>
            <td>
                <a href="' . $CONFIG['scriptdir'] . 'index.php?action=logout">Logout</a>: Logout from the vbsNews system.
            </td>
        </tr></table>
        <br />';
        
        //get recent news submissions for user:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE uID='" . $CONFIG['uID'] . "' ORDER By postedOn DESC LIMIT 5";
        $items = mysql_query($sql);
        
        echo '<table border="0" cellpadding="2" cellspacing="0" style="width: 100%;">
                <tr>
                    <td class="bars" colspan="3">
                        <b>Recent News Submissions</b>
                    </td>
                </tr>';

        if ($item = mysql_fetch_array($items)) {
            do {
                echo '<tr>
                    <td style="width: 70%;">' . stripslashes(htmlspecialchars($item['subject'])) . '</td>
                    <td style="width: 15%;">
                        <a href="' . $CONFIG['scriptdir'] . 'index.php?action=modify&s=modify&id=' . $item['id'] . '">Edit</a>
                    </td>
                    <td style="width: 15%;">
                        <a href="' . $CONFIG['scriptdir'] . 'index.php?action=modify&s=delete&id=' . $item['id'] . '">Delete</a>
                    </td>
                </tr>';
            } while($item = mysql_fetch_array($items));
        } else {
            echo '<tr>
                <td colspan="3">No news submissions found.</td>
            </tr>';
        }
        echo '</table>
        <br />';
        
        include 'footer.html';
    }
?>