<?php

    $sql = array();

    ///////////////////////////////////////////////////////////
    // Table structure for table cats
    $sql[] = 'CREATE TABLE `' . $CONFIG['tblPrefix'] . 'cats` (
        `key_name` varchar(15) NOT NULL default \'\',
        `name` varchar(25) NOT NULL default \'\',
        `templateName` varchar(20) NOT NULL default \'\',
        KEY `key_name` (`key_name`)
    ) TYPE=MyISAM;';

    ///////////////////////////////////////////////////////////
    // Table structure for table comments
    $sql[] = 'CREATE TABLE `' . $CONFIG['tblPrefix'] . 'comments` (
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

    ///////////////////////////////////////////////////////////
    // Table structure for table `config`
    $sql[] = 'CREATE TABLE `' . $CONFIG['tblPrefix'] . 'config` (
        `name` varchar(16) NOT NULL default \'\',
        `value` varchar(255) NOT NULL default \'\',
        KEY `name` (`name`)
    ) TYPE=MyISAM;';

    ///////////////////////////////////////////////////////////
    // Table structure for table news
    $sql[] = 'CREATE TABLE `' . $CONFIG['tblPrefix'] . 'news` (
        `id` int(11) NOT NULL auto_increment,
        `subject` varchar(30) NOT NULL default \'\',
        `body` text NOT NULL,
        `postedOn` datetime NOT NULL default \'0000-00-00 00:00:00\',
        `uID` int(4) NOT NULL default \'0\',
        `cat_name` varchar(15) NOT NULL default \'0\',
        PRIMARY KEY  (`id`),
        UNIQUE KEY `id` (`id`)
    ) TYPE=MyISAM;';

    ///////////////////////////////////////////////////////////
    // Table structure for table templates
    $sql[] = 'CREATE TABLE `' . $CONFIG['tblPrefix'] . 'templates` (
        `name` varchar(20) NOT NULL default \'\',
        `template` longtext NOT NULL,
        `comments` mediumtext NOT NULL,
        `type` varchar(6) NOT NULL default \'system\',
        KEY `name` (`name`)
    ) TYPE=MyISAM;';

    ///////////////////////////////////////////////////////////
    // Table structure for table users
    $sql[] = 'CREATE TABLE `' . $CONFIG['tblPrefix'] . 'users` (
        `id` int(4) NOT NULL auto_increment,
        `usn` varchar(16) NOT NULL default \'\',
        `password` varchar(32) NOT NULL default \'\',
        `email` varchar(50) NOT NULL default \'\',
        `up` tinyint(1) NOT NULL default \'0\',
        `cat_restrictions` varchar(255) NOT NULL default \'all\',
        `hidemail` char(3) NOT NULL default \'no\',
        `pass_reset` varchar(8) NOT NULL default \'\',
        `lastIP` varchar(15) NOT NULL default \'000.000.000.000\',
        PRIMARY KEY  (`id`),
        UNIQUE KEY `id` (`id`)
    ) TYPE=MyISAM;';

    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'sitename\', \'\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'scriptdir\', \'\');';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'adminmail\', \'\');';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'shownews\', \'all\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'numitems\', \'10\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'oldnews\', \'archive\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'timeformat\', \'F jS, Y \\a\\t g:i A\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'maxlen\', \'4096\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'enablecats\', \'no\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'allowhtml\', \'no\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'allowtags\', \'yes\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'allowimages\', \'yes\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'allowcomments\', \'yes\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'numcomments\', \'20\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'maxcomlen\', \'2000\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'banips\', \'no\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'modifyusn\', \'no\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'config` VALUES (\'max_subject_len\', \'75\')';


    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'templates` VALUES (\'news\', \'<table cellpadding=\\"2\\" cellspacing=\\"0\\" style=\\"margin-left: auto; margin-right: auto; width: 500px;\\">\r\n    <tr>\r\n        <td style=\\"background-color: #ffcc33; border: 1px solid #000000;\\">\r\n            <b><subject></b><br />\r\n            <span style=\\"font-size: 8pt;\\">Posted on <datetime> by <b><user></b></span>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style=\\"background-color: #efefef; border: 1px solid #000000; border-top: none;\\">\r\n            <item>\r\n            <br /><br />\r\n            <div style=\\"text-align: right;\\">\r\n                <a href=\\"?action=comments&id=<id>\\">Comments: (<num>)</a>\r\n            </div>\r\n        </td>\r\n    </tr>\r\n</table><br />\', \'The news template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;id&gt; - Replaced with ID.</li>\r\n<li>&lt;subject&gt; - Replaced with subject.</li>\r\n<li>&lt;datetime&gt; - Replaced with date and time information.</li>\r\n<li>&lt;user&gt; - Replaced with user information.</li>\r\n<li>&lt;item&gt; - Replaced with news item.</li>\r\n<li>&lt;num&gt; - Replaced with the number of comments.</li></ul>\r\n<br />\r\n<b>The Comments Link</b>:<br />\r\nTo link to the comments page, the general link is:<br /><br />\r\n&lt;a href="?action=comments&id=&lt;id&gt;">Comments: &lt;num&gt;&lt;/a&gt;<br /><br />    \r\nIf you want your comments to be shown on another page, such as comments.php, you would do something like:<br /><br />\r\n&lt;a href="comment_page.php?action=comments&id=&lt;id&gt;">Comments: &lt;num&gt;&lt;/a&gt;\', \'system\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'templates` VALUES (\'header\', \'<span style=\\"font-size: 9px;\\">\r\n    <a href=\\"#<id>\\"><subject></a> by <user>\r\n</span><br />\', \'The header template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;id&gt; - Replaced with ID.</li>\r\n<li>&lt;subject&gt; - Replaced with subject.</li>\r\n<li>&lt;user&gt; - Replaced with user information.</li></ul><br />\r\nThe link part of the template is required if you want your visitors to be able to navigate to the news item from the header.\', \'system\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'templates` VALUES (\'archives\', \'<table cellpadding=\\"2\\" cellspacing=\\"0\\" style=\\"margin-left: auto; margin-right: auto; width: 500px;\\">\r\n    <tr>\r\n        <td style=\\"background-color: #ffcc33; border: 1px solid #000000;\\">\r\n            <b>News Archives</b>\r\n        </td>\r\n    </tr>\r\n    <archives_listing>\r\n</table>\', \'The archives template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;archive_listing&gt; - Replaced with all archive listings.</li></ul><br />\r\nThis is one of the more confusing templates. It is simply the shell that each archive month\\\'s information is placed into.\', \'system\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'templates` VALUES (\'archiveitem\', \'<tr>\r\n    <td>\r\n        <archive_text>\r\n    </td>\r\n</tr>\', \'The archiveitem template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;archive_text&gt; - Replaced with archive information.</li></ul><br />\r\nThis is one of the more confusing templates. This template is placed <i>within</i> the archive listing template for <i>each</i> archived month.\', \'system\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'templates` VALUES (\'comments\', \'<table cellpadding=\\"2\\" cellspacing=\\"0\\" style=\\"margin-left: auto; margin-right: auto; width: 500px;\\">\r\n    <tr>\r\n        <td style=\\"background-color: #ffcc33; border: 1px solid #000000;\\">\r\n            <b><subject></b><br>\r\n            <span style=\\"font-size: 8pt;\\">Posted on <datetime> by <b><user></b></span>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style=\\"background-color: #efefef; border: 1px solid #000000; border-top: none;\\">\r\n            <item>\r\n        </td>\r\n    </tr>\r\n</table><br />\', \'The comments template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;id&gt; - Replaced with comment ID.</li>\r\n<li>&lt;subject&gt; - Replaced with comment subject.</li>\r\n<li>&lt;datetime&gt; - Replaced with date and time information.</li>\r\n<li>&lt;user&gt; - Replaced with user information.</li>\r\n<li>&lt;item&gt; - Replaced with comment item.</li></ul>\', \'system\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'templates` VALUES (\'comments_form\', \'<table align="center" style="width: 300px;">\r\n    <tr>\r\n        <td colspan="2"><b>Post a Comment</b>: <options> | <logout><hr>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 30%;"><b>Name</b>:</td>\r\n        <td style="width: 70%;"><b><user></b></td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 30%;"><b>Subject</b>:</td>\r\n        <td style="width: 70%;">\r\n            <input type="text" name="c_subject" style="width: 100%;" value="<subject>" maxlength="30">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td colspan="2"><hr><b>Mesage</b>:</td>\r\n    </tr>\r\n    <tr>\r\n        <td colspan="2"><textarea name="c_message" style="width: 100%; height: 150px;"><message></textarea></td>\r\n    </tr>\r\n    <tr>\r\n        <td align="center" colspan="2">\r\n            <input type="reset" value=" Clear "> <input type="submit" value=" Submit ">\r\n        </td>\r\n    </tr>\r\n</table>\', \'The comments form template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;options&gt; - Replaced with options link.</li>\r\n<li>&lt;logout&gt; - Replaced with logout link.</li>\r\n<li>&lt;user&gt; - Replaced with user information.</li>\r\n<li>&lt;subject&gt; - Replaced with comment subject.</li>\r\n<li>&lt;message&gt; - Replaced with comment item.</li></ul>\r\n<br />\r\nThese are the form fields that are required to post a comment: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>c_subject (input text)<br />\r\n    <i>&lt;input type="text" name="c_subject" maxlength="30"&gt;</i>\r\n</li>\r\n<li>c_message (textarea><br />\r\n    <i>&lt;textarea name="c_message"&gt;&lt;/textarea&gt;</i>\r\n</li>\r\n<li>submit (input submit)<br />\r\n    <i>&lt;input type="submit" value=" Submit "&gt;</i>\r\n</li></ul>\r\n<br />\r\nYou may change the style and properties of these fields, however, if any of them are missing, the form will not work. \', \'system\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'templates` VALUES (\'comments_options\', \'<table align="center" style="width: 300px;">\r\n    <tr>\r\n        <td colspan="2">User Options for <b><user></b>:<hr></td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 35%;"><b>Password</b>:</td>\r\n        <td style="width: 65%;">\r\n            <input type="password" name="c_pass" style="width: 100%;" maxlength="16">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 35%;"><b>Confirm</b>:</td>\r\n        <td style="width: 65%;">\r\n        <input type="password" name="c_confirmpass" style="width: 100%;" maxlength="16">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 35%;"><b>Email</b>*:</td>\r\n        <td style="width: 65%;">\r\n        <input type="text" name="c_email" style="width: 100%;" value="<email>" maxlength="50">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 35%;">\r\n        <b>Hide Email?</b>*\r\n        </td>\r\n        <td style="width: 65%;"><hidemail></td>\r\n    </tr>\r\n    <tr>\r\n        <td colspan="2">\r\n        <br>* Indicates required field.\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td align="center" colspan="2"><hr>\r\n        <input type="submit" value=" Save ">\r\n        </td>\r\n    </tr>\r\n</table>\', \'The comments options template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;user&gt; - Replaced with user information.</li>\r\n<li>&lt;email&gt; - Replaced with user\\\'s email.</li>\r\n<li>&lt;hidemail&gt; - Replaced with the hidemail option boxes.</li></ul>\r\n<br />\r\nThese are the form fields that are required to post a comment: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>c_pass (input password)<br />\r\n    <i>&lt;input type="password" name="c_pass" maxlength="16"&gt;</i>\r\n</li>\r\n<li>c_confirmpass (input password)<br />\r\n    <i>&lt;input type="password" name="c_confirmpass" maxlength="16"&gt;</i>\r\n</li>\r\n<li>c_email (input text)<br />\r\n    <i>&lt;input type="text" name="c_email" maxlength="50"&gt;</i>\r\n</li>\r\n<li>submit (input submit)<br />\r\n    <i>&lt;input type="submit" value=" Save "&gt;</i>\r\n</li></ul>\r\n<br />\r\nYou may change the style and properties of these fields, however, if any of them are missing, the form will not work. \', \'system\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'templates` VALUES (\'display_error\', \'<b>An error has occurred in <i>display.php</i></b>:<br /><error>\', \'The display error template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;error&gt; - Replaced with the error message.</li></ul>\', \'system\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'templates` VALUES (\'comments_register\', \'<table align="center" style="width: 300px;">\r\n    <tr>\r\n        <td colspan="2"><b>Comments Registration</b><hr></td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 40%;"><b>Name</b>:</td>\r\n        <td style="width: 60%;">\r\n            <input type="text" name="c_name" style="width: 100%;" maxlength="16">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 40%;"><b>Email</b>:</td>\r\n        <td style="width: 60%;">\r\n            <input type="text" name="c_email" style="width: 100%;" maxlength="50">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 40%;"><b>Confirm Email</b>:</td>\r\n        <td style="width: 60%;">\r\n            <input type="text" name="cc_email" style="width: 100%;" maxlength="50">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td align="center" colspan="2">\r\n            <hr><input type="reset" value=" Clear "> <input type="submit" value=" Signup ">\r\n        </td>\r\n    </tr>\r\n</table>\', \'The comments registration template has no needed tags.<br /><br />\r\nThese are the form fields that are required to post a comment: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>c_name (input text)<br />\r\n    <i>&lt;input type="text" name="c_name" maxlength="16"&gt;</i>\r\n</li>\r\n<li>c_email (input text)<br />\r\n    <i>&lt;input type="text" name="c_email" maxlength="50"&gt;</i>\r\n</li>\r\n<li>cc_email (input text)<br />\r\n    <i>&lt;input type="text" name="cc_email" maxlength="50"&gt;</i>\r\n</li>\r\n<li>submit (input submit)<br />\r\n    <i>&lt;input type="submit" value=" Signup "&gt;</i>\r\n</li></ul>\r\n<br />\r\nYou may change the style and properties of these fields, however, if any of them are missing, the form will not work.\', \'system\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'templates` VALUES (\'comments_login\', \'<table align="center" style="width: 300px;">\r\n    <tr>\r\n        <td colspan="2"><b>Comments Login</b><hr></td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 40%;"><b>Name</b>:</td>\r\n        <td style="width: 60%;">\r\n            <input type="text" name="c_name" style="width: 100%;" maxlength="16">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td style="width: 40%;"><b>Password</b>:</td>\r\n        <td style="width: 60%;">\r\n            <input type="password" name="c_password" style="width: 100%;">\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td align="center" colspan="2">\r\n            <hr><input type="reset" value=" Clear "> <input type="submit" value=" Login ">\r\n        </td>\r\n    </tr>\r\n</table>\', \'The comments login template has no needed tags.<br /><br />\r\nThese are the form fields that are required to post a comment: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>c_name (input text)<br />\r\n    <i>&lt;input type="text" name="c_name" maxlength="16"&gt;</i>\r\n</li>\r\n<li>c_password (input password)<br />\r\n    <i>&lt;input type="password" name="c_password" maxlength="50"&gt;</i>\r\n</li>\r\n<li>submit (input submit)<br />\r\n    <i>&lt;input type="submit" value=" Signup "&gt;</i>\r\n</li></ul>\r\n<br />\r\nYou may change the style and properties of these fields, however, if any of them are missing, the form will not work.\', \'system\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'templates` VALUES (\'comments_message\', \'<message>\', \'The comments message template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;message&gt; - Replaced with the comment message.</li>\r\n</ul>\', \'system\')';
    $sql[] = 'INSERT INTO `' . $CONFIG['tblPrefix'] . 'templates` VALUES (\'pagelinks\', \'<div style="text-align: center;"><links></div>\', \'The pagelinks template has the following tags available: <br /><br />\r\n<ul style="margin-top: 0px; margin-bottom: 0px;">\r\n<li>&lt;links&gt; - Replaced with numeric links.</li></ul><br />\', \'system\')';
    

?>