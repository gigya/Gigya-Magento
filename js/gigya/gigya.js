/*
 *gigya functions
 */
var gigyaFunctions = gigyaFunctions || {};
var gigyaCache = {};
gigyaFunctions.login = function (response) {
  var toPost = {signature:response.UIDSignature, timestamp:response.signatureTimestamp, UID:response.UID, email: response.user.email , firstName: response.user.firstName, lastName: response.user.lastName};
  gigyaCache.uInfo = response;
  new Ajax.Request('/gigyalogin/login/login', {
      parameters: {json:JSON.stringify(response)},
      onSuccess: function (trans) {
        if (typeof trans.responseJSON.result !== 'undefined') {
          switch (trans.responseJSON.result)
          {
          case 'login':
            window.location = trans.responseJSON.redirect;
            break;
          case 'noEmail':
            $(trans.responseJSON.id).update(trans.responseJSON.html);
            break;
          case 'emailExsists':
            window.location = trans.responseJSON.redirect;
            break;
          }
        }
      }
  });
  };

  gigyaFunctions.emailSubmit = function () {
    var email = $$('#gigyaEmail')[0].value;
    var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (email.match(emailRegEx)) {
      var toPost = gigyaCache.uInfo;
      toPost.user.email = email;

      new Ajax.Request('/gigyalogin/login/login', {
          parameters: {json:JSON.stringify(toPost)},
          onSuccess: function (trans) {
            if (typeof trans.responseJSON.redirect !== 'undefined') {
              window.location = trans.responseJSON.redirect;
            }
          }
      }
      );
    }
    else {
      alert ('please enter a valid email');
    }
    };

    /*
     * register events
     */
    if (typeof gigya !== 'undefined') {
      gigya.services.socialize.addEventHandlers({onLogin:gigyaFunctions.login});
    }


    document.observe("dom:loaded", function() {
      if (typeof gigyaSettings !== 'undefined'){
        gigya.socialize.showLoginUI(gigyaSettings.login);
      }
    });
