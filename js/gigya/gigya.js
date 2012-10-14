/*
 *gigya functions
 */
var gigyaFunctions = gigyaFunctions || {};
gigyaFunctions.login = function (responce) {
  console.log(responce);
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
