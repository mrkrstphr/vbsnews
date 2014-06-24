<?php

    ////////////////////////////////////////////////////////////////////////////
    // DESCRIPTION:
    // modify.php - Modify news item functions
    ////////////////////////////////////////////////////////////////////////////
    // WARNING:
    // It is not recommended to modify anything below this line!
    ////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////
    // MODIFYMAIN - Main function to handle news modification:
    ////////////////////////////////////////////////////////////////////////////
    function modifymain() {
        if (isset($_REQUEST['s'])) {
            switch ($_REQUEST['s']) {
                case 'modify':
                    include 'post.php';
                    create_form(1); break;

                case 'save':
                    include 'post.php';
                    savepost(); break;
                    
                case 'delete':
                    removeconfirm(); break;

                case 'remove':
                    if (isset($_POST['delIDs'])) {
                        removemultiple();
                    } else {
                        remove();
                    }

                    break;

                default:
                    modifyselect();
            }
        } else {
            modifyselect();
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // MODIFYSELECT - News selection for modification:
    ////////////////////////////////////////////////////////////////////////////
    function modifyselect() {
        global $CONFIG;
        
        $jsFiles = 'js_selchbox.js';
        $title = $CONFIG['sitename'] . ' - Modify News';
        include 'header.html';

        // Figure out what user(s) we are showing:
        if (isset($_REQUEST['username']) && $CONFIG['up'] < 3) {
            $user_name = $_REQUEST['username'];
        } elseif ($CONFIG['up'] < 3) {
            $user_name = 'All Users';
        } else {
            $user_name = $CONFIG['user'];
        }

        // Start table output:
        echo '<table cellpadding="2" cellspacing="0" style="margin-left: auto; margin-right: auto; width: 555px;">
            <tr>
                <td class="bars">
                    <b>Modify News</b>
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    Showing news items by <b>' . $user_name . '</b>';
            
        if (isset($_REQUEST['category'])) {
            echo ' in category <b>' . $_REQUEST['category'] . '</b>';
        }
            
        echo '. To modify the news items, click on the title. To filter by category or user name, click on the category or user name and only those matching items will be shown.<br /><br />';
        
        // Find starting point:
        if (isset($_REQUEST['start'])) {
            $start = $_REQUEST['start'];
        } else {
            $start = 1;
        }
        
        // Create SQL:
        if ($CONFIG['up'] < 3) {
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news ";

            // Setup category information:
            if (isset($_REQUEST['category'])) {
                $sql .= "WHERE cat_name='" . $_REQUEST['category'] . "' ";
            }

            // Stup username information:
            if (isset($_REQUEST['username'])) {
                $user_sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE usn='" . $_REQUEST['username'] . "'";
                $users = mysql_query($user_sql) or die(do_error(mysql_error(), 1));
                if ($user = mysql_fetch_array($users)) {
                    if (strpos($sql, 'WHERE') !== false) {
                        $sql .= "AND uID=" . $user['id'] . " ";
                    } else {
                        $sql .= "WHERE uID=" . $user['id'] . " ";
                    }
                }

                mysql_free_result($users);
            }
            $sql .= "ORDER by postedOn DESC";
        } else {
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE uID='" . $CONFIG['uID'] . "' ";

            // Setup category information:
            if (isset($_REQUEST['category'])) {
                $sql .= "AND cat_name='" . $_REQUEST['category'] . "' ";
            }
            $sql .= "ORDER by postedOn DESC";
        }

        $num = mysql_num_rows(mysql_query($sql));
        $sql .= " LIMIT " . (($start - 1) * 20) . ", 20";

        // Get news items:        
        $posts = mysql_query($sql) or die(do_error(mysql_error(), 1));
        //$showing_num = mysql_num_rows($posts);
        if ($post = mysql_fetch_array($posts)) {
            // Echo table header:
            echo '<form action="index.php" method="post">
                <input type="hidden" name="action" value="modify" />
                <input type="hidden" name="s" value="delete" />
                <table cellpadding="2" cellspacing="0" style="margin-left: auto; margin-right: auto; width: 555px;">
                    <tr>
                        <td class="barcolor" style="text-align: left; width: 10%;"><b>Delete</b></td>
                        <td class="barcolor" style="text-align: left; width: 62%;"><b>Subject</b></td>
                        <td class="barcolor" style="text-align: left; width: 28%;"><b>Posted By</b></td>
                    </tr>';

            $color[] = '#f2f2f2'; $color[] = '#fbfbfb'; $i = 0;
            do {
                $users = mysql_query("SELECT * FROM " . $CONFIG['tblPrefix'] . "users WHERE id=" . $post['uID'])
                    or die(do_error(mysql_error()));

                $i = ($i == 0) ? $i = 1 : $i = 0;

                // Setup user name link:
                if ($user = mysql_fetch_array($users)) {
                    $user_name = '<a href="' . $CONFIG['scriptdir'] . 'index.php?action=modify&start=' . $start;
                    if (isset($_REQUEST['category'])) {
                        $user_name .= '&category=' . $_REQUEST['category'];
                    }
                    $user_name .= '&username=' . $user['usn'] . '">' . $user['usn'] . '</a>';
                } else {
                    $user_name = 'Unknown';
                }

                // Setup category link:
                if ($post['cat_name'] != '' && $CONFIG['enablecats'] == 'yes') {
                    $cat_link = '<a href="' . $CONFIG['scriptdir'] . 'index.php?action=modify&start=' . $start;
                    if (isset($_REQUEST['username'])) {
                        $cat_link .= '&username=' . $_REQUEST['username'];
                    }
                    $cat_link .= '&category=' . $post['cat_name'] . '">' . $post['cat_name'] . '</a>';
                } else {
                    $cat_link = 'N/A';
                }

                // Echo news information:
                echo '<tr>
                    <td style="background-color: ' . $color[$i] . '; text-align: center;">
                        <input type="checkbox" name="delIDs[]" value="' . $post['id'] . '" />
                    </td>
                    <td style="background-color: ' . $color[$i] . '; text-align: left;">
                        <a href="' . $CONFIG['scriptdir'] . 'index.php?action=modify&s=modify&id=' . $post['id'] . '">' . formatname($post['subject']) . '</a>
                    </td>
                    <td style="background-color: ' . $color[$i] . ';text-align: left; ">' . $user_name . '</td>
                </tr>
                <tr>
                    <td style="background-color: ' . $color[$i] . '; text-align: center;">
                        &nbsp;
                    </td>
                    <td style="font-size: 8pt; background-color: ' . $color[$i] . '; text-align: left;">
                        <b>Category</b>: ' . $cat_link . '</td>
                    <td style="font-size: 8pt; background-color: ' . $color[$i] . '; text-align: left;">
                        <b>Posted On</b>: ' . date('Y-m-d', strtotime($post['postedOn'])) . '</td>
                </tr>';
            } while($post = mysql_fetch_array($posts));

            // Echo 'Select All Items' option and navigation links (if applicable):
            echo '<tr>
                <td class="barcolor" colspan="3" style="padding: 0px; vertical-align: middle;">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <tr>
                            <td style="width: 50%; text-align: left;">
                                <!--<a href="#null" onclick="javascript: selectAll(\'delIDs[]\');"><b>Check/Uncheck All Items</b></a>-->
                                <input type="checkbox" name="checkall" onclick="selectAll(this.form, this.checked);" /> Check/Uncheck All Items
                            <td>
                            <td style="width: 50%; text-align: right;">';

            // Create links (if needed):
            include_once 'functions/function.createlinks.php';

            $url = $CONFIG['scriptdir'] . 'index.php?action=modify';
            $url .= (isset($_REQUEST['username'])) ? '&username=' . $_REQUEST['username'] : '';
            $url .= (isset($_REQUEST['category'])) ? '&category=' . $_REQUEST['category'] : '';
            $url .= '&start=';

            $L = createlinks($num, 20, $start, $url);
            if ($L != '') {
                echo $L;
            } else {
                echo '&nbsp;';
            }

            echo '</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center; width: 100%;">
                    <br />
                    <input type="submit" name="submit" value=" Delete Selected " />
                </td>
            </tr></table></form>';
        } else {
            // No news items:
            echo '<b>There are no news items.</b>';
        }

        echo '</td></tr></table>';
        include 'footer.html';
    }

    ////////////////////////////////////////////////////////////////////////////
    // FORMATNAME - Used in modifyselect() to format news item names:
    ////////////////////////////////////////////////////////////////////////////
    function formatname($string) {
        if (strlen($string) > 35) {
            $string = substr($string, 0, 34) . '...';   
        }
        // Return the string:
        return stripslashes(htmlspecialchars($string));
    }
           
    ////////////////////////////////////////////////////////////////////////////
    // REMOVECONFIRM - Remove news confirmation:
    ////////////////////////////////////////////////////////////////////////////
    function removeconfirm() {
        global $CONFIG;
            
        // Show confirmations (either multiple or single):
        if (isset($_POST['delIDs'])) {            
            $msg = 'Are you sure you want to delete the following news items?<br /><br />
            <form action="index.php" method="post">
            <input type="hidden" name="action" value="modify" />
            <input type="hidden" name="s" value="remove" />';

            // Get information about all posts selected for deletion:
            for ($i = 0; $i < count($_POST['delIDs']); $i++) {
                $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE id=" . $_POST['delIDs'][$i];
                $items = mysql_query($sql) or die(do_error(mysql_error(), 1));

                // Create ouput for post item:
                if ($item = mysql_fetch_array($items)) {
                    $msg .= '<input type="checkbox" name="delIDs[]" value="' . $item['id'] . '" checked="checked" />
                    <b>' . stripslashes($item['subject']) . '</b><br />';
                }
            }

            // Continue building message:
            $msg .= '<br />
                <div style="text-align: center;">
                    <input type="button" name="no" value=" Go Back " onclick="history.back();" />
                    <input type="submit" name="remove" value=" Delete News Item(s) " />
                </div>
            </form>';

            // Display message:
            do_message('Confirm Delete', $msg);
        } else {
            if (!isset($_REQUEST['id'])) {
                die(do_error('No news items selected for deletion.'));
            }

            // Get the news item:
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE id=" . $_REQUEST['id'];
            $news = mysql_query($sql) or die(do_error(mysql_error(), 1));
            if ($item = mysql_fetch_array($news)) {
                // Start building output:
                $msg = 'Are you sure you want to delete this item: <b>' . stripslashes($item['subject']) . '</b>?<br /><br />
                <form action="' . $CONFIG['scriptdir'] . 'index.php" method="post">
                    <input type="hidden" name="action" value="modify" />
                    <input type="hidden" name="s" value="remove" />
                    <input type="hidden" name="id" value="' . $_REQUEST['id'] . '" />
                
                    <div style="text-align: center;">
                        <input type="submit" name="remove" value=" Delete News Item " />    
                        <input type="button" name="no" value=" Go Back " onclick="history.back();" />
                    </div>
                </form>';

                // Show message:
                do_message('Confirm Delete', $msg);
            } else {
                // Invalid ID given:
                die(do_error('Invalid news item ID.'));
            }
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // REMOVE - Removes a news item:
    ////////////////////////////////////////////////////////////////////////////
    function remove() {
        global $CONFIG;

        // Find the item in the database:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE id=" . $_POST['id'];
        $items = mysql_query($sql) or die(do_error(mysql_error(), 1));
        if ($item = mysql_fetch_array($items)) {
            // Check to make sure they can delete:
            if (($item['uID'] == $CONFIG['uID']) || $CONFIG['up'] < 3) {
                // Delete the item from the database:
                $sql = "DELETE FROM " . $CONFIG['tblPrefix'] . "news WHERE id=" . $_POST['id'];
                $result = mysql_query($sql) or die(do_error(mysql_error(), 1));

                if ($result) {
                   
                    // Buld success message:
                    $msg = 'News item removed successfully.<br><br>
                    <a href="' . $CONFIG['scriptdir'] . 'index.php">Click here</a> to return to the main page.';
                    // Output success message:
                    do_message('News Item Removed', $msg);
                } else {
                    // Unknown error:
                    die(do_error('Failed to delete news item.'));
                }
            } else {
                // No permission to delete news item:
                die(do_error('You do not have permission to delete this item.'));
            }
        } else {
            // News item doesn't exist.
            die(do_error('Invalid news item ID given. Unable to remove news item.'));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // REMOVEMULTIPLE - Removes multiple news items:
    ////////////////////////////////////////////////////////////////////////////
    function removemultiple() {
        global $CONFIG;

        if (!isset($_POST['delIDs'])) {
            // No posts were selected:
            die(do_error('No posts selected for deletion.'));
        }

        // Loop the news item IDs:
        for($i = 0; $i < count($_POST['delIDs']); $i++) {
            // Delete the news items:
            $result = mysql_query("DELETE FROM " . $CONFIG['tblPrefix'] . "news WHERE id=" . $_POST['delIDs'][$i]) 
                or die(do_error(mysql_error(), 1));

            if (!$result) {
                // Failed to remove news item, report error:
                die(do_error('Failed to delete news item. ID: ' . $_POST['delIDs'][$i]));
            }
        }

        // Create success message:
        $msg = 'News items removed successfully! The items will disappear right away, and any comments made to the
            news item have also been deleted.<br /><br />
        <a href="' . $CONFIG['scriptdir'] . 'index.php">Click here</a> to return to the main page.';

        // Report success:
        do_message('News Items Removed', $msg);
    }
?>