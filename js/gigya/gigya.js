document.observe("dom:loaded", function() {
  if (typeof gigyaSettings !== 'undefined'){
    gigya.socialize.showLoginUI(gigyaSettings.login);
  }
});
