/**
 *
 * Created with PhpStorm.
 * User:
 * Date: 6/9/14
 * Time: 5:51 PM
 */

var gigyaAdmin = gigyaAdmin || {};

gigyaAdmin.userManegmentUI = function(userMod) {
	if (userMod == null) {
        if (userMod == null) {
            var userMod = $F('gigya_login_gigya_user_management_login_modes');
        }
	}
	switch (userMod) {
		case "social":
			this.hideSection("gigya_login_gigya_raas_conf");
			this.showSection("gigya_login_gigya_link_accounts-state");
			this.showSection("gigya_login_gigya_login_conf-state");

			break;
		case "disable":
			this.hideSection("gigya_login_gigya_raas_conf");
			this.hideSection("gigya_login_gigya_link_accounts-state");
			this.hideSection("gigya_login_gigya_login_conf-state");
			break;
		case "raas":
			this.hideSection("gigya_login_gigya_link_accounts-state");
			this.hideSection("gigya_login_gigya_login_conf-state");
				this.showSection("gigya_login_gigya_raas_conf");
			break;
	}
}

gigyaAdmin.userKeyUI = function (useKey) {
    if (useKey == null) {
        if ($("gigya_global_gigya_global_conf_useUserKey") != null){
            var useKey = $F('gigya_global_gigya_global_conf_useUserKey');
        } else {
            return false;
        }
    }
    if (useKey == 0) {
        gigyaAdmin.hideUserKey();
    } else {
        gigyaAdmin.showUserKey();
    }

}

gigyaAdmin.hideUserKey = function () {
    $('row_gigya_global_gigya_global_conf_userKey').hide();
    $('row_gigya_global_gigya_global_conf_userSecret').hide();
    $('row_gigya_global_gigya_global_conf_secretkey').show();
}

gigyaAdmin.showUserKey = function () {
    $('row_gigya_global_gigya_global_conf_userKey').show();
    $('row_gigya_global_gigya_global_conf_userSecret').show();
    $('row_gigya_global_gigya_global_conf_secretkey').hide();
}

gigyaAdmin.hideSection = function (section) {
	$(section).up('.section-config').hide();
}

gigyaAdmin.showSection = function (section) {
	$(section).up('.section-config').show();
}


document.observe("dom:loaded", function () {
    if ($('gigya_login_gigya_user_management_login_modes') != null) {
        gigyaAdmin.userManegmentUI(null);
    }
    gigyaAdmin.userKeyUI(null);
	// bind events
    if ($("gigya_login_gigya_user_management_login_modes") != null){
	$("gigya_login_gigya_user_management_login_modes").observe("change", function(event) {
		var el = $F('gigya_login_gigya_user_management_login_modes');
		if (typeof el !== 'undefined') {
			gigyaAdmin.userManegmentUI(el.value);
		}
	})
    }
    if($("gigya_global_gigya_global_conf_useUserKey") != null) {
    $("gigya_global_gigya_global_conf_useUserKey").observe("change", function(event) {
        var useKey = $F('gigya_global_gigya_global_conf_useUserKey');
        gigyaAdmin.userKeyUI(useKey);
    })
    }
});