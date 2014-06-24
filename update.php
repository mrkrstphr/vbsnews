<?php

    require 'config.php';

    if (isset($_REQUEST['stage'])) {
        switch($_REQUEST['stage']) {
            case '2':
                stageTwo(); break;
            case '3':
                stageThree(); break;
            case '4':
                stageFour(); break;
            case '5':
                stageFive(); break;
            case '6':
                stageSix(); break;
            case '7':
                completion(); break;
            default:
                stageOne();
        }
    } else {
        stageOne();
    }

    function stageOne() {
        global $CONFIG;

        ///////////////////////////////////////////////////////////
        // Table structure for table cats
        $sql = 'CREATE TABLE IF NOT EXISTS `' . $CONFIG['tblPrefix'] . 'cats` (
            `key_name` varchar(15) NOT NULL default \'\',
            `name` varchar(25) NOT NULL default \'\',
            `templateName` varchar(20) NOT NULL default \'\',
            KEY `key_name` (`key_name`)
        ) TYPE=MyISAM;';

        mysql_query($sql) or die(mysql_error());

        // Add type field, if needed:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "cats LIMIT 1";
        $result = mysql_query($sql) or die(mysql_error());

        $i = 0; $bFound = false;
        while ($i < mysql_num_fields($result)) {
            $meta = mysql_fetch_field($result, $i);
            if ($meta->name == 'templateName') {
                $bFound = true;
                break;
            }

            $i++;
        }

        if ($bFound !== true) {
            $sql = "ALTER TABLE " . $CONFIG['tblPrefix'] . "cats ADD `templateName` VARCHAR(20) NOT NULL";
            mysql_query($sql) or die(mysql_error());
        }

        $message = 'Category table updated...<br />';
        $url = 'update.php?stage=2';

        outputStage($message, $url);
    }

    function stageTwo() {
        global $CONFIG;

        ///////////////////////////////////////////////////////////
        // Table structure for table comments
        $sql = 'CREATE TABLE IF NOT EXISTS `' . $CONFIG['tblPrefix'] . 'comments` (
            `id` int(11) NOT NULL auto_increment,
            `par_id` int(11) NOT NULL default \'0\',
            `uID` int(4) NOT NULL default \'0\',
            `postedOn` datetime NOT NULL default \'0000-00-00 00:00:00\',
            `subject` varchar(100) NOT NULL default \'\',
            `body` mediumtext NOT NULL,
            `email` varchar(50) NOT NULL default \'\',
            PRIMARY KEY  (`id`),
            UNIQUE KEY `id` (`id`)
        ) TYPE=MyISAM;';
        
        mysql_query($sql) or die(mysql_error());

        $message = 'Category table updated.<br />
        Comments table updated...<br />';
        $url = 'update.php?stage=3';

        outputStage($message, $url);
    }

    function stageThree() {
        global $CONFIG;

        // Drop ID field in config (if needed):
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "config LIMIT 1";
        $fields = mysql_query($sql) or die(mysql_error());

        $i = 0;
        while ($i < mysql_num_fields($fields)) {
            $meta = mysql_fetch_field($fields, $i);

            if ($meta->name == 'id') {
                $sql = "ALTER TABLE " . $CONFIG['tblPrefix'] . "config DROP id";
                mysql_query($sql) or die(mysql_error());

                break;
            }

            $i++;
        }

        // Change arcdelete to oldnews, if needed:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "config WHERE name='arcdelete'";
        $configs = mysql_query($sql) or die(mysql_error());
        if ($config = mysql_fetch_array($configs)) {
            $sql = "UPDATE " . $CONFIG['tblPrefix'] . "config SET name='oldnews' WHERE name='arcdelete'";
            mysql_query($sql) or die(mysql_error());
        }

        $configArray = array('adminmail' => '', 'allowcomments' => 'no', 'banips' => 'no', 'enablecats' => 'no', 'maxcomlen' => '2000', 'maxlen' => '4096', 'max_subject_len' => '75', 'numcomments' => '20');

        while (list($key, $val) = each($configArray)) {
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "config WHERE name='$key'";
            $configs = mysql_query($sql) or die(mysql_error());

            if (mysql_fetch_array($configs) === false) {
                $sql = "INSERT INTO " . $CONFIG['tblPrefix'] . "config (name, value) VALUES ('$key', '$val')";
                mysql_query($sql) or die(mysql_error());
            }
        }

        $message = 'Category table updated.<br />
        Comments table updated.<br />
        Config table updated...<br />';
        $url = 'update.php?stage=4';

        outputStage($message, $url);
    }

    function stageFour() {
        global $CONFIG;

        // Change news.subject length, if necessary:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news LIMIT 1";
        $fields = mysql_query($sql) or die(mysql_error());

        $i = 0;
        while ($i < mysql_num_fields($fields)) {
            $meta = mysql_fetch_field($fields, $i);

            // Update the subject field (if needed):
            if ($meta->name == 'subject') {
                $iLen = mysql_field_len($fields, $i);

                if ($iLen != 255) {
                    $sql = "ALTER TABLE " . $CONFIG['tblPrefix'] . "news CHANGE `subject` `subject` VARCHAR(255) NOT NULL";
                    mysql_query($sql) or die(mysql_error());
                }
            }

            // Update the postedBy field to uID:
            if ($meta->name == 'postedBy') {
                // Loop the news:
                $sql = "SELECT id, postedBy FROM " . $CONFIG['tblPrefix'] . "news";
                mysql_query($sql) or die(mysql_error());

                $items = mysql_query($sql) or die(mysql_error());
                if ($item = mysql_fetch_array($items)) {
                    do {
                        $sql = "SELECT id FROM " . $CONFIG['tblPrefix'] . "users WHERE usn='" . $item['postedBy'] . "'";
                        mysql_query($sql) or die(mysql_error());

                        $users = mysql_query($sql) or die(mysql_error());
                        if ($user = mysql_fetch_array($users)) {
                            $sql = "UPDATE " . $CONFIG['tblPrefix'] . "news SET postedBy='" . $user['id'] . "' WHERE id=" . $item['id'];
                            mysql_query($sql) or die(mysql_error());
                        }
                    } while ($item = mysql_fetch_array($items));
                }

                $sql = "ALTER TABLE " . $CONFIG['tblPrefix'] . "news CHANGE `postedBy` `uID` INT(4) NOT NULL";
                mysql_query($sql) or die(mysql_error());
            }

            $i++;
        }

        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "news LIMIT 1";
        $items = mysql_query($sql) or die(mysql_error());
        if ($item = mysql_fetch_array($items)) {
            if (array_key_exists('cat_name', $item) !== true) {
                $sql = "ALTER TABLE " . $CONFIG['tblPrefix'] . "news ADD `cat_name` VARCHAR(15) NOT NULL";
                mysql_query($sql) or die(mysql_error());
            }
        }

        $message = 'Category table updated.<br />
        Comments table updated.<br />
        Config table updated.<br />
        News table updated...<br />';
        $url = 'update.php?stage=5';

        outputStage($message, $url);
    }

    function stageFive() {
        global $CONFIG;

        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "users LIMIT 1";
        $users = mysql_query($sql) or die(mysql_error());
        if ($user = mysql_fetch_array($users)) {
            if (array_key_exists('cat_restrictions', $user) !== true) {
                $sql = "ALTER TABLE " . $CONFIG['tblPrefix'] . "users ADD `cat_restrictions` VARCHAR(255) DEFAULT 'all' NOT NULL";
                mysql_query($sql) or die(mysql_error());
            }

            if (array_key_exists('hidemail', $user) !== true) {
                $sql = "ALTER TABLE " . $CONFIG['tblPrefix'] . "users ADD `hidemail` CHAR(3) DEFAULT 'no' NOT NULL";
                mysql_query($sql) or die(mysql_error());
            }

            if (array_key_exists('pass_reset', $user) !== true) {
                $sql = "ALTER TABLE " . $CONFIG['tblPrefix'] . "users ADD `pass_reset` VARCHAR(8) NOT NULL";
                mysql_query($sql) or die(mysql_error());
            }

            if (array_key_exists('lastIP', $user) !== true) {
                $sql = "ALTER TABLE " . $CONFIG['tblPrefix'] . "users ADD `lastIP` VARCHAR(15) DEFAULT '000.000.000.000' NOT NULL";
                mysql_query($sql) or die(mysql_error());
            }
        }

        $message = 'Category table updated.<br />
        Comments table updated.<br />
        Config table updated.<br />
        News table updated.<br />
        Users table updated...<br />';
        $url = 'update.php?stage=6';

        outputStage($message, $url);
    }

    function stageSix() {
        global $CONFIG;

        ///////////////////////////////////////////////////////////
        // Table structure for table templates
        $sql = 'CREATE TABLE IF NOT EXISTS `' . $CONFIG['tblPrefix'] . 'templates` (
            `name` varchar(20) NOT NULL default \'\',
            `template` longtext NOT NULL,
            `comments` mediumtext NOT NULL,
            `type` varchar(6) NOT NULL default \'system\',
            KEY `name` (`name`)
        ) TYPE=MyISAM;';

        mysql_query($sql) or die(mysql_error());

        // Add type field, if needed:
        $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "templates LIMIT 1";
        $result = mysql_query($sql) or die(mysql_error());

        $i = 0; $bFound = false;
        while ($i < mysql_num_fields($result)) {
            $meta = mysql_fetch_field($result, $i);
            if ($meta->name == 'type') {
                $bFound = true;
                break;
            }

            $i++;
        }

        if ($bFound !== true) {
            $sql = "ALTER TABLE " . $CONFIG['tblPrefix'] . "templates ADD `type` VARCHAR(6) DEFAULT 'system' NOT NULL";
            mysql_query($sql) or die(mysql_error());
        }

        // Update Templates: 
        $template[] = array('news', '<table cellpadding=\\"2\\" cellspacing=\\"0\\" style=\\"margin-left: auto; margin-right: auto; width: 500px;\\">\r\n    <tr>\r\n        <td style=\\"background-color: #ffcc33; border: 1px solid #000000;\\">\r\n            <b><subject></b><br />\r\n            <span style=\\"font-size: 8pt;\\">Posted on <datetime> by <b><user></b></span>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style=\\"background-color: #efefef; border: 1px solid #000000; border-top: none;\\">\r\n            <item>\r\n            <br /><br />\r\n            <div style=\\"text-align: right;\\">\r\n                <a href=\\"?action=comments&id=<id>\\">Comments: (<num>)</a>\r\n            </div>\r\n        </td>\r\n    </tr>\r\n</table><br />', 'The news template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;id&gt; - Replaced with ID.</li>\r\n<li>&lt;subject&gt; - Replaced with subject.</li>\r\n<li>&lt;datetime&gt; - Replaced with date and time information.</li>\r\n<li>&lt;user&gt; - Replaced with user information.</li>\r\n<li>&lt;item&gt; - Replaced with news item.</li>\r\n<li>&lt;num&gt; - Replaced with the number of comments.</li></ul>\r\n<br />\r\n<b>The Comments Link</b>:<br />\r\nTo link to the comments page, the general link is:<br /><br />\r\n&lt;a href="?action=comments&id=&lt;id&gt;">Comments: &lt;num&gt;&lt;/a&gt;<br /><br />    \r\nIf you want your comments to be shown on another page, such as comments.php, you would do something like:<br /><br />\r\n&lt;a href="comment_page.php?action=comments&id=&lt;id&gt;">Comments: &lt;num&gt;&lt;/a&gt;', 'system');
        
        $template[] = array('header', '<span style=\\"font-size: 9px;\\">\r\n    <a href=\\"#<id>\\"><subject></a> by <user>\r\n</span><br />', 'The header template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;id&gt; - Replaced with ID.</li>\r\n<li>&lt;subject&gt; - Replaced with subject.</li>\r\n<li>&lt;user&gt; - Replaced with user information.</li></ul><br />\r\nThe link part of the template is required if you want your visitors to be able to navigate to the news item from the header.', 'system');

        
        $template[] = array('archives', '<table cellpadding=\\"2\\" cellspacing=\\"0\\" style=\\"margin-left: auto; margin-right: auto; width: 500px;\\">\r\n    <tr>\r\n        <td style=\\"background-color: #ffcc33; border: 1px solid #000000;\\">\r\n            <b>News Archives</b>\r\n        </td>\r\n    </tr>\r\n    <archives_listing>\r\n</table>', 'The archives template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;archive_listing&gt; - Replaced with all archive listings.</li></ul><br />\r\nThis is one of the more confusing templates. It is simply the shell that each archive month\\\'s information is placed into.', 'system');

        
        $template[] = array('archiveitem', '<tr>\r\n    <td>\r\n        <archive_text>\r\n    </td>\r\n</tr>', 'The archiveitem template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;archive_text&gt; - Replaced with archive information.</li></ul><br />\r\nThis is one of the more confusing templates. This template is placed <i>within</i> the archive listing template for <i>each</i> archived month.', 'system');

        
        $template[] = array('comments', '<table cellpadding=\\"2\\" cellspacing=\\"0\\" style=\\"margin-left: auto; margin-right: auto; width: 500px;\\">\r\n    <tr>\r\n        <td style=\\"background-color: #ffcc33; border: 1px solid #000000;\\">\r\n            <b><subject></b><br>\r\n            <span style=\\"font-size: 8pt;\\">Posted on <datetime> by <b><user></b></span>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style=\\"background-color: #efefef; border: 1px solid #000000; border-top: none;\\">\r\n            <item>\r\n        </td>\r\n    </tr>\r\n</table><br />', 'The comments template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;id&gt; - Replaced with comment ID.</li>\r\n<li>&lt;subject&gt; - Replaced with comment subject.</li>\r\n<li>&lt;datetime&gt; - Replaced with date and time information.</li>\r\n<li>&lt;user&gt; - Replaced with user information.</li>\r\n<li>&lt;item&gt; - Replaced with comment item.</li></ul>', 'system');

        $template[] = array('comments_form', '<table align="center" style="width: 300px;">\r\n    <tr>\r\n        <td colspan="2"><b>Post a Comment</b>: <options> | <logout><hr>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 30%;"><b>Name</b>:</td>\r\n        <td style="width: 70%;"><b><user></b></td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 30%;"><b>Subject</b>:</td>\r\n        <td style="width: 70%;">\r\n            <input type="text" name="c_subject" style="width: 100%;" value="<subject>" maxlength="30">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td colspan="2"><hr><b>Mesage</b>:</td>\r\n    </tr>\r\n    <tr>\r\n        <td colspan="2"><textarea name="c_message" style="width: 100%; height: 150px;"><message></textarea></td>\r\n    </tr>\r\n    <tr>\r\n        <td align="center" colspan="2">\r\n            <input type="reset" value=" Clear "> <input type="submit" value=" Submit ">\r\n        </td>\r\n    </tr>\r\n</table>', 'The comments form template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;options&gt; - Replaced with options link.</li>\r\n<li>&lt;logout&gt; - Replaced with logout link.</li>\r\n<li>&lt;user&gt; - Replaced with user information.</li>\r\n<li>&lt;subject&gt; - Replaced with comment subject.</li>\r\n<li>&lt;message&gt; - Replaced with comment item.</li></ul>\r\n<br />\r\nThese are the form fields that are required to post a comment: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>c_subject (input text)<br />\r\n    <i>&lt;input type="text" name="c_subject" maxlength="30"&gt;</i>\r\n</li>\r\n<li>c_message (textarea><br />\r\n    <i>&lt;textarea name="c_message"&gt;&lt;/textarea&gt;</i>\r\n</li>\r\n<li>submit (input submit)<br />\r\n    <i>&lt;input type="submit" value=" Submit "&gt;</i>\r\n</li></ul>\r\n<br />\r\nYou may change the style and properties of these fields, however, if any of them are missing, the form will not work.', 'system');

        $template[] = array('comments_options', '<table align="center" style="width: 300px;">\r\n    <tr>\r\n        <td colspan="2">User Options for <b><user></b>:<hr></td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 35%;"><b>Password</b>:</td>\r\n        <td style="width: 65%;">\r\n            <input type="password" name="c_pass" style="width: 100%;" maxlength="16">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 35%;"><b>Confirm</b>:</td>\r\n        <td style="width: 65%;">\r\n        <input type="password" name="c_confirmpass" style="width: 100%;" maxlength="16">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 35%;"><b>Email</b>*:</td>\r\n        <td style="width: 65%;">\r\n        <input type="text" name="c_email" style="width: 100%;" value="<email>" maxlength="50">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 35%;">\r\n        <b>Hide Email?</b>*\r\n        </td>\r\n        <td style="width: 65%;"><hidemail></td>\r\n    </tr>\r\n    <tr>\r\n        <td colspan="2">\r\n        <br>* Indicates required field.\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td align="center" colspan="2"><hr>\r\n        <input type="submit" value=" Save ">\r\n        </td>\r\n    </tr>\r\n</table>', 'The comments options template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;user&gt; - Replaced with user information.</li>\r\n<li>&lt;email&gt; - Replaced with user\\\'s email.</li>\r\n<li>&lt;hidemail&gt; - Replaced with the hidemail option boxes.</li></ul>\r\n<br />\r\nThese are the form fields that are required to post a comment: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>c_pass (input password)<br />\r\n    <i>&lt;input type="password" name="c_pass" maxlength="16"&gt;</i>\r\n</li>\r\n<li>c_confirmpass (input password)<br />\r\n    <i>&lt;input type="password" name="c_confirmpass" maxlength="16"&gt;</i>\r\n</li>\r\n<li>c_email (input text)<br />\r\n    <i>&lt;input type="text" name="c_email" maxlength="50"&gt;</i>\r\n</li>\r\n<li>submit (input submit)<br />\r\n    <i>&lt;input type="submit" value=" Save "&gt;</i>\r\n</li></ul>\r\n<br />\r\nYou may change the style and properties of these fields, however, if any of them are missing, the form will not work.', 'system');

        
        $template[] = array('display_error', '<b>An error has occurred in <i>display.php</i></b>:<br /><error>', 'The display error template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;error&gt; - Replaced with the error message.</li></ul>', 'system');

        $template[] = array('comments_register', '<table align="center" style="width: 300px;">\r\n    <tr>\r\n        <td colspan="2"><b>Comments Registration</b><hr></td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 40%;"><b>Name</b>:</td>\r\n        <td style="width: 60%;">\r\n            <input type="text" name="c_name" style="width: 100%;" maxlength="16">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 40%;"><b>Email</b>:</td>\r\n        <td style="width: 60%;">\r\n            <input type="text" name="c_email" style="width: 100%;" maxlength="50">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 40%;"><b>Confirm Email</b>:</td>\r\n        <td style="width: 60%;">\r\n            <input type="text" name="cc_email" style="width: 100%;" maxlength="50">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td align="center" colspan="2">\r\n            <hr><input type="reset" value=" Clear "> <input type="submit" value=" Signup ">\r\n        </td>\r\n    </tr>\r\n</table>', 'The comments registration template has no needed tags.<br /><br />\r\nThese are the form fields that are required to post a comment: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>c_name (input text)<br />\r\n    <i>&lt;input type="text" name="c_name" maxlength="16"&gt;</i>\r\n</li>\r\n<li>c_email (input text)<br />\r\n    <i>&lt;input type="text" name="c_email" maxlength="50"&gt;</i>\r\n</li>\r\n<li>cc_email (input text)<br />\r\n    <i>&lt;input type="text" name="cc_email" maxlength="50"&gt;</i>\r\n</li>\r\n<li>submit (input submit)<br />\r\n    <i>&lt;input type="submit" value=" Signup "&gt;</i>\r\n</li></ul>\r\n<br />\r\nYou may change the style and properties of these fields, however, if any of them are missing, the form will not work.', 'system');

        $template[] = array('comments_login', '<table align="center" style="width: 300px;">\r\n    <tr>\r\n        <td colspan="2"><b>Comments Login</b><hr></td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 40%;"><b>Name</b>:</td>\r\n        <td style="width: 60%;">\r\n            <input type="text" name="c_name" style="width: 100%;" maxlength="16">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 40%;"><b>Password</b>:</td>\r\n        <td style="width: 60%;">\r\n            <input type="password" name="c_password" style="width: 100%;">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td align="center" colspan="2">\r\n            <hr><input type="reset" value=" Clear "> <input type="submit" value=" Login ">\r\n        </td>\r\n    </tr>\r\n</table>', 'The comments login template has no needed tags.<br /><br />\r\nThese are the form fields that are required to post a comment: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>c_name (input text)<br />\r\n    <i>&lt;input type="text" name="c_name" maxlength="16"&gt;</i>\r\n</li>\r\n<li>c_password (input password)<br />\r\n    <i>&lt;input type="password" name="c_password" maxlength="50"&gt;</i>\r\n</li>\r\n<li>submit (input submit)<br />\r\n    <i>&lt;input type="submit" value=" Signup "&gt;</i>\r\n</li></ul>\r\n<br />\r\nYou may change the style and properties of these fields, however, if any of them are missing, the form will not work.', 'system');

        $template[] = array('comments_message', '<message>', 'The comments message template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;message&gt; - Replaced with the comment message.</li>\r\n</ul>', 'system');

        $template[] = array('pagelinks', '<div style="text-align: center;"><links></div>', 'The pagelinks template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;links&gt; - Replaced with numeric links.</li></ul><br />', 'system');

        for ($i = 0; $i < count($template); $i++) {
            $sql = "SELECT * FROM " . $CONFIG['tblPrefix'] . "templates WHERE name='" . $template[$i][0] . "'";
            $ts = mysql_query($sql) or die(mysql_error());

            if (mysql_fetch_array($ts) === false) {
                $sql = "INSERT INTO " . $CONFIG['tblPrefix'] . "templates (name, template, comments, type) VALUES ";
                $sql .= "('" . $template[$i][0] . "', '" . $template[$i][1] . "', '" . $template[$i][2] . "', '" . $template[$i][3] . "')";

                mysql_query($sql) or die(mysql_error() . $sql);
            }
        }

        $message = 'Category table updated.<br />
        Comments table updated.<br />
        Config table updated.<br />
        News table updated.<br />
        Users table updated.<br />
        Templates table updated...<br />';
        $url = 'update.php?stage=7';

        outputStage($message, $url);
    }

    function completion() {
        $message = 'Category table updated.<br />
        Comments table updated.<br />
        Config table updated.<br />
        News table updated.<br />
        Users table updated.<br />
        Templates table updated.<br /><br />
        <i>vbsNews</i> has been updated successfully. It is recommended that you delete this file.<br /><br />
        Thanks for using <i>vbsNews</i>!';

        outputStage($message);
    }

    function outputStage($content, $url = '') {
        echo '<html>
        <head>
            <title>vbsNews Update Script</title>
            <style type="text/css">
                body {
                    font-family: verdana, arial, serif;
                    font-size: 10pt;
                }

                .main {
                    background-color: #f9f9f9;
                    border: 1px solid #d6d6d6;

                    margin-left: auto; 
                    margin-right: auto;

                    padding: 8px;

                    text-align: left; 
                    width: 400px;
                }
            </style>';
        if ($url != '') {
            echo '<meta http-equiv="refresh" content="3; url=' . $url . '" />';
        }

        echo '</head>
        <body style="text-align: center;">
            <div class="main">
                <span style="font-size: 14pt; font-weight: bold;">
                    vbsNews Update Script
                </span>
                <br /><br />' . $content . '<br />
            </div>
        </body>
        </html>';
    } 
?>