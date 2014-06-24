<?php

    //////////////////////////////////////////////////////////////////////////
    // POSTMAIN - main function for news posting:
    //////////////////////////////////////////////////////////////////////////
    function postmain() {
        if (isset($_REQUEST['s'])) {
            switch ($_REQUEST['s']) {
                case 'submit':
                    create_form(); break; //create a blank form
                case 'post':
                    savepost(); break; //post the news item

                default:
                    //error
            }
        } else {
            //error
        }
    }

    //////////////////////////////////////////////////////////////////////////
    // CREATE_FORM - create the submission or modification form:
    //////////////////////////////////////////////////////////////////////////
    function create_form($method = 0) {
        global $CONFIG;

        // Include header:
        $jsFiles = array('js_formvalidation', 'js_tags.js');
        $page_title = ($method == 1) ? 'Modify News Item' : 'Submit News Item';
        $title = $CONFIG['sitename'] . ' - ' . $page_title;
        include 'header.html';

        // Replace basic elements:
        $postform = postform();
        $postform = str_replace('<title>', $page_title, $postform);
        $postform = str_replace('<boardcode>', boardcode(), $postform);

        if ($method == 1) {
            // We're modifying news:
            // Replace basic modify elements:
            $postform = str_replace('<action>', 'modify', $postform);
            $postform = str_replace('<s>', 'save', $postform);
            $postform = str_replace('<buttons>', '<input type="submit" value=" Save News " class="formfield" />', $postform);
            $postform = str_replace('<extra>', '', $postform);

            // Get the item from the database and replace information:
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE id=" . $_REQUEST['id'];
            $items = mysql_query($sql) or die(do_error(mysql_error(), 1));
            if ($item = mysql_fetch_array($items)) {
                // Begin replacing information:
                $postform = str_replace('<id>', $item['id'], $postform);
                $postform = str_replace('<subject>', stripslashes($item['subject']), $postform);
                $postform = str_replace('<body>', stripslashes($item['body']), $postform);
                $postform = str_replace('<categories>', categories($item['cat_name']), $postform);
            } else {
                die(do_error('Invalid news item ID.'));
            }
            // Free the result:
            mysql_free_result($items);
        } else {
            // Posting an item, we need a blank form:
            $postform = str_replace('<action>', 'post', $postform);
            $postform = str_replace('<s>', 'post', $postform);
            $postform = str_replace('<buttons>', '<input type="submit" value=" Submit News " class="formfield" />', $postform);
            $postform = str_replace('<id>', '', $postform);
            $postform = str_replace('<subject>', '', $postform);
            $postform = str_replace('<body>', '', $postform);
            $postform = str_replace('<categories>', categories(), $postform);
        }

        // Output the form:
        echo $postform;

        include 'footer.html';
    }

    //////////////////////////////////////////////////////////////////////////
    // POSTFORM - HTML form template for posting/modifying news:
    //////////////////////////////////////////////////////////////////////////
    function postform() {
        global $CONFIG;

        // Create the form:
        $output = '<form name="post_form" method="post" onsubmit="return validate(this);" action="' . $CONFIG['scriptdir'] . 'index.php" enctype="multipart/form-data" style="margin: 0px;">
            <input type="hidden" name="action" value="<action>" />
            <input type="hidden" name="s" value="<s>" />
            <input type="hidden" name="id" value="<id>" />
            <table cellpadding="2" cellspacing="0" style="margin-left: auto; margin-right: auto; width: 100%;">
            <tr>
                <td class="bars" colspan="3">
                    <b><title></b>
                </td>
            </tr>
            <tr>
                <td style="width: 115px;"><b>Subject</b>:</td>
                <td colspan="2">
                    <input type="text" name="subject" size="30" value="<subject>" maxlength="' . $CONFIG['max_subject_len'] . '" style="width: 100%;" />
                </td>
            </tr>
            <categories>
            <boardcode>
            <tr>
                <td colspan="3">
                    <textarea name="body" style="width: 100%;" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" cols="0" rows="20"><body></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center;">
                    <br />
                    <buttons>
                </td>
            </tr>
        </table>
        </form>';

        // Return the output:
        return $output;
    }

    //////////////////////////////////////////////////////////////////////////
    // CATEGORIES - category information for post/modify form:
    //////////////////////////////////////////////////////////////////////////
    function categories($category = '') {
        global $CONFIG;

        $output = '';
        if ($CONFIG['enablecats'] == 'yes') {
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "cats";
            $cats = mysql_query($sql) or die(do_error('MySQL ERROR - ' . mysql_error()));

            $num = mysql_num_rows($cats);
            if ($num != 0) {
                $output = '<tr>
                    <td class="box_notop_left"><b>Category</b>:</td>
                    <td class="box_notop_right" colspan="2">
                        <select name="category" style="width: 100%;">';
                
                if ($cat = mysql_fetch_array($cats)) {
                    $availcats = explode(', ', $CONFIG['cats']);
                    do {
                        if ($CONFIG['cats'] == 'all' || array_search($cat['key_name'], $availcats) !== false) {
                            $output .= '<option value="' . $cat['key_name'] . '"';
                            if ($cat['key_name'] == $category) {
                                $output .= ' selected="selected"';
                            }
                            $output .= '>' . $cat['name'] . '</option>';
                        }
                    } while ($cat = mysql_fetch_array($cats));
                }

                $output .= '</select>
                    </td>
                </tr>';
            }
        }

        // Return the output:
        return $output;
    }
    
    //////////////////////////////////////////////////////////////////////////
    // BOARDCODE - Board code information for post/modify form:
    //////////////////////////////////////////////////////////////////////////
    function boardcode() {
        global $CONFIG;

        // Show board [tags], if allowed:
        $boardcode = '';
        if ($CONFIG['allowtags'] == 'yes') {
            $boardcode = '<tr>
                <td valign="top"><b>Message Code</b>:</td>
                <td colspan="2">
                    <input type="button" onclick="javascript: basicTag(this.form.body, \'b\', \'b\');" style="width: 25px;" value=" B " class="formfield" />
                    <input type="button" onclick="javascript: basicTag(this.form.body, \'i\', \'i\');" style="width: 25px;" value=" I " class="formfield" />
                    <input type="button" onclick="javascript: basicTag(this.form.body, \'u\', \'u\');" style="width: 25px;" value=" U " class="formfield" />
                    <input type="button" onclick="javascript: dolink(this.form.body);" value=" LINK " class="formfield" />
                    <input type="button" onclick="javascript: dolist(this.form.body);" value=" LIST " class="formfield" />';

            if ($CONFIG['allowimages'] == 'yes') {
                $boardcode .= '&nbsp;<input type="button" onclick="javascript: doimg(this.form.body);" value = " IMAGE " class="formfield" />';
            }
            
            $boardcode .= '&nbsp;<input type="button" onclick="javascript: basicTag(this.form.body, \'quote\', \'quote\');" value=" QUOTE " class="formfield" />
                    <br />
                    <input type="button" onclick="javascript: doalign(this.form.body);" value=" ALIGN " class="formfield" />&nbsp;

                    <select name="color" style="margin-top: 4px;" onchange="advTag(this.form.body, \'color\', this.value);" class="formfield">
                        <option value="">Font Color: </option>
                        <option value="">============</option>
                        <option value="#000000">Black</option>
                        <option value="#ffffff">White</option>
                        <option value="#0000ff" style="color: #0000FF;">Blue</option>
                        <option value="#004080" style="color: #004080;">Dark Blue</option>
                        <option value="#ff0000" style="color: #FF0000;">Red</option>
                        <option value="#800000" style="color: #800000;">Dark Red</option>
                        <option value="#008000" style="color: #008000;">Green</option>
                        <option value="#00FF00" style="color: #00FF00;">Lime Green</option>
                        <option value="#FFCC33" style="color: #FFCC33;">Light Orange</option>
                        <option value="#FF9900" style="color: #FF9900;">Dark Orange</option>
                    </select>
                    <select name="color" style="margin-top: 4px;" onchange="advTag(this.form.body, \'size\', this.value);" class="formfield">
                        <option value="">Font Size: </option>
                        <option value="">============</option>
                        <option value="8">Smaller</option>
                        <option value="10">Small</option>
                        <option value="14">Large</option>
                        <option value="16">Larger</option>
                    </select>
                </td>
            </tr>';
                }

        // Return the output:
        return $boardcode;
    }

    //////////////////////////////////////////////////////////////////////////
    // SAVEPOST - saves the newly added or modified post:
    //////////////////////////////////////////////////////////////////////////
    function savepost() {
        global $CONFIG;

        // Error checking:
        if (strlen($_POST['subject']) < 2 || strlen($_POST['subject']) > $CONFIG['max_subject_len']) {
            die(do_error('Post subject must be between 2 and ' . $CONFIG['max_subject_len'] . ' characters in length.'));
        } elseif (strlen($_POST['body']) < 4) {
            die(do_error('Post body must be 4 or more characters in length.'));
        }

        // Figure out whether we're posting or saving:
        if ($_REQUEST['s'] == 'post') {
            // Build the query:
            $sql = "INSERT INTO " . $CONFIG['tblPrefix'] . "news (uID, subject, body, postedOn";
            if ($CONFIG['enablecats'] == 'yes' && isset($_POST['category'])) {
                $sql .= ", cat_name";    
            }
            
            $sql .= ") VALUES (" . $CONFIG['uID'] . ", '" . addslashes($_POST['subject']) . "', ";
            $sql .= "'" . addslashes($_POST['body']) . "', '" . date('Y.m.d H:i:s') . "'";
            
            if ($CONFIG['enablecats'] == 'yes' && isset($_POST['category'])) {
                $sql .= ", '" . $_POST['category'] . "'";     
            }
            
            // Finish SQL:
            $sql .= ")";
        } elseif ($_REQUEST['s'] == 'save') {
            // Find the post:
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news WHERE id='". $_POST['id'] . "'";
            $posts = mysql_query($sql);
            if ($post = mysql_fetch_array($posts)) {
                // Create the SQL query:
                $sql = "UPDATE " . $CONFIG['tblPrefix'] . "news SET subject='" . addslashes($_POST['subject']) . "', body='";
                $sql .= addslashes($_POST['body']) . "' ";
                
                if ($CONFIG['enablecats'] == 'yes' && isset($_POST['category'])) {
                    $sql .= ", cat_name='" . $_POST['category'] . "' ";
                } 
              
                
                $sql .= "WHERE id='" . $_POST['id'] . "'";
            } else {
                die(do_error('Invalid post ID specified. Cannot modify item.'));
            }

        }

        // Create or save the news item:
        $result = mysql_query($sql) or die(mysql_error());
        if ($result) {
            // Create message:
            $msg = 'Your post was saved in the database successfully.<br /><br />
            <a href="' . $CONFIG['scriptdir'] . 'index.php">Click here</a> to return to the main page.';
            // Display success message:
            do_message('Post Saved', $msg);
        } else {
            // Unknown error:
            die(do_error('Failed to create or modify post.'));
        }
    }
    
?>