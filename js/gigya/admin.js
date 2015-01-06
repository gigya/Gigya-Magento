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
            $$('.raas-comment')[0].hide();
			break;
		case "disable":
			this.hideSection("gigya_login_gigya_raas_conf");
			this.hideSection("gigya_login_gigya_link_accounts-state");
			this.hideSection("gigya_login_gigya_login_conf-state");
            $$('.raas-comment')[0].hide();
			break;
		case "raas":
			this.hideSection("gigya_login_gigya_link_accounts-state");
			this.hideSection("gigya_login_gigya_login_conf-state");
			this.showSection("gigya_login_gigya_raas_conf");
            $$('.raas-comment')[0].show();
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

// toggle other-data-center text-field in general settings page
gigyaAdmin.hideOtherDataCenter = function () {
    $('row_gigya_global_gigya_global_conf_dataCenterOther').hide();
}
gigyaAdmin.showOtherDataCenter = function () {
    $('row_gigya_global_gigya_global_conf_dataCenterOther').show();
}
gigyaAdmin.JsonExampleWindow = function(event) {
    var w = window.open( "about:blank", "jsonExample", "width=440,height=330" );
    var json_example = '{ &#013;' +
        '  "key1" : "value1",&#013;' +
        '  "key2" : "value2" &#013;' +
        '  "key3" : "value3" &#013;' +
        '  "key4" : "value4" &#013;' +
        '}';
    w.document.write( '<p>JSON Example:</p><textarea  rows="8" cols="45">' + json_example + '</textarea><br><small>Tips for valid JSON:<ol><li>{curly brackets} are for objects (key:value).</li><li>[square brackets] are for arrays (value).</li><li>Both keys and values must have double quote ("").</li><li>No trailing commas.</li></ol></small>' );
}

/*
// Validate advanced config json:
// added jsonlint to js/gigya/json
try to activate following - http://blog.kyp.fr/how-to-validate-magento-configuration-values-format/
Validation.addAllThese([
   ['validate-advanced-json','the text you have entered is not a valid JSON format.', function(v) {
       return Validation.get('IsEmpty').test(v) || /^[a-zA-Z]{3}$/i.test(v)
   }]
]);
*/

// On document load :
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
    if ($('row_gigya_global_gigya_global_conf_dataCenterOther') != null) {
        if ($(gigya_global_gigya_global_conf_dataCenterOther).value == '') {
            gigyaAdmin.hideOtherDataCenter();
        }

        // Global config - When 'other' data center is selected, show text field
        // (this can be avoided by defining field dependancy in system.xml
        $( 'gigya_global_gigya_global_conf_dataCenter' ).observe('change', function(event) {
            if ( !this.value ) {
                gigyaAdmin.showOtherDataCenter();
            } else {
                gigyaAdmin.hideOtherDataCenter();
            }
        });
    }
    // Open JSON example window for Advanced configuration
    $$('.valid-json-example').each( function (elem) {
        elem.observe('click', function (event) {
            gigyaAdmin.JsonExampleWindow(event);
        });
    });

});
