(function ($) {
    $(document).ready(function () {
        var popup_is_showing = false;
        var popup_id = isMobile() ? splPop.mobile : splPop.desktop;
        var loadPopupAfterSeconds = parseInt(splPop.loadPopupAfterSeconds) * 1000;
        var exitIntentPopup = splPop.exitIntentPopup;
        var popup_base = $(`#splPop-${popup_id}`);
        const close_btn = $('.splPop .close');
        const hook = popup_base.data('hook') || 'appear';
        const timeout = popup_base.data('timeout') || 0;
        const animation = popup_base.data('animation') || 'none';
        const expiration = parseInt(popup_base.data('expiration')) || 0;
        close_btn.on('click', function (e) { popup_base.removeClass('on'); $('.p-content-wrapper', popup_base).removeClass(`animated ${animation}`); popup_is_showing = false; })

        switch (exitIntentPopup) {
            case 'load':
                setTimeout(() => {
                    show_popup()
                }, loadPopupAfterSeconds);
                break;
            case 'exit':
                if (!isMobile()) {
                    document.documentElement.addEventListener("mouseleave", function(e){
                        if (e.clientY > 20) { return; }
                        show_popup()
                    })
                }
                break;
            default:
                setTimeout(() => {
                    show_popup()
                }, loadPopupAfterSeconds);

                if (!isMobile()) {
                    document.documentElement.addEventListener("mouseleave", function (e) {
                        if (e.clientY > 20) {
                            return;
                        }
                        show_popup()
                    })
                }
                break;
        }

        function show_popup() {
            if (!popup_is_showing && !!!Cookies.get(popup_id)) {
                popup_base.addClass(`on`)
                if (animation != 'none') {
                    $('.p-content-wrapper', popup_base).addClass(`animated ${animation}`)
                }
                popup_is_showing = true;

                if (expiration) {
                    Cookies.set(popup_id, 1, { expires: expiration });
                }
            }
        }

    })
})(jQuery);

function isMobile() {
    var check = false;
    (function (a) { if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true; })(navigator.userAgent || navigator.vendor || window.opera);
    return check;
}

// Cookie.js

!function (e, t) { "object" == typeof exports && "undefined" != typeof module ? module.exports = t() : "function" == typeof define && define.amd ? define(t) : (e = e || self, function () { var r = e.Cookies, n = e.Cookies = t(); n.noConflict = function () { return e.Cookies = r, n } }()) }(this, function () { "use strict"; function e(e) { for (var t = 1; t < arguments.length; t++) { var r = arguments[t]; for (var n in r) e[n] = r[n] } return e } var t = { read: function (e) { return e.replace(/%3B/g, ";") }, write: function (e) { return e.replace(/;/g, "%3B") } }; return function r(n, i) { function o(r, o, u) { if ("undefined" != typeof document) { "number" == typeof (u = e({}, i, u)).expires && (u.expires = new Date(Date.now() + 864e5 * u.expires)), u.expires && (u.expires = u.expires.toUTCString()), r = t.write(r).replace(/=/g, "%3D"), o = n.write(String(o), r); var c = ""; for (var f in u) u[f] && (c += "; " + f, !0 !== u[f] && (c += "=" + u[f].split(";")[0])); return document.cookie = r + "=" + o + c } } return Object.create({ set: o, get: function (e) { if ("undefined" != typeof document && (!arguments.length || e)) { for (var r = document.cookie ? document.cookie.split("; ") : [], i = {}, o = 0; o < r.length; o++) { var u = r[o].split("="), c = u.slice(1).join("="), f = t.read(u[0]).replace(/%3D/g, "="); if (i[f] = n.read(c, f), e === f) break } return e ? i[e] : i } }, remove: function (t, r) { o(t, "", e({}, r, { expires: -1 })) }, withAttributes: function (t) { return r(this.converter, e({}, this.attributes, t)) }, withConverter: function (t) { return r(e({}, this.converter, t), this.attributes) } }, { attributes: { value: Object.freeze(i) }, converter: { value: Object.freeze(n) } }) }(t, { path: "/" }) });