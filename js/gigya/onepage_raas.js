/**
 *
 * Created with PhpStorm.
 * User:
 * Date: 7/6/14
 * Time: 4:18 PM
 */


var gigyaOnepage = gigyaOnepage || {};

gigyaOnepage.embedGigyaLogin = function () {
    var params = gigyaSettings.RaaS;
    params.raas_login_div_id = 'login-form';
    gigya.accounts.showScreenSet(JSON.parse('{"screenSet": "' + params.WebScreen + '", "containerID": "' + params.raas_login_div_id + '" , "mobileScreenSet":"' + params.MobileScreen + '", "startScreen":"' + params.LoginScreen + '"}'));
    $('onepage-guest-register-button').enable();
}

gigyaOnepage.embedGigyaRe = function () {
    var params = gigyaSettings.RaaS;
    params.raas_register_div_id = 'login-form';
    gigya.accounts.showScreenSet(JSON.parse('{"screenSet":"' + params.WebScreen + '", "containerID":"' + params.raas_register_div_id + '", "mobileScreenSet":"' + params.MobileScreen + '", "startScreen": "' + params.RegisterScreen + '"}'));
    $('onepage-guest-register-button').disable();
}

gigyaOnepage.register = function () {

}
gigyaOnepage.init = function () {
    $$('.col-2 button[type=submit]')[0].remove();
    $$("#checkout-step-login .form-list")[0].observe("change", function(event) {
        var el = event.findElement("input");
        if (typeof el !== 'undefined') {
            console.log(el.value);
            if (el.value == 'register') {
                gigyaOnepage.embedGigyaRe();
            } else {
                gigyaOnepage.embedGigyaLogin();
            }
        }
    });
    $$('.col-2')[0].setStyle({float: "none", width: "auto"});
    gigyaOnepage.embedGigyaLogin();
}

document.observe("dom:loaded", function () {
   gigyaOnepage.init();
});
