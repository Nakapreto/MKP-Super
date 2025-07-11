jQuery(document).ready(function(){
    jQuery('[data-toggle="popover"]').popover({
        'trigger': 'hover'
    })

    window.onload = function () {
        execNotify()
    }
})

function execNotify(){
    const toast = getCookie('toastSPL')? getCookie('toastSPL') : false
    //success, error, warning, info
    const typeToast = getCookie('typeToastSPL')? getCookie('typeToastSPL') : 'success'
    if(toast){
        var notifier = new Notifier({
            default_time: '10000'
        });
        notifier.notify(typeToast, toast);
        document.cookie = "toastSPL=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "typeToastSPL=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

jQuery('.select2Multiple').select2({
    matcher: matchCustom
})

function matchCustom(params, data) {
    // If there are no search terms, return all of the data
    if (jQuery.trim(params.term) === '') {
        return data;
    }

    // Do not display the item if there is no 'text' property
    if (typeof data.text === 'undefined') {
        return null;
    }

    // `params.term` should be the term that is used for searching
    // `data.text` is the text that is displayed for the data object
    if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
        var modifiedData = jQuery.extend({}, data, true);

        // You can return modified objects from here
        // This includes matching the `children` how you want in nested data sets
        return modifiedData;
    }

    // Return `null` if the term should not be displayed
    return null;
}