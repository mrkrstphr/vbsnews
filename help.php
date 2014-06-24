<?php

    ////////////////////////////////////////////////////////////////////////////
    // DESCRIPTION:
    // help.php - Help system for vbsNews Settings
    ////////////////////////////////////////////////////////////////////////////
    // WARNING:
    // It is not recommended to modify anything below this line!
    ////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////
    // Setup the $HELP array:
    ////////////////////////////////////////////////////////////////////////////
    $HELP = array(
        // General Settings:
        'sitename' => '<b>Site Name</b>: This setting simply controls the title of the script pages.',
        'scriptdir' => '<b>News Script Directory</b>: This setting should be set to the directory where 
            vbsNews resides on your server. It must end in a trailing slash.<br /><br />
            <b>Note</b>: Changing the <i>News Script Directory</i> setting can cause vbsNews to not function
            properly. It is recommended that if you need to change the location of the script, you make
            sure a copy exists in the new location beforehand.',
        'adminmail' => '<b>Admin Email</b>: This option sets the email from which all messages are sent form
            by the news scripts.',

        // News Item Settings:
        'datetime' => '<b>Date/Time Format</b>: This setting sets the date and time format for displaying within posts 
            and comments.<br /><br />The values for this function match the php date() function. For more information
            on how to setup these values, visit the
            <a href="http://www.vbshelf.com/support/manual.php" target="_blank">user manual</a>.',
        'shownews' => '<b>Show News Method</b>: This setting determines whether all news items are shown, or only a 
            certain number.<br /><br />
            <b>Note</b>: If option <i>Show All Items</i> is selected as the <i>Show News Method</i>, then <i>Number of 
            Items</i> setting sets the number of news items per page. Otherwise it sets the number shown at a time.',
        'numitems' => '<b>Number of Items</b>: This setting sets the number of items to show, or items per page if 
            <i>Show All</i> is selected for <i>Show News Method</i>.',
        'oldnews' => '<b>Older News Items</b>: This setting determines whether to keep (archive) or delete older 
            news items.',
        'maxlen' => '<b>Max Post Length</b>: Sets the maximum length (number of characters) of the news item body.',
        'max_subject_len' => '<b>Max Subject Length</b>: Sets the maximum length (number of characters) of the news item subject, between 2 and 255 characters.',
        'allowhtml' => '<b>Allow HTML In Posts</b>: This setting toggles HTML for user posts, commenting, etc.',
        'allowtags' => '<b>Allow [tags] In Posts</b>: This setting toggles the built in tags for user posts, 
            commenting, etc.',
        'allowimages' => '<b>Allow Image [tags] In Posts</b>: This setting determines whether or not to allow the 
            posting of images. Allow Tags must be turned on for this to work.',
        'allowimages' => '<b>Allow Image [tags] In Posts</b>: This setting determines whether or not to allow the 
            posting of images. Allow Tags must be turned on for this to work.',
        'enablecats' => '<b>Enable News Categories</b>: Enables categorical news posting. News items are placed in
            different categories and can be displayed by category.', 

        //Comment Settings:
        'allowcomments' => '<b>Allow Guest Commenting</b>: This setting determines whether or not a visitor to your site
            can leave comments about news items.',
        'numcomments' => '<b>Number of Comments / Page</b>: This setting sets the number of comments to be displayed per page.',
        'banips' => '<b>Ban User IPs</b>: Enable/disable IP banning in the comments system.',
        'maxcomlen' => '<b>Maximum Comment Length</b>: Sets the maximum length (number of characters) of the comment item body.',
        //User Settings:
        'modifyusn' => '<b>User Name Modification</b>: This setting determines whether or not a user can change/modify
            their user name.'
    );

    // Figure out what to do:
    if (isset($HELP[$_REQUEST['item']])) {
        item();
    } else {
        echo '<span style="font-family: verdana, arial; font-size: 8pt;"><b>Error</b>: The help item does not exist!</span>';
    }

    ////////////////////////////////////////////////////////////////////////////
    // ITEM - Display help for the specified item:
    ////////////////////////////////////////////////////////////////////////////
    function item() {
        global $HELP;

        echo '<html>
        <head>
            <title>vbsNews Help System</title>
            <style type="text/css">
                body {
                    font-family: verdana, arial;
                    font-size: 10pt;
                }
            </style>
        </head>
        <body>' . $HELP[$_REQUEST['item']] . '<br /><br />
            <div style="text-align: right;"><a href="javascript: close();">Close Window</a></div>
        </body>
        </html>';
    }
?>