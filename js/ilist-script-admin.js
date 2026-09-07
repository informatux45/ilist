// Javascript functions
function ilist_confirm_delete(txt) {
    var r = confirm(txt);
    if (r == true) {
        return true;
    } else {
        return false;
    }
}