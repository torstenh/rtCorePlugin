/*
 * Based on code by Scott Klarr
 *
 * http://www.scottklarr.com
 */
/*
 * Based on code by Scott Klarr
 *
 * http://www.scottklarr.com
 */
function injectTextIntoCurrent(text) { injectTextIntoID(text, $("*:focus").attr('id')); }


function injectTextIntoID(text, itemId) { var txtarea = document.getElementById(itemId); if(txtarea == null) { return false; }; var scrollPos = txtarea.scrollTop; var strPos = 0; var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? "ff" : (document.selection ? "ie" : false ) ); if (br == "ie") { txtarea.focus(); var range = document.selection.createRange(); range.moveStart ('character', -txtarea.value.length); strPos = range.text.length; } else if (br == "ff") strPos = txtarea.selectionStart; var front = (txtarea.value).substring(0,strPos); var back = (txtarea.value).substring(strPos,txtarea.value.length); txtarea.value=front+text+back; strPos = strPos + text.length; if (br == "ie") { txtarea.focus(); var range = document.selection.createRange(); range.moveStart ('character', -txtarea.value.length); range.moveStart ('character', strPos); range.moveEnd ('character', 0); range.select(); } else if (br == "ff") { txtarea.selectionStart = strPos; txtarea.selectionEnd = strPos; txtarea.focus(); } txtarea.scrollTop = scrollPos; }
