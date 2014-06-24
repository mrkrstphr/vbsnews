///////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION - validate() : validates a user post
///////////////////////////////////////////////////////////////////////////////////////////////////
function validate(theform) {
	if (theform.subject.value.length < 2) {
		alert('Subject must be two or more characters in length.');
		return false;
	} else if(theform.body.value.length < 4) {
		alert('Body must be four or more characters in length.');
		return false;
	} else if(theform.body.value.length > 10000) {
		alert('Body must be less than 10,000 characters in length.');
		return false;
	} else {
		return true;
	}
}