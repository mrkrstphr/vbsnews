<?php

    ////////////////////////////////////////////////////////////////////////////
    // DESCRIPTION:
    // templates.php - Template management functions
    ////////////////////////////////////////////////////////////////////////////
    // WARNING:
    // It is not recommended to modify anything below this line!
    ////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////
    // TEMPMAIN - Handle the template requests:
    ////////////////////////////////////////////////////////////////////////////
    function tempmain() {
        if (isset($_REQUEST['s'])) {
            switch ($_REQUEST['s']) {
                case 'modify_news':
                    news_templates(); break;
                case 'modify_comments':
                    comment_templates(); break;
                case 'manage_user':
                    user_templates(); break;

                case 'modify':
                    modify_template(); break;
                case 'save':
                    save_template(); break;
                case 'add':
                    add_template(); break;
                case 'delete':
                    delete_templates(); break;
                default:
                    managetemps();
            }
        } else {
            managetemps();
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // MANAGETEMPS - Main template management page:
    ////////////////////////////////////////////////////////////////////////////
    function managetemps() {
        global $CONFIG;

        $title = $CONFIG['sitename'] . ' - Update Templates';
        include 'header.html';

        echo '<table cellpadding="2" cellspacing="0" style="margin-left: auto; margin-right: auto; width: 555px;">
            <tr>
                <td class="bars" colspan="2" style="width: 100%;">
                    <b>Update Templates</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: left;">
                    Templates are used by the <i>vbsNews</i> system when displaying news items, comments, archives, 
                    and similar things. The purpose of these templates is to allow you complete control over how things look on your news page. 
                    <br /><br />
                
                    Each template has their own special set of tags, very much like HTML tags, that are replaced with the
                    respective data when the template is parsed. These tags are explained on the modification page for 
                    each template.<br /><br />

                    <b>Template Options</b>:<br />
                    <ul style="margin-top: 0px; margin-bottom: 0px;">
                        <li><a href="' . $CONFIG['scriptdir'] . 'index.php?action=templates&s=modify_news">Modify News Templates</a>: Modify the news templates.</li>
                        <li><a href="' . $CONFIG['scriptdir'] . 'index.php?action=templates&s=modify_comments">Modify Comment Templates</a>: Modify the comment templates.</li>
                        <li><a href="' . $CONFIG['scriptdir'] . 'index.php?action=templates&s=manage_user">Manage User Templates</a>: Manage (Add/Modify/Delete) the user templates.</li>
                    </ul><br />

                    If something somehow goes wrong when you are modifying your templates, there is a utility on
                    <a href="http://www.vbshelf.com/" target="_blank">vbShelf</a> that can restore the templates to their
                    defaults.
                </td>
            </tr>
        </table>';

        include 'footer.html';
    }


    function news_templates() {
        global $CONFIG;

        $title = $CONFIG['sitename'] . ' - Modify News Templates';
        include 'header.html';

        echo '<table cellpadding="2" cellspacing="0" style="margin-left: auto; margin-right: auto; width: 555px;">
            <tr>
                <td class="bars" colspan="2" style="width: 100%;">
                    <b>Modify News Templates</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: left;">
                    The following are templates associated with the displaying of news items. Click on the template name for instructions and to modify the template.<br /><br />
                    <table style="width: 100%;">
                        <tr>
                            <td style="background-color: #C5C5C5; text-align: left;"><b>News Templates</b>:</td>
                        </tr>
                        <tr>
                            <td style="background-color: #f4f4f4;text-align: left; ">
                                <a href="index.php?action=templates&s=modify&name=news"><b>News Template</b></a>: Default template for news items (and archive items). This template is ignored if there is a category-specific template defined.
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #dfdfdf; text-align: left;">
                                <a href="index.php?action=templates&s=modify&name=header"><b>Header Template</b></a>: The style of the news headers (ie headlines) is controlled by this template.
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #dfdfdf; text-align: left;">
                                <a href="index.php?action=templates&s=modify&name=pagelinks"><b>Page Links Template</b></a>: Controls the style of the navigational links for news and comment items.
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #f4f4f4; text-align: left;">
                                <a href="index.php?action=templates&s=modify&name=archives"><b>Archives Template</b></a>: The text <i>around</i> the archive listing is control by this item. This has no effect on individual archive links.
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #dfdfdf; text-align: left;">
                                <a href="index.php?action=templates&s=modify&name=archiveitem"><b>Archive Item Template</b></a>: Each individual archive list item (ie month) is controlled by this template.
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #f4f4f4; text-align: left;">
                                <a href="index.php?action=templates&s=modify&name=display_error"><b>Display Error</b></a>: This is used to control the display of an error that might occur during any type of news or comment displaying.
                            </td>
                        </tr>
                    </table><br />
                    These templates cannot be renamed or deleted. Only user templates can be modified in that manner.
                </td>
            </tr>
        </table>';

        include 'footer.html';
    }

    function comment_templates() {
        global $CONFIG;

        $title = $CONFIG['sitename'] . ' - Modify Comment Templates';
        include 'header.html';

        echo '<table cellpadding="2" cellspacing="0" style="margin-left: auto; margin-right: auto; width: 555px;">
            <tr>
                <td class="bars" colspan="2" style="width: 100%;">
                    <b>Modify Comment Templates</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: left;">
                    The following are templates associated with the displaying of comment items. Click on the template name for instructions and to modify the template.<br /><br />
                    <table style="width: 100%;">
                        <tr>
                            <td style="background-color: #C5C5C5; text-align: left;"><b>Comment Templates</b>:</td>
                        </tr>
                        <tr>
                            <td style="background-color: #f4f4f4; text-align: left;">
                                <a href="index.php?action=templates&s=modify&name=comments"><b>Comment Item Template</b></a>: Default template for comment items. This template is ignored if there is a category-specific template defined.
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #dfdfdf; text-align: left;">
                                <a href="index.php?action=templates&s=modify&name=comments_form"><b>Comment Form</b></a>: 
                                Replaced with the comment form that is used to post comments on news items.
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #f4f4f4; text-align: left;">
                                <a href="index.php?action=templates&s=modify&name=comments_options"><b>Comments Options</b></a>: Replaced with the comment options form for registered comment users.
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #dfdfdf; text-align: left;">
                                <a href="index.php?action=templates&s=modify&name=comments_register"><b>Comments Registration</b></a>: 
                                Replaced with the comments registration form that allows users to be a registered member. This option can be turned off.
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #f4f4f4; text-align: left;">
                                <a href="index.php?action=templates&s=modify&name=comments_login"><b>Comments Login</b></a>: Replaced with the comments login form for registered comment users.
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #dfdfdf; text-align: left;">
                                <a href="index.php?action=templates&s=modify&name=comments_message"><b>Comment Message</b></a>: This controls the look of a message, given by the comment system, such as confirmations and notices.
                            </td>
                        </tr>
                    </table><br />
                    These templates cannot be renamed or deleted. Only user templates can be modified in that manner.
                </td>
            </tr>
        </table>';

        include 'footer.html';
    }

    function user_templates() {
        global $CONFIG;

        $title = $CONFIG['sitename'] . ' - Manage User Templates';
        include 'header.html';

        echo '<table style="margin-left: auto; margin-right: auto; width: 100%;">
            <tr>
                <td class="bars" colspan="2" style="width: 100%;">
                    <b>Manage User Templates</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: left;">
                    The following are templates associated with the displaying of comment items. Click on the template name for instructions and to modify the template.<br /><br />
                    <form action="index.php" method="post">
                    <input type="hidden" name="action" value="templates" />
                    <input type="hidden" name="s" value="delete" />
                    <table style="width: 100%;">
                        <tr>
                            <td style="background-color: #c5c5c5; text-align: left;"><b>User Templates</b>:</td>
                        </tr>';

        // Get user-created news and comment templates:
        $sql = "SELECT name FROM " . $CONFIG['tblPrefix'] . "templates WHERE type='user' ORDER BY name ASC";
        $templates = mysql_query($sql) or die(do_error(mysql_error(), 1));

        $bg_color = '';
        if ($template = mysql_fetch_array($templates)) {
            do {
                $bg_color = ($bg_color == '#f4f4f4') ? '#dfdfdf' : '#f4f4f4';

                echo '<tr>
                    <td style="background-color: ' . $bg_color . '; text-align: left; vertical-align: center;">
                        <input type="checkbox" name="templates[]" value="' . $template['name'] . '" /> 
                        <a href="index.php?action=templates&s=modify&name=' . $template['name'] . '">
                            <b>' . $template['name'] . '</b></a>: No description available.
                    </td>
                </tr>';
            } while ($template = mysql_fetch_array($templates));

            echo '<tr>
                <td style="text-align: center; width: 100%;">
                    <br />
                    <input type="submit" value=" Delete Selected " />
                </td>
            </tr>';
        } else {
            echo '<tr>
                <td style="background-color: #f4f4f4;">
                    No user templates exist.
                </td>
            </tr>';
        }

        // Finish output:                        
        echo '</table></form>
                </td>
            </tr>
            <tr>
                <td class="bars" colspan="2" style="width: 100%;">
                    <b>Add User Template</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: left; width: 100%;">
                    For instructions and tags available in user templates, view the instructions for the <i>News Template</i>. Instructions and tags are the same.<br /><br />
                    <div style="text-align: center;">
                        <form action="index.php" method="post">
                        <input type="hidden" name="action" value="templates" />
                        <input type="hidden" name="s" value="add" />
                        <table style="margin-left: auto; margin-right: auto; table-layout: fixed; width: 100%;">
                            <tr>
                                <td style="text-align: left; width: 30%;">
                                    <b>Template Name</b>:
                                </td>
                                <td style="text-align: left; width: 70%;">
                                    <input type="text" name="name" maxlength="20" style="width: 100%;" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: left; width: 100%;">
                                    <b>Template Text</b>:<br />
                                    <textarea name="template" cols="0" rows="10" style="width: 100%;"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center; width: 100%;">
                                    <br />
                                    <input type="submit" value=" Add Template " />
                                </td>
                            </tr>
                        </table>
                        </form>
                    </div>
                </td>
            </tr>
        </table>';

        include 'footer.html';
    }

    ////////////////////////////////////////////////////////////////////////////
    // MODIFY_TEMPLATE - Template modification and viewer:
    ////////////////////////////////////////////////////////////////////////////
    function modify_template() {
        global $CONFIG;

        // Build SQL statement:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "templates WHERE name='" . $_REQUEST['name'] . "'";
        // Get Template:
        $templates = mysql_query($sql) or die(do_error(mysql_error()));
        if ($template = mysql_fetch_array($templates)) {
            $title = $CONFIG['sitename'] . ' - Modify Template';
            include 'header.html';

            if ($template['type'] == 'system') {
                $comments = $template['comments'];
            } else {
                $sql2 = "SELECT * FROM " . $CONFIG['tblPrefix'] . "templates WHERE name='news'";
                $tmps = mysql_query($sql2) or die(do_error(mysql_error(), 1));
                if ($tmp = mysql_fetch_array($tmps)) {
                    $comments = $tmp['comments'];
                } else {
                    $comments = '';
                }

                mysql_free_result($tmps);
            }

            echo '<form action="' . $CONFIG['scriptdir'] . 'index.php" method="post" style="margin: 0px;">
            <input type="hidden" name="action" value="templates" />
            <input type="hidden" name="s" value="save" />
            <input type="hidden" name="name" value="' . $template['name'] . '" />
            <table align="center" style="width: 550px;">
                <tr>
                    <td class="bars" colspan="2" style="border: 1px solid #000000; width: 100%;">
                        <b>Modify Template</b>: ' . $template['name'] . '</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: left;">
                        <b>Template Instructions</b> <br />' . $comments . '<br /><br />';

            if ($template['type'] == 'user') {
                echo '<b>Template Name</b>: 
                    <input type="name" name="new_name" style="width: 50%;" value="' . $template['name'] . '" />
                <br /><br />';
            }
            
            echo '<b>Current Template</b>:<br />
                        <textarea name="new_template" rows="10" cols="0" style="width: 100%;">' 
                            . stripslashes(htmlspecialchars($template['template'])) . '</textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <br />
                        <input type="submit" value=" Save Changes " class="formfield" />
                    </td>
                </tr>
            </table>
            </form>';

            include 'footer.html';
        } else {
            die(do_error('Invalid template name specified.'));
        }

    }

    ////////////////////////////////////////////////////////////////////////////
    // ADD_TEMPLATE - Add a user template to the database:
    ////////////////////////////////////////////////////////////////////////////
    function add_template() {
        global $CONFIG;

        // Error check:
        if (strlen($_POST['name']) < 2 || strlen($_POST['name']) > 20) {
            die(do_error('Template name must be between 2 and 20 characters. Please go back.'));
        } elseif (strlen($_POST['template']) == 0) {
            die(do_error('No template text as specified. Please go back.'));
        }

        // Check to make sure the template name doesn't already exist:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "templates WHERE name='" . $_POST['name'] . "'";
        $templates = mysql_query($sql) or die(do_error(mysql_error(), 1));
        if ($template = mysql_fetch_array($templates)) {
            // The template name is already in use:
            die(do_error('A template with the name <b>' . $_POST['name'] . '</b> already exists.'));
        } else {
            // Build the SQL query:
            $comments = 'For instructions and tags available in user templates, view the instructions for the <i>News Template</i>. Instructions and tags are the same.';
            $sql = "INSERT INTO " . $CONFIG['tblPrefix'] . "templates (name, type, template, comments) VALUES ";
            $sql .= "('" . addslashes($_POST['name']) . "', 'user', '" . addslashes($_POST['template']) . "', '$comments')";

            // Add the template:
            $result = mysql_query($sql) or die(do_error(mysql_error(), 1));
            if ($result) {
                // Go back to template management:
                header('Location: index.php?action=templates&s=manage_user');
            } else {
                die(do_error('Unknown error. Failed to create new template.'));
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // DELETE_TEMPLATE - Delete a user template from the database:
    ////////////////////////////////////////////////////////////////////////////
    function delete_templates() {
        global $CONFIG;

        // Make sure items were selected for deletion:
        if (!isset($_POST['templates'])) {
            die(do_error('No templates were selected for deletion.'));
        }

        if (isset($_POST['confirmed'])) {
            // Delete the user templates from the database:
            for ($i = 0; $i < count($_POST['templates']); $i++) {
                $sql = "DELETE FROM " . $CONFIG['tblPrefix'] . "templates WHERE name='" . addslashes($_POST['templates'][$i]) . "'";
                $result = mysql_query($sql) or die(do_error(mysql_error(), 1));

                if (!$result) {
                    die(do_error('Failed to delete template <b>' . $_POST['templates'][$i] . '</b>'));
                }
            }

            $msg = 'The selected templates were removed successfully.<br /><br /><a href="index.php?action=templates">Click here</a> to return to template management, or <a href="index.php">click here</a> to go to the main page.';

            do_message('Templates Removed', $msg);
        } else {
            // Display confirmation page for template deletion:
            $msg = 'Are you sure you want to delete the following templates?<br /><br />
            <form action="index.php" method="post">
            <input type="hidden" name="action" value="templates" />
            <input type="hidden" name="s" value="delete" />
            <input type="hidden" name="confirmed" value="yes" />';

            for ($i = 0; $i < count($_POST['templates']); $i++) {
                $msg .= '<input type="checkbox" name="templates[]" value="' . $_POST['templates'][$i] . '" checked="checked" /> ' . $_POST['templates'][$i] . '<br />';
            }

            $msg .= '<div style="text-align: center;">
                    <input type="button" name="no" value=" Go Back " onclick="history.back();" />
                    <input type="submit" name="remove" value=" Delete Template(s) " />
                </div>
            </form>';

            do_message('Confirm Delete', $msg);
        }

    }

    ////////////////////////////////////////////////////////////////////////////
    // SAVE_TEMPLATE - Save template changes:
    ////////////////////////////////////////////////////////////////////////////
    function save_template() {
        global $CONFIG;

        // Build the SQL query:
        $sql = "UPDATE " . $CONFIG['tblPrefix'] . "templates SET template='" . addslashes($_POST['new_template']) . "'";

        if (isset($_POST['new_name'])) {
            $sql .= ", name='" . $_POST['new_name'] . "'";
        }

        $sql .= " WHERE name='" . $_POST['name'] . "'";

        $result = mysql_query($sql) or die(do_error(mysql_error()));
        if ($result) {
            $msg = 'Template modification was successful! The updates made to the template will appear right away.<br /><br />
            <a href="' . $CONFIG['scriptdir'] . 'index.php?action=templates">Click here</a> to go back to  Template Modification or <a href="index.php">click here</a> to go to the main page.';

            do_message('Template Saved', $msg);
        } else {
            die(do_error('Failed to update template.'));
        }

    }
    

?>