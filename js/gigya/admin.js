/**
 *
 * Created with PhpStorm.
 * User:
 * Date: 6/9/14
 * Time: 5:51 PM
 */

var gigyaAdmin = gigyaAdmin || {};

gigyaAdmin.userManegmentUI = function(userMod) {
	if (typeof userMod == 'undefined') {
		var userMod = $$('#row_gigya_login_gigya_user_management_login_modes .value input:checked[type="radio"][name="groups[gigya_user_management][fields][login_modes][value]"').pluck('value')[0];
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

gigyaAdmin.hideSection = function (section) {
	$(section).up('.section-config').hide();
}

gigyaAdmin.showSection = function (section) {
	$(section).up('.section-config').show();
}



document.observe("dom:loaded", function () {
	gigyaAdmin.userManegmentUI();
	// bind events
	$("row_gigya_login_gigya_user_management_login_modes").observe("click", function(event) {
		var el = event.findElement("input");
		if (typeof el !== 'undefined') {
			gigyaAdmin.userManegmentUI(el.value);
		}
	})
});
