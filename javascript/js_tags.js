var isChecked = false;

///////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION - basicTag() : formats text in textarea with [tags]
///////////////////////////////////////////////////////////////////////////////////////////////////
function basicTag (input, start, end) {
    if (input.setSelectionRange) {
        var selectionStart = input.selectionStart;
        var selectionEnd = input.selectionEnd;
    
        input.value = input.value.substring(0, selectionStart)
          + '[' + start + ']' + input.value.substring(selectionStart, selectionEnd) + '[/' + end + ']' 
          + input.value.substring(selectionEnd);

    } else if (document.selection) {
        var range = document.selection.createRange();
        var noSel = range.text == '';

        if (range.parentElement() == input) {
            range.text = '[' + start + ']' + range.text + '[/' + end + ']';
        }

        // If nothing was selected, add tags at cursor position:
        if (noSel) {
            var caretPos = input.caretPos;
            var tag = '[' + start + '][/' + end + ']';

            caretPos.text = tag;
        }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION - advTag() : validates param, then calls basicTag, if needed
///////////////////////////////////////////////////////////////////////////////////////////////////
function advTag(input, tag, param) {
    if (param != '') {
        basicTag(input, tag + '=' + param, tag);
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION - doimg() : runs prompts to create an image [tag]
///////////////////////////////////////////////////////////////////////////////////////////////////
function doimg(input) {
	var tagtext = prompt('Enter the location of the image:', 'http://');
	var tagalign = prompt('Enter the image alignment (optional):\n1 - Left, 2 - Right', '');

	if (tagtext != '' && tagtext != null) {
		if (tagalign != '' && tagalign != null) {
			switch (tagalign) {
				case '1':
					tagtext = '[img align=left]' + tagtext + '[/img]'; break;
				case '2':
					tagtext = '[img align=right]' + tagtext + '[/img]'; break;
				default:
					tagtext = '[img]' + tagtext + '[/img]'; break;
			}
		} else {
			tagtext = '[img]' + tagtext + '[/img]';
		}

		insertAtCaret(input, tagtext);
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION - doaling() : creates an align tag
///////////////////////////////////////////////////////////////////////////////////////////////////
function doalign(input) {
	var tagtext = prompt('Enter the text:', '');
	var tagalign = prompt('Enter the alignment:\n1 - Left, 2 - Right, 3 - Center', '');

	if (tagtext != '' && tagtext != null) {
		if (tagalign != '' && tagalign != null) {
			switch (tagalign) {
				case '1':
					tagtext = '[align=left]' + tagtext + '[/align]'; break;
				case '2':
					tagtext = '[align=right]' + tagtext + '[/align]'; break;
				case '3':
					tagtext = '[align=center]' + tagtext + '[/align]'; break;
				default:
					tagtext = '';
			}

			insertAtCaret(input, tagtext);
		}
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION - dolink() : creates a link tag
///////////////////////////////////////////////////////////////////////////////////////////////////
function dolink(input) {
	var linktext = prompt('Enter the text for the link below: (optional)', '');
	var linkurl = prompt('Enter the web address for the link: (required)', '');

	if (linkurl != '' && linkurl != null) {
		if (linktext != '' && linktext != null) {
			var link = '[url=' + linkurl + ']' + linktext + '[/url]';
		} else {
			var link = '[url]' + linkurl + '[/url]';
		}

		insertAtCaret(input, link);
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION - dolist() : creates list tags
///////////////////////////////////////////////////////////////////////////////////////////////////
function dolist(input) {
	var listtype = prompt("Which type of list?\n1 - Numbered, 2 - Alphabetical, or 3 - Bullet", '');

	if (listtype != '' && listtype != null && listtype < 4 && listtype > 0) {
		var listitem = '';
		var listtag = '';

		do {
			listitem = prompt('Enter next item: (or leave blank to stop)', '');
			 if (listitem != '' && listitem != null) {
				 listtag = listtag + '\n[*]' + listitem + '[/*]';
			 }
		} while (listitem != '' && listitem != null);

		if (listtag != '' && listtag != null) {
			listtag = '[list=' + listtype + ']' + listtag + '[/list]';
		}

		insertAtCaret(input, listtag);
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION - insertAtCaret : used to insert text at a specific point in textarea
///////////////////////////////////////////////////////////////////////////////////////////////////
function insertAtCaret(input, textString) {
    if (input.setSelectionRange) {
        var selectionStart = input.selectionStart;
        var selectionEnd = input.selectionEnd;
    
        input.value = input.value.substring(0, selectionStart) + textString + input.value.substring(selectionEnd);
    } else if (document.selection) {
        var range = document.selection.createRange();

        if (range.parentElement() == input) {
            var isCollapsed = range.text == '';
            range.text = textString;

            if (!isCollapsed) {
                range.moveStart('character', -textString.length);
                range.select();
            }
        } else {
            var caretPos = input.caretPos;

            caretPos.text = textString;
        }
    } else {
        input.value = input.value + textString;
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION - storeCaret : stores caret location in given textarea
///////////////////////////////////////////////////////////////////////////////////////////////////
function storeCaret (input) {
    if (input.createTextRange) {
        input.caretPos = document.selection.createRange().duplicate();
    }
}