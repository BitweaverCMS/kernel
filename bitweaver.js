// $Header: /cvsroot/bitweaver/_bit_kernel/Attic/bitweaver.js,v 1.1.1.1.2.1 2005/06/26 07:59:36 squareing Exp $

//
// Set client offset (in minutes) to a cookie to avoid server-side DST issues
// Added 7/25/03 by Jeremy Jongsma (jjongsma@tickchat.com)
//
//alert( tikiCookiePath );
//alert( tikiCookieDomain );
var expires = new Date();
var offset = -(expires.getTimezoneOffset() * 60);
expires.setFullYear(expires.getFullYear() + 1);
setCookie("tz_offset", offset);

function toggle_dynamic_var($name) {
	name1 = 'dyn_'+$name+'_display';
	name2 = 'dyn_'+$name+'_edit';
	if(document.getElementById(name1).style.display == "none") {
		document.getElementById(name2).style.display = "none";
		document.getElementById(name1).style.display = "inline";
	} else {
		document.getElementById(name1).style.display = "none";
		document.getElementById(name2).style.display = "inline";
	}

}

function genPass(w1, w2, w3) {
	vo = "aeiouAEU";
	co = "bcdfgjklmnprstvwxzBCDFGHJKMNPQRSTVWXYZ0123456789_$%#";
	s = Math.round(Math.random());
	l = 8;
	p = '';

	for (i = 0; i < l; i++) {
		if (s) {
			letter = vo.charAt(Math.round(Math.random() * (vo.length - 1)));

			s = 0;
		} else {
			letter = co.charAt(Math.round(Math.random() * (co.length - 1)));

			s = 1;
		}

		p = p + letter;
	}

	document.getElementById(w1).value = p;
	document.getElementById(w2).value = p;
	document.getElementById(w3).value = p;
}

function setSomeElement(fooel, foo1) {
	document.getElementById(fooel).value = document.getElementById(fooel).value + foo1;
}

function replaceSome(fooel, what, repl) {
	document.getElementById(fooel).value = document.getElementById(fooel).value.replace(what, repl);
}

function replaceLimon(vec) {
	document.getElementById(vec[0]).value = document.getElementById(vec[0]).value.replace(vec[1], vec[2]);
}

// used by insertAt()
function setSelectionRange(textarea, selectionStart, selectionEnd) {
	if (textarea.setSelectionRange) {
		textarea.focus();
		textarea.setSelectionRange(selectionStart, selectionEnd);
	}
	else if (textarea.createTextRange) {
		var range = textarea.createTextRange();
		textarea.collapse(true);
		textarea.moveEnd('character', selectionEnd);
		textarea.moveStart('character', selectionStart);
		textarea.select();
	}
}

// used by insertAt()
function setCaretToPos (textarea, pos) {
	setSelectionRange(textarea, pos, pos);
}

// inserts replaceString in elementId - used for quicktags
function insertAt(elementId, replaceString) {
	//inserts given text at selection or cursor position
	textarea = document.getElementById(elementId);
	var toBeReplaced = /text|page|textarea_id/;//substrings in replaceString to be replaced by the selection if a selection was done
	if (textarea.setSelectionRange) {
		//Mozilla UserAgent Gecko-1.4
		var selectionStart = textarea.selectionStart;
		var selectionEnd = textarea.selectionEnd;
		if (selectionStart != selectionEnd) { // has there been a selection
	var newString = replaceString.replace(toBeReplaced, textarea.value.substring(selectionStart, selectionEnd));
			textarea.value = textarea.value.substring(0, selectionStart)
									+ newString
									+ textarea.value.substring(selectionEnd);
			setSelectionRange(textarea, selectionStart, selectionStart + newString.length);
		} else {// set caret
			textarea.value = textarea.value.substring(0, selectionStart)
									+ replaceString
									+ textarea.value.substring(selectionEnd);
			setCaretToPos(textarea, selectionStart + replaceString.length);
		}
	} else if (document.selection) {
		//UserAgent IE-6.0
		textarea.focus();
		var range = document.selection.createRange();
		if (range.parentElement() == textarea) {
			var isCollapsed = range.text == '';
			if (! isCollapsed)	{
				range.text = replaceString.replace(toBeReplaced, range.text);
				range.moveStart('character', -range.text.length);
				range.select();
			} else {
				range.text = replaceString;
			}
		}
	}
	else { //UserAgent Gecko-1.0.1 (NN7.0)
		setSomeElement(elementId, replaceString)
		//alert("don't know yet how to handle insert" + document);
	}
}

function setUserModuleFromCombo(id) {
	document.getElementById('usermoduledata').value = document.getElementById('usermoduledata').value
		+ document.getElementById(id).options[document.getElementById(id).selectedIndex].value;
//document.getElementById('usermoduledata').value='das';
}

function show(foo,f) {
	document.getElementById(foo).style.display = "block";
	if (f) { setCookie(foo, "o"); }
}

// show, hide, flip and toggle should be removed at some point as they are can't be navigated without js if the initial setting is hidden.
function hide(foo,f) {
	document.getElementById(foo).style.display = "none";
	if (f) { setCookie(foo, "c"); }
}

function flip(foo) {
	if (document.getElementById(foo).style.display == "none") {
		show(foo);
	} else {
		if (document.getElementById(foo).style.display == "block") {
			hide(foo);
		} else {
			show(foo);
		}
	}
}

function toggle(foo) {
	if (document.getElementById(foo).style.display == "none") {
		show(foo);

		setCookie(foo, "o");
	} else {
		if (document.getElementById(foo).style.display == "block") {
			hide(foo,1);
		} else {
			show(foo,1);
		}
	}
}

function setfoldericonstate(foo) {
	pic = new Image();

	if (getCookie(foo) == "o") {
		pic.src = tikiIconDir + "/expanded.gif";
	} else {
		pic.src = tikiIconDir + "/collapsed.gif";
	}
//alert(document.getElementById(foo+"img").src);
//alert(pic.src);
	document.getElementById(foo+"img").src = pic.src;
}

// used in conjunction with setfoldericonstate()
function icntoggle(foo) {
	if (document.getElementById(foo).style.display == "none") {
		show(foo);
		setCookie(foo, "o");
	} else {
		hide(foo);
		setCookie(foo, "c");
	}
	setfoldericonstate(foo);
}

// name - name of the cookie
// value - value of the cookie
// [expires] - expiration date of the cookie (defaults to end of current session)
// [path] - path for which the cookie is valid (defaults to path of calling document)
// [domain] - domain for which the cookie is valid (defaults to domain of calling document)
// [secure] - Boolean value indicating if the cookie transmission requires a secure transmission
// * an argument defaults when it is assigned null as a placeholder
// * a null placeholder is not required for trailing omitted arguments
function setCookie(name, value, expire, path, domain, secure) {
	var cookie_path = ((tikiCookiePath) ? "; path=" + tikiCookiePath : tikiRootUrl);
	var cookie_domain = ((tikiCookieDomain) ? "; domain=" + escape(tikiCookieDomain) : "");
	var curCookie = name + "=" + escape(value)
		+ ((expire) ? "; expires=" + expire.toGMTString() : "; expires=" + expires.toGMTString())
		+ ((path) ? "; path=" + path : cookie_path)
		+ ((domain) ? "; domain=" + domain : cookie_domain)
		+ ((secure) ? "; secure" : "");
//alert(curCookie);
	document.cookie = curCookie;
}

// name - name of the desired cookie
// * return string containing value of specified cookie or null if cookie does not exist
function getCookie(name) {
	var dc = document.cookie;

	var prefix = name + "=";
	var begin = dc.indexOf("; " + prefix);

	if (begin == -1) {
		begin = dc.indexOf(prefix);

		if (begin != 0)
			return null;
	} else begin += 2;

	var end = document.cookie.indexOf(";", begin);

	if (end == -1)
		end = dc.length;

	return unescape(dc.substring(begin + prefix.length, end));
}

// name - name of the cookie
// [path] - path of the cookie (must be same as path used to create cookie)
// [domain] - domain of the cookie (must be same as domain used to create cookie)
// * path and domain default if assigned null or omitted if no explicit argument proceeds
function deleteCookie(name, path, domain) {
	var cookie_path = ((tikiCookiePath) ? "; path=" + tikiCookiePath : tikiRootUrl);
	var cookie_domain = ((tikiCookieDomain) ? "; domain=" + escape(tikiCookieDomain) : "");
	if (getCookie(name)) {
		document.cookie = name + "="
			+ ((path) ? "; path=" + path : cookie_path) + ((domain) ? "; domain=" + domain : cookie_domain) + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
	}
}

// Expand/collapse lists
function flipWithSign(foo) {
	if (document.getElementById(foo).style.display == "none") {
		show(foo);

		collapseSign("flipper" + foo);
		setCookie(foo, "o");
	} else {
		hide(foo);

		expandSign("flipper" + foo);
		setCookie(foo, "c");
	}
}

// set the state of a flipped entry after page reload
function setFlipWithSign(foo) {
	if (getCookie(foo) == "o") {
		collapseSign("flipper" + foo);

		show(foo);
	} else {
		expandSign("flipper" + foo);

		hide(foo);
	}
}

function expandSign(foo) {
	document.getElementById(foo).firstChild.nodeValue = "[+]";
}

function collapseSign(foo) {
	document.getElementById(foo).firstChild.nodeValue = "[-]";
} // flipWithSign()

// Check / Uncheck all Checkboxes
function switchCheckboxes(the_form, elements_name, switcher_name) {
	// checkboxes need to have the same name elements_name
	// e.g. <input type="checkbox" name="my_ename[]">, will arrive as Array in php.
	var elements = document.getElementById(the_form).elements[elements_name];

	var elements_cnt = ( typeof (elements.length) != 'undefined') ? elements.length : 0;

	if (elements_cnt) {
		for (var i = 0; i < elements_cnt; i++) {
			elements[i].checked = document.forms[the_form].elements[switcher_name].checked;
		}
	} else {
		elements.checked = document.forms[the_form].elements[switcher_name].checked;

		;
	} // end if... else

	return true;
}		 // switchCheckboxes()


// function added for use in navigation dropdown
// example :
// <select name="anything" onchange="go(this);">
// <option value="http://bitweaver.org">bitweaver.org</option>
// </select>
function go(o) {
	if (o.options[o.selectedIndex].value != "") {
		location = o.options[o.selectedIndex].value;
		o.options[o.selectedIndex] = 1;
	}
	return false;
}

// function:	confirmTheLink
// desc:	pop up a dialog box to confirm the action
// added by: 	Franck Martin
// date:	Oct 12, 2003
// params:	theLink: The link where it is called from
// params: theMsg: The message to display
function confirmTheLink(theLink, theMsg) {
	// Confirmation is not required if browser is Opera (crappy js implementation)
	if (typeof(window.opera) != 'undefined') {
		return true;
	}
	var is_confirmed = confirm(theMsg);
	return is_confirmed;
}

/** \brief: modif a textarea dimension
 * \elementId = textarea idea
 * \height = nb pixels to add to the height (the number can be negative)
 * \width = nb pixels to add to the width
 * \formid = form id (needs to have 2 input rows and cols
 **/
function textareasize(elementId, height, width, formId) {
	textarea = document.getElementById(elementId);
	form = document.getElementById(formId);
	if (textarea && height != 0 && textarea.rows + height > 5) {
		textarea.rows += height;
		if (form.rows)
			form.rows.value = textarea.rows;
	}
	if (textarea && width != 0 && textarea.cols + width > 10) {
		 textarea.cols += width;
		if (form.cols)
			form.cols.value = textarea.cols;
	}
}

// function:	popUpWin
// desc:		span a new window which is XHTML 1.0 Strict compliant and in accordance with WCAG
// added by:	xing
// date:		2004-12-27
// params:		url:		the url for the new window
//				type:		standard or fullscreen
//				strWidth:	width of the window
//				strHeight:	height of the spawned window
// usage:		<a href="<URL>" title="{tr}Opens link in new window{/tr}" onkeypress="popUpWin(this.href,'standard',600,400);" onclick="popUpWin(this.href,'standard',600,400);return false;">{tr}FooBar{/tr}</a>
var newWindow = null;
function closeWin(){
	if (newWindow != null){
		if(!newWindow.closed)
			newWindow.close();
	}
}

function popUpWin(url, type, strWidth, strHeight) {
	closeWin();
	if (type == "fullScreen"){
		strWidth = screen.availWidth - 10;
		strHeight = screen.availHeight - 160;
	}
	var tools="";
	if (type == "standard" || type == "fullScreen") tools = "resizable,toolbar=no,location=no,scrollbars=yes,menubar=no,width="+strWidth+",height="+strHeight+",top=0,left=0";
	if (type == "console") tools = "resizable,toolbar=no,location=no,scrollbars=yes,width="+strWidth+",height="+strHeight+",left=0,top=0";
	newWindow = window.open(url, 'newWin', tools);
	newWindow.focus();
}

function toggleBlockDisplay(item) {
	if (document.layers) {
		current = (document.layers[item].display == 'none') ? 'block' : 'none';
		document.layers[item].display = current;
	} else if (document.all) {
		current = (document.all[item].style.display == 'none') ? 'block' : 'none';
		document.all[item].style.display = current;
	} else if (document.getElementById) {
		vista = (document.getElementById(item).style.display == 'none') ? 'block' : 'none';
		document.getElementById(item).style.display = vista;
	}
}

function setBlockDisplay(item,vizFlag) {
	current = (vizFlag) ? 'block' : 'none';
	if (document.layers) {
		document.layers[item].display = current;
	} else if (document.all) {
		document.all[item].style.display = current;
	} else if (document.getElementById) {
		document.getElementById(item).style.display = current;
	}
}
