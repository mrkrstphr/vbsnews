<?php

    ////////////////////////////////////////////////////////////////////////////
    // DESCRIPTION:
    // settings.php - System settings functions
    ////////////////////////////////////////////////////////////////////////////
    // WARNING:
    // It is not recommended to modify anything below this line!
    ////////////////////////////////////////////////////////////////////////////
    include_once 'config.php';
    include_once 'functions/function.getdefault.php';

    ////////////////////////////////////////////////////////////////////////////
    // SETTINGSMAIN - Main settings function, figure out what to do:
    ////////////////////////////////////////////////////////////////////////////
    function settingsmain() {
        if (isset($_REQUEST['s'])) {
            switch($_REQUEST['s']) {
                case 'save':
                    savesettings(); break;
                default:
                    changesettings();
            }
        } else {
            changesettings();
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // CHANGESETTINGS - Display script settings for modification:
    ////////////////////////////////////////////////////////////////////////////
    function changesettings() {
        global $CONFIG;
        
        if ($CONFIG['up'] == 1) {
            $jsFiles = 'js_settings.js';
            $title = $CONFIG['sitename'] . ' - Change Settings';
            include 'header.html';

            // Display settings form
            echo '<form action="' . $CONFIG['scriptdir'] . 'index.php" method="post" style="margin: 0px;">
            <input type="hidden" name="action" value="settings" />
            <input type="hidden" name="s" value="save" />
            <table cellpadding="2" cellspacing="0" style="margin-left: auto; margin-right: auto; width: 555px;">';

            ////////////////////////////////////////////////////////////////////////////
            // SITE SETTINGS
            ////////////////////////////////////////////////////////////////////////////
            echo '<tr>
                    <td class="bars" colspan="3">
                        <b>Site Settings</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <b>Note</b>: Changing the <i>News Script Directory</i> setting can
                        cause vbsNews to not function properly. It is recommended that if you need to change the location
                        of the script, you make sure a copy exists in the new location beforehand.<br /><br />
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'sitename\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Site Name:
                    </td>
                    <td style="width: 295px;">
                        <input type="text" name="sitename" value="' . $CONFIG['sitename'] . '" style="width: 100%;" />
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'scriptdir\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        News Script Directory:
                    </td>
                    <td style="width: 295px;">
                        <input type="text" name="scriptdir" value="' . $CONFIG['scriptdir'] . '" style="width: 100%;" />
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'adminmail\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Administrator Email:
                    </td>
                    <td style="width: 295px;">
                        <input type="text" name="adminmail" value="' . $CONFIG['adminmail'] . '" style="width: 100%;" />
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        &nbsp; <!--spacer-->
                    </td>
                </tr>';

                ////////////////////////////////////////////////////////////////////////////
                // POST/NEWS SETTINGS
                ////////////////////////////////////////////////////////////////////////////
                echo '<tr>
                    <td class="bars" colspan="3">
                        <b>Post/News Settings</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <b>Note</b>: If option <i>Show All Items</i> is selected as the <i>Show News Method</i>, then 
                        <i>Number of Items</i> setting sets the number of news items per page. Otherwise it sets the number
                        shown at a time.<br /><br />
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                       <a href="#null" onclick="launchhelp(\'oldnews\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td colspan="2" style="width: 535px;">
                        How would you like to handle older news items?
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                       &nbsp;
                    </td>
                    <td colspan="2" style="width: 535px;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 100%;">
                                    <input type="radio" name="oldnews" value="archive" onclick="enabler(this.form.news_age, 0); enabler(this.form.measurement, 0);"' . 
                                        getdefault($CONFIG['oldnews'], 'archive', 'check') . '/> Archive (save) all items
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 100%;">
                                    <input type="radio" name="oldnews" value="delete" onclick="enabler(this.form.news_age, 0); enabler(this.form.measurement, 0);"' . 
                                        getdefault($CONFIG['oldnews'], 'delete', 'check') . ' /> Delete items that are 
                                            not displayed
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 100%;">';

                            if ($CONFIG['oldnews'] != 'delete' && $CONFIG['oldnews'] != 'archive') {
                                echo '<input type="radio" name="oldnews" value="age" onclick="enabler(this.form.news_age, 1); enabler(this.form.measurement, 1);" checked="checked" />';

                                $parts = explode(' ', $CONFIG['oldnews']);
                            } else {
                                echo '<input type="radio" name="oldnews" value="age" onclick="enabler(this.form.news_age, 1); enabler(this.form.measurement, 1);" />';

                                $parts[] = ''; $parts[] = '';
                            }

                            echo ' Delete older than&nbsp;
                            <input type="text" name="news_age" maxlength="3" style="width: 30px;" value="' . $parts[0] . '"';

                            if ($CONFIG['oldnews'] != 'delete') {
                                echo ' disabled="disabled" ';
                            }

                            echo '/> 
                                    <select name="measurement"';
                                  
                            if ($CONFIG['oldnews'] != 'delete') {
                                echo ' disabled="disabled"';
                            }

                            echo '>
                                        <option value="days"' . getdefault($parts[1], 'days', 'select'). '>Days</option>
                                        <option value="weeks"' . getdefault($parts[1], 'weeks', 'select'). '>Weeks</option>
                                        <option value="months"' . getdefault($parts[1], 'months', 'select'). '>Months</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <br />
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'numitems\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Number of Items:
                    </td>
                    <td style="width: 295px;">
                        <input type="text" name="numitems" value="' . $CONFIG['numitems'] . '" maxlength="2" style="width: 100%;" />
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'datetime\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Date/Time Format:
                    </td>
                    <td style="width: 295px;">
                        <input type="text" name="timeformat" value="' . $CONFIG['timeformat'] . '" style="width: 100%;" />
                    </td>
                </tr>

                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'maxlen\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Maximum News Length:
                    </td>
                    <td style="width: 295px;">
                        <input type="text" name="maxlen" maxlength="5" value="' . $CONFIG['maxlen'] . '" style="width: 100%;" />
                    </td>
                </tr>

                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'max_subject_len\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Maximum Subject Length:
                    </td>
                    <td style="width: 295px;">
                        <input type="text" name="max_subject_len" maxlength="3" value="' . $CONFIG['max_subject_len'] . '" style="width: 100%;" />
                    </td>
                </tr>

                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'shownews\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Show News Method:
                    </td>
                    <td style="width: 295px;">
                        <select name="shownews" style="width: 100%;">
                            <option value="all"' . getdefault($CONFIG['shownews'], 'all', 'select') . '>Show All Items</option>
                            <option value="new"' . getdefault($CONFIG['shownews'], 'new', 'select') . '>Show New Items</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'allowhtml\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Allow HTML In Posts?
                    </td>
                    <td style="width: 295px;">' . getoptions('allowhtml', 'check') . '</td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'enablecats\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Enable News Cateogires?
                    </td>
                    <td style="width: 295px;">' . getoptions('enablecats', 'check') . '</td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'allowtags\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Allow [tags] In Posts?
                    </td>
                    <td>' . getoptions('allowtags', 'check') . '</td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'allowimages\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Allow Image [tags] In Posts?
                    </td>
                    <td style="width: 295px;">' . getoptions('allowimages', 'check') . '</td>
                </tr>

                <tr>
                    <td colspan="3">
                        &nbsp; <!--spacer-->
                    </td>
                </tr>';

                ////////////////////////////////////////////////////////////////////////////
                // COMMENT SETTINGS
                ////////////////////////////////////////////////////////////////////////////
                echo '<tr>
                    <td class="bars" colspan="3">
                        <b>Comment Settings</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <b>Note</b>: Banning by IPs does not only ban that member, but it also bans most people from
                        that ISPs server because there is no way to ban specific IPs for dialup users. A dialup user\'s
                        IP will change each time they sign on.<br /><br />
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'allowcomments\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Allow News Item Comments?
                    </td>
                    <td style="width: 295px;">' . getoptions('allowcomments', 'check') . '</td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'banips\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Ban Comment Users by IPs?
                    </td>
                    <td style="width: 295px;">' . getoptions('banips', 'check') . '</td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'numcomments\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Comments per Page:
                    </td>
                    <td style="width: 295px;">
                        <input type="text" name="numcomments" maxlength="2" value="' . $CONFIG['numcomments'] . '" style="width: 100%;">
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'maxcomlen\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        Maximum Comment Length:
                    </td>
                    <td style="width: 295px;">
                        <input type="text" name="maxcomlen" maxlength="4" value="' . $CONFIG['maxcomlen'] . '" style="width: 100%;">
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        &nbsp; <!--spacer-->
                    </td>
                </tr>';

                ////////////////////////////////////////////////////////////////////////////
                // USER SETTINGS
                ////////////////////////////////////////////////////////////////////////////
                echo '<tr>
                    <td class="bars" colspan="3">
                        <b>User Settings</b>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 20px;">
                        <a href="#" onclick="launchhelp(\'modifyusn\');"><img alt="Help" border="0" src="images/bullet_help.gif" /></a>
                    </td>
                    <td style="width: 240px;">
                        User Name Modification:
                    </td>
                    <td style="width: 295px;">
                        <select name="modifyusn" style="width: 100%;">
                            <option value="yes"' . getdefault($CONFIG['modifyusn'], 'yes', 'select') . '>
                                Allow name modification
                            </option>
                            <option value="no"' . getdefault($CONFIG['modifyusn'], 'no', 'select') . '>
                                Do not allow name modification
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center;">
                        <br />
                        <input type="submit" value=" Change Settings " class="formfield" />
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
    // SAVESETTINGS - Save the modified script settings:
    ////////////////////////////////////////////////////////////////////////////
    function savesettings() {
        global $CONFIG;
        
        // Save new settings:
        if ($CONFIG['up'] == 1) {
            // Validate numeric fields:
            if (isset($_POST['news_age'])) {
                if (!is_numeric($_POST['news_age']) || $_POST['news_age'] < 1 || $_POST['news_age'] > 31)
                    die(do_error('The <i>News Age</i> field must been a number between 1 and 31.'));
            }

            if (!is_numeric($_POST['numitems']) || $_POST['numitems'] < 1 || $_POST['numitems'] > 25) {
                die(do_error('The <i>Number of Items</i> field must been a number between 1 and 25.'));
            }

            if (!is_numeric($_POST['maxlen']) || $_POST['maxlen'] < 25 || $_POST['maxlen'] > 32000) {
                die(do_error('The <i></i> field must been a number between 25 and 32000.'));
            }

            if (!is_numeric($_POST['max_subject_len']) || $_POST['max_subject_len'] < 20 || $_POST['max_subject_len'] > 255) {
                die(do_error('The <i></i> field must been a number between 20 and 255.'));
            }

            if (!is_numeric($_POST['numcomments']) || $_POST['numcomments'] < 5 || $_POST['numcomments'] > 20) {
                die(do_error('The <i></i> field must been a number between 5 and 20.'));
            }

            if (!is_numeric($_POST['maxcomlen']) || $_POST['maxcomlen'] < 25 || $_POST['maxcomlen'] > 2000) {
                die(do_error('The <i></i> field must been a number between 25 and 2000.'));
            }

            // Loop through the $_POST var:
            while (list ($key, $val) = each ($_POST)) {
                if (array_key_exists($key, $CONFIG)) {
                    $sql = "UPDATE " . $CONFIG['tblPrefix'] . "config SET value='$val' WHERE name='$key'";

                    $result = mysql_query($sql) or die(do_error(mysql_error(), 1));
                    if (!$result) { 
                        die(do_error('Failed to save settings.')); 
                    }
                }
            }

            if ($_POST['oldnews'] != 'archive' && $_POST['oldnews'] != 'delete') {
                if (is_numeric($_POST['news_age'])) {
                    $date = $_POST['news_age'] . ' ' . $_POST['measurement'];
                    $sql = "UPDATE " . $CONFIG['tblPrefix'] . "config SET value='$date' WHERE name='oldnews'";

                    $result = mysql_query($sql) or die(do_error(mysql_error(), 1));
                    if (!$result) { 
                        die(do_error('Failed to save settings.')); 
                    }
                } else {
                    die(do_error('Invalid value specified for Old News option'));
                }
            }

            // Echo success:
            $title = $CONFIG['sitename'] . ' - Settings Saved';
            include 'header.html';

            echo 'The settings have been saved to the database.<br /><br />
            <a href="' . $CONFIG['scriptdir'] . 'index.php?action=settings">Click here</a> to go back to the settings page.';

            include 'footer.html';
        } else {
            die(do_error('You do not have permission to modify the settings.'));
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // GETOPTIONS - Used by changesettings() to print options easily:
    ////////////////////////////////////////////////////////////////////////////
    function getoptions($item, $type) {
        global $CONFIG;

        if ($type == 'check') {
            return '<input type="radio" name="' . $item . '" value="yes"' . getdefault($CONFIG[$item], 'yes', $type) . '>Yes &nbsp;
            <input type="radio" name="'. $item . '" value="no"' . getdefault($CONFIG[$item], 'no', $type) . '>No';
        }
    }
?>