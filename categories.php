<?php

    ////////////////////////////////////////////////////////////////////////////
    // DESCRIPTION:
    // cats.php - Category management functions
    ////////////////////////////////////////////////////////////////////////////
    // WARNING:
    // It is not recommended to modify anything below this line!
    ////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////
    // CATSMAIN - Handle the category request:
    ////////////////////////////////////////////////////////////////////////////
    function catsmain() {
        if (isset($_REQUEST['s'])) {
            switch ($_REQUEST['s']) {
                case 'add':
                    addcat(); break;
                case 'confirm':
                    delconfirm(); break;
                case 'delete':
                    delcat(); break;
                case 'edit':
                    editcat(); break;
                case 'save':
                    savecat(); break;
                default:
                    managecats();
            }
        } else {
            managecats();
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // MANAGECATS - Main category management page:
    ////////////////////////////////////////////////////////////////////////////
    function managecats() {
        global $CONFIG;

        $title = $CONFIG['sitename'] . ' - Manage Categories';
        include 'header.html';

        echo '<form action="index.php" method="post" style="margin: 0px;">
        <input type="hidden" name="action" value="managecats" />
        <input type="hidden" name="s" value="add" />
        <table cellpadding="2" cellspacing="0" style="margin-left: auto; margin-right: auto; width: 555px;">
            <tr>
                <td class="bars" colspan="2" style="width: 100%;">
                    <b>Manage Categories</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="width: 100%;">
                    News categories allow you to seperate types of news items into groups. You could, for example, have
                    one category for site news, one for reviews, and one for stories.<br /><br />';

            if ($CONFIG['enablecats'] == 'no') {
                echo '<b>Note</b>: News categories are currently <b>DISABLED</b>. To enable them, toggle the option in
                    the settings section.<br /><br />';
            }
                    
            echo '<table align="center" style="width: 450px; border-bottom: 1px solid #000000;">
                <tr>
                    <td colspan="3" style="border-bottom: 1px solid #000000; width: 100%;">
                        <b>Current Categories</b>
                    </td>
                </tr>';

            $cats = mysql_query("SELECT * FROM " . $CONFIG['tblPrefix'] . "cats") or die(do_error(mysql_error(), 1));
            if ($cat = mysql_fetch_array($cats)) {
                do {
                    $link1 = $CONFIG['scriptdir'] . 'index.php?action=managecats&amp;s=edit&amp;key=' . $cat['key_name'];
                    $link2 = $CONFIG['scriptdir'] . 'index.php?action=managecats&amp;s=confirm&amp;key=' . $cat['key_name'];

                    echo '<tr>
                        <td align="left">' . $cat['name'] . '</td>
                        <td align="right"><a href="' . $link1 . '">Edit</a></td>
                        <td align="right"><a href="' . $link2 . '">Delete</a></td>
                    </tr>';
                } while ($cat = mysql_fetch_array($cats));
            }
            
            echo '</table>
                <br /><br />
                </td>
            </tr>
            <tr>
                <td class="bars" colspan="2" style="width: 100%;">
                    <b>Add Category</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="width: 100%;">
                    The <i>Category Name</i> is what will be displayed in news submission. The <i>Category Key</i> is 
                    what you will use to reference the category when displaying news. This must be unique.<br /><br />
                </td>
            </tr>
            <tr>
                <td style="width: 140px;">
                    Category Name:
                </td>
                <td style="width: 415px;">
                    <input type="text" name="cat_name" style="width: 100%;" maxlength="25" />
                </td>
            </tr>
            <tr>
                <td style="width: 140px;">
                    Category Key:
                </td>
                <td style="width: 415px;">
                    <input type="text" name="cat_key" style="width: 100%;" maxlength="15" />
                </td>
            </tr>
            <tr>
                <td style="width: 140px;">
                    Category Template:
                </td>
                <td style="width: 415px;">';
                    
        // Get user templates:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "templates WHERE type='user'";
        $templates = mysql_query($sql) or die(do_error(mysql_error(), 1));

        if ($template = mysql_fetch_array($templates)) {
            echo '<select name="templateName" style="width: 100%;">
                <option value="">Use default news template</option>';

            do {
                echo '<option value="' . $template['name'] . '">' . $template['name'] . '</option>';
            } while ($template = mysql_fetch_array($templates));
            echo '</select>';
        } else {
            echo '<select name="templateName" style="width: 100%;">
                <option value="">Use default news template</option>
            </select>';
        }
  
        echo '</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; width: 100%;">
                    <br />
                    <input type="submit" name="submit" value=" Add Category " class="formfield" />
                </td>
            </tr>
        </table>
        </form>';

        include 'footer.html';
    }

    ////////////////////////////////////////////////////////////////////////////
    // ADDCAT - Add a news category:
    ////////////////////////////////////////////////////////////////////////////
    function addcat() {
        global $CONFIG;

        // Validate information:
        if (strlen($_POST['cat_key']) < 3) {
            die(do_error('The <i>Category Key</i> must be more than 2 characters in length.'));
        } elseif (strlen($_POST['cat_name']) < 3) {
            die(do_error('The <i>Category Name</i> must be more than 2 characters in length.'));
        }

        // Check for duplication:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "cats WHERE key_name='" . $_POST['cat_key'] . "'";
        $cats = mysql_query($sql) or die(do_error(mysql_error(), 1));
        if ($cat = mysql_fetch_array($cats)) {
            die(do_error('The specified <i>Category Key</i> is already being used.'));
        }

        // Add the category:
        $sql = "INSERT INTO " . $CONFIG['tblPrefix'] . "cats (name, key_name, templateName) ";
        $sql .= "VALUES ('" . addslashes($_POST['cat_name']) . "'";
        $sql .= ", '" . addslashes($_POST['cat_key']) . "', '" . addslashes($_POST['templateName']) . "')";

        $result = mysql_query($sql) or die(do_error(mysql_error(), 1));
        if ($result) {
            header('Location: ' . $CONFIG['scriptdir'] . 'index.php?action=managecats');
        } else {
            die(do_error('Failed to create new category.'));
        }

    }

    ////////////////////////////////////////////////////////////////////////////
    // DELCONFIRM - Confirm category deletion:
    ////////////////////////////////////////////////////////////////////////////
    function delconfirm() {
        global $CONFIG;
            
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "cats WHERE key_name='" . $_REQUEST['key'] . "'";
        $cats = mysql_query($sql) or die(do_error(mysql_error(), 1));

        if ($cat = mysql_fetch_array($cats)) {
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE cat_name='" . $_REQUEST['key'] . "'";
            $num = mysql_num_rows(mysql_query($sql));

            $msg = 'Are you sure you want to delete this category: <b>' . stripslashes($cat['name']) . '</b>?<br />
            <form action="' . $CONFIG['scriptdir'] . 'index.php" method="post">
                <input type="hidden" name="action" value="managecats" />
                <input type="hidden" name="s" value="delete" />
                <input type="hidden" name="key" value="' . $_REQUEST['key'] . '" />';

            if ($num > 0) {
                $msg .= 'There are also currently <b>' . $num . '</b> news items under this category. What would you like to do with
                them?<br /><br />
                <b>Action</b>: <select name="move">
                    <option value="delete">Delete Them</option>
                    <option value="nothing">Do Nothing</option>';

                $l_cats = mysql_query("SELECT * FROM " . $CONFIG['tblPrefix'] . "cats") or die(do_error(mysql_error(), 1));
                if ($l_cat = mysql_fetch_array($l_cats)) {
                    do {
                        if ($l_cat['key_name'] != $_REQUEST['key']) {
                            $msg .= '<option value="' . $l_cat['key_name'] . '">Move them to "' . $l_cat['name'] . '"</option>';
                        }
                    } while ($l_cat = mysql_fetch_array($l_cats));
                }
                

                $msg .= '</select><br />';
            }

            $msg .= '<br /><div style="text-align: center;">
                <input type="submit" name="remove" value=" Delete " />    
                <input type="button" name="no" value=" Go Back " onclick="history.back();" />
            </div>
            </form>';

            do_message('Confirm Category Deletion', $msg);
        } else {
            die(do_error('Invalid category key specified.'));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // DELCAT - Deletes a category:
    ////////////////////////////////////////////////////////////////////////////
    function delcat() {
        global $CONFIG;

        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "cats WHERE key_name='" . $_POST['key'] . "'";
        $cats = mysql_query($sql) or die(do_error(mysql_error(), 1));

        if ($cat = mysql_fetch_array($cats)) {
            $sql = "DELETE FROM " . $CONFIG['tblPrefix'] . "cats WHERE key_name='" . $_POST['key'] . "'";
            $result = mysql_query($sql) or die(do_error(mysql_error(), 1));

            if ($result) {
                // Take care of news items:
                if (isset($_POST['move'])) {
                    if ($_POST['move'] == 'delete') {
                        $sql = "DELETE FROM " . $CONFIG['tblPrefix'] . "news WHERE cat_name='" . $_POST['key'] . "'";
                        mysql_query($sql) or die(do_error(mysql_error(), 1));
                    } else {
                        if ($_POST['move'] != 'nothing') {
                            $sql = "UPDATE " . $CONFIG['tblPrefix'] . "news SET cat_name='" . $_POST['move'] . "' ";
                            $sql .= "WHERE cat_name='" . $_POST['key'] . "'";
                            mysql_query($sql) or die(do_error(mysql_error(), 1));
                        }
                    }
                }
                
                $msg = 'The category was removed successfully.<br /><br />
                <a href="' . $CONFIG['scriptdir'] . 'index.php?action=managecats">
                    Click here</a> to return to category management.';

                do_message('Category Removed', $msg);
            } else {
                die(do_error('Failed to remove category'));
            }
        } else {
            die(do_error('Invalid category key specified.'));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // EDITCAT - Modify category information:
    ////////////////////////////////////////////////////////////////////////////
    function editcat() {
        global $CONFIG;

        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "cats WHERE key_name='" . $_REQUEST['key'] . "'";
        $cats = mysql_query($sql) or die(do_error(mysql_error(), 1));

        if ($cat = mysql_fetch_array($cats)) {
            $title = $CONFIG['sitename'] . ' - Edit Category';
            include 'header.html';

            echo '<form action="index.php" method="post">
            <input type="hidden" name="action" value="managecats" />
            <input type="hidden" name="s" value="save" />
            <input type="hidden" name="old_key" value="' . $cat['key_name']. '" />
            <input type="hidden" name="old_name" value="' . $cat['name']. '" />
            <table cellpadding="2" cellspacing="0" style="margin-left: auto; margin-right: auto; width: 555px;">
                <tr>
                    <td class="bars" colspan="2" style="border: 1px solid #000000; width: 100%;">
                        <b>Edit Category</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="width: 100%;">
                        The <i>Category Name</i> is what will be displayed in news submission. The <i>Category Key</i> is 
                        what you will use to reference the category when displaying news.<br /><br />
                    </td>
                </tr>
                <tr>
                    <td style="width: 140px;">
                        Category Name:
                    </td>
                    <td style="width: 415px;">
                        <input type="text" name="cat_name" style="width: 100%;" maxlength="25" value="' . $cat['name'] . '" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 140px;">
                        Category Key:
                    </td>
                    <td style="width: 415px;">
                        <input type="text" name="cat_key" style="width: 100%;" maxlength="15" value="' . $cat['key_name'] . '" />
                    </td>
                </tr>
                <tr>
                <td style="width: 140px;">
                    Category Template:
                </td>
                <td style="width: 415px;">';
                    
            // Get user templates:
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "templates WHERE type='user'";
            $templates = mysql_query($sql) or die(do_error(mysql_error(), 1));

            if ($template = mysql_fetch_array($templates)) {
                echo '<select name="templateName" style="width: 100%;">
                    <option value=""';
                if ($cat['templateName'] == '') {
                    echo 'selected="selected"';   
                }
                
                echo '>Use default news tempalte</option>';
                do {
                    echo '<option value="' . $template['name'] . '"';
                    
                    if ($cat['templateName'] == $template['name']) {
                        echo ' selected="selected"';
                    }

                    echo '>' . $template['name'] . '</option>';
                } while ($template = mysql_fetch_array($templates));
                echo '</select>';
            } else {
                echo '<select name="templateName" style="width: 100%;">
                    <option value="">Use default news tempalte</option>
                </select>';
            }
      
            echo '</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center; width: 100%;">
                        <br />
                        <input type="submit" name="submit" value=" Save Category " class="formfield" />
                    </td>
                </tr>
            </table>
            </form>';

            include 'footer.html';
        } else {
            die(do_error('Invalid category ID specified.'));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // SAVECAT - Saves category information:
    ////////////////////////////////////////////////////////////////////////////
    function savecat() {
        global $CONFIG;

        // Build SQL:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "cats WHERE ";
        $sql .= "key_name='" . $_POST['cat_key'] . "' AND name != '" . $_POST['old_name'] . "'";

        // Make sure key_name isn't in use:
        $cats = mysql_query($sql) or die(do_error(mysql_error(), 1));
        if ($cat = mysql_fetch_array($cats)) {
            die(do_error('The specified Key Name is already being used.'));
        }
        mysql_free_result($cats);

        // Build 2nd SQL:
        $sql = "UPDATE " . $CONFIG['tblPrefix'] . "cats SET key_name='" . $_POST['cat_key'] . "'";
        $sql .= ", name='" . $_POST['cat_name'] . "', templateName='" . $_POST['templateName'] . "'";
        $sql .= " WHERE key_name='" . $_POST['old_key'] . "'";

        // Update the category:
        $result = mysql_query($sql) or die(do_error(mysql_error(), 1));
        if ($result) {
            $title = $CONFIG['sitename'] . ' - Category Updated Successfully';
            include 'header.html';
            
            echo 'The category was updated successfully.<br /><br />
            <a href="' . $CONFIG['scriptdir'] . 'index.php?action=managecats">
                Click here</a> to return to category management.';

            include 'footer.html';
        } else {
            die(do_error('Failed to update category information.'));
        }
    }

?>