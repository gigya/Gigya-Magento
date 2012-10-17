/*
 *gigya functions
 */
var gigyaFunctions = gigyaFunctions || {};
var gigyaCache = {};
gigyaFunctions.login = function (response) {
  var toPost = {signature:response.UIDSignature, timestamp:response.signatureTimestamp, UID:response.UID, email: response.user.email , firstName: response.user.firstName, lastName: response.user.lastName};
  gigyaCache.uInfo = toPost;
  new Ajax.Request('/gigyalogin/login/login', {
      parameters: toPost,
      onSuccess: function (trans) {
        if (typeof trans.responseJSON.redirect !== 'undefined') {
          window.location = trans.responseJSON.redirect;
        }
        if (typeof trans.responseJSON.html !== 'undefined') {
          var rep = $(trans.responseJSON.id).update(trans.responseJSON.html);
        }
      }
  });
};

gigyaFunctions.emailSubmit = function () {
  var email = $$('#gigyaEmail')[0].value;
  var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if (email.match(emailRegEx)) {
    var toPost = gigyaCache.uInfo;
    toPost.email = email;
    console.log(toPost);

    new Ajax.Request('/gigyalogin/login/login', {
        parameters: toPost,
        onSuccess: function (trans) {
          console.log(trans);
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
