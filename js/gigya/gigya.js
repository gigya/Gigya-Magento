/*
 *gigya functions
 */
var gigyaFunctions = gigyaFunctions || {};
var gigyaCache = {};
gigyaFunctions.login = function (response) {
  gigyaCache.uInfo = response;
  new Ajax.Request('/gigyalogin/login/login', {
      parameters: {json:JSON.stringify(response)},
      onSuccess: function (trans) {
        if (typeof trans.responseJSON.result !== 'undefined') {
          switch (trans.responseJSON.result)
          {
          case 'newUser':
          case 'login':
            window.location.reload();
            break;
          case 'noEmail':
            gigyaFunctions.hideLogin(trans.responseJSON.id);
            gigyaFunctions.updateHeadline(trans.responseJSON.id, trans.responseJSON.headline)
            $(trans.responseJSON.id).update(trans.responseJSON.html);
            break;
          case 'emailExsists':
            gigyaFunctions.updateHeadline(trans.responseJSON.id, trans.responseJSON.headline)
            gigyaFunctions.hideLogin(trans.responseJSON.id);
            $(trans.responseJSON.id).update(trans.responseJSON.html);
            $(trans.responseJSON.id).style.height = '';
            Form.Element.setValue('gigya-mini-login', gigyaCache.uInfo.user.email);
            break;
          }
        }
      }
  });
};

gigyaFunctions.hideLogin = function (id) {
  var form = $(id).adjacent('li');
  if (form !== 'undefined') {
    form.each(function (e) {
      if ((e.firstDescendant().readAttribute('for') == 'email') || (e.firstDescendant().readAttribute('for') == 'pass')){
        e.hide();
      }
    });
  }
};

gigyaFunctions.updateHeadline = function (id, text) {
  var headline = $(id).previous(0);
  if (typeof headline !== 'undefined') {
    headline.remove();
  }

};


gigyaFunctions.linkAccounts = function () {
  var email = $$('#gigya-mini-login')[0].value,
  password = $$('#gigya-mini-password')[0].value;
  if (email.empty()){
    alert('Please enter a email');
  }
  else if (password.empty()){
    alert('Please enter your password');
  }
  else {
  var toPost = {username:email, password:password};
  new Ajax.Request('/gigyalogin/login/loginPost', {
      parameters: {login:JSON.stringify(toPost)},
      onSuccess: function (trans) {
        if (trans.responseJSON.result === 'success') {
          document.location.reload(true);
        }
        if (trans.responseJSON.result === 'error') {
          alert(trans.responseJSON.message);
        }
      }
  });
  }
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
          if (trans.responseJSON.result === 'emailExsists') {
            gigyaFunctions.hideLogin(trans.responseJSON.id);
            gigyaFunctions.updateHeadline(trans.responseJSON.id, trans.responseJSON.headline)
            $(trans.responseJSON.id).update(trans.responseJSON.html);
            Form.Element.setValue('gigya-mini-login', gigyaCache.uInfo.user.email);
          }
        }
    }
    );
  }
  else {
    alert ('please enter a valid email');
  }
};

gigyaFunctions.shareBar = function (settings) {
  var mediaObj = {type: 'image', href: settings.ua.linkBack};
  switch (settings.imageBehavior)
  {
  case 'default':
    if ($$('meta[property=og:image]').size() > 0) {
      mediaObj.src = $$('meta[property=og:image]').readAttribute('content');
    }
    else {
      mediaObj.src = settings.ua.imageUrl;
    }
    break;
  case 'product':
    mediaObj.src = settings.ua.imageUrl;
    break;
  case 'url':
    if (typeof settings.imageUrl !== 'undefined') {
      mediaObj.src = settings.imageUrl;
    }
    break;
  }
  var ua = new gigya.socialize.UserAction();
  ua.setLinkBack(settings.ua.linkBack);
  ua.setTitle(settings.ua.title);
  ua.addActionLink(settings.ua.title, settings.ua.linkBack);
  ua.setDescription(settings.description);
  ua.addMediaItem(mediaObj);
  delete settings.ua;
  delete settings.imageBehavior;
  if (typeof settings.imageUrl !== 'undefined') {
    delete settings.imageUrl;
  }
  settings.userAction = ua;
  gigya.socialize.showShareBarUI(settings);
};

gigyaFunctions.shareAction = function (settings) {
  var mediaObj = {type: 'image', href: settings.ua.linkBack};
  mediaObj.src = settings.ua.imageUrl;
  var ua = new gigya.socialize.UserAction();
  ua.setLinkBack(settings.ua.linkBack);
  ua.setTitle(settings.ua.title);
  ua.addActionLink(settings.ua.title, settings.ua.linkBack);
  ua.setDescription(settings.ua.description);
  ua.addMediaItem(mediaObj);
  delete settings.ua;
  delete settings.enable;
  settings.userAction = ua;
  gigya.socialize.showShareUI(settings);
};


gigyaFunctions.reactions = function (settings) {
  var mediaObj = {type: 'image', href: settings.ua.linkBack};
  switch (settings.imageBehavior)
  {
  case 'default':
    if ($$('meta[property=og:image]').size() > 0) {
      mediaObj.src = $$('meta[property=og:image]').readAttribute('content');
    }
    else {
      mediaObj.src = settings.ua.imageUrl;
    }
    break;
  case 'product':
    mediaObj.src = settings.ua.imageUrl;
    break;
  case 'url':
    if (typeof settings.imageUrl !== 'undefined') {
      mediaObj.src = settings.imageUrl;
    }
    break;
  }
  var ua = new gigya.socialize.UserAction();
  ua.setLinkBack(settings.ua.linkBack);
  ua.setTitle(settings.ua.title);
  ua.addActionLink(settings.ua.title, settings.ua.linkBack);
  ua.setDescription(settings.description);
  ua.addMediaItem(mediaObj);
  delete settings.ua;
  delete settings.imageBehavior;
  if (typeof settings.imageUrl !== 'undefined') {
    delete settings.imageUrl;
  }
  eval('var reactions = [' + settings.reactions + ']');
  settings.reactions = reactions;
  settings.userAction = ua;
  gigya.socialize.showReactionsBarUI(settings);
};

gigyaFunctions.gm = function (settings) {
  if (typeof settings.notifications !== 'undefined') {
    gigya.gm.showNotifications();
  }
  if (typeof settings.plugins !== 'undefined') {
    $H(settings.plugins).each ( function (gmPlugin) {
      var parms = {containerID: gmPlugin.value};
      switch (gmPlugin.key)
      {
        case 'Achievements':
          gigya.gm.showAchievementsUI(parms);
        break;
        case 'ChallengeStatus':
          gigya.gm.showChallengeStatusUI(parms);
        break;
        case 'UserStatus':
          gigya.gm.showUserStatusUI(parms);
        break;
        case 'Leaderboard':
          gigya.gm.showLeaderboardUI(parms);
        break;
        }
    })

  }
};

gigyaFunctions.ratings = function (settings) {
  settings.each( function (ins) {
    ins.onAddReviewClicked = gigyaFunctions.goToReviews;
    ins.onReadReviewsClicked = gigyaFunctions.goToReviews;
    gigya.socialize.showRatingUI(ins);
  });
};

gigyaFunctions.goToReviews = function (eventObj) {
  if (typeof eventObj.context.reviewUrl !== 'undefined') {
    document.location = eventObj.context.reviewUrl;
  }
};

gigyaFunctions.postReview = function (eventObj) {
  var ratings = [],
  r = eventObj.ratings._overall;
  var i = 1;
  for (i; i <= 3; i++) {
    ratings[i] = r;
    r = r + 5;
  };
  var toPost = {
    nickname:eventObj.user.firstName,
    title:eventObj.commentTitle,
    detail:eventObj.commentText,
    ratings:ratings
  };
  var reviewsUrl = '/gigyareviews/reviews/post',
  id = '',
  category = '';
  if (id = gigyaFunctions.getUrlParam('id')) {
    reviewsUrl += '/id/' + id;
  }
  if (category = gigyaFunctions.getUrlParam('category')) {
    reviewsUrl += '/category/' + category;
  }
  new Ajax.Request(reviewsUrl, {
      parameters: {json:JSON.stringify(toPost)},
      onSuccess: function (trans) {
        //TODO: add success/error handeling
      }
  }
  );


};
gigyaFunctions.RnR = function (settings) {
  $$('table.ratings-table')[0].writeAttribute('id', settings.containerID);
  settings.linkedCommentsUI = 'customer-reviews';
  var reviews = {
    containerID: 'customer-reviews',
    categoryID: settings.categoryID,
    streamID: settings.streamID,
    onCommentSubmitted: gigyaFunctions.postReview,
  };
  gigya.socialize.showRatingUI(settings);
  gigya.socialize.showCommentsUI(reviews);
};

gigyaFunctions.getUrlParam = function (param) {
  var urlArray = document.location.href.split('/'),
  idx = urlArray.indexOf(param);
  if (idx !== -1) {
    return urlArray[idx + 1];
  }
  return false;
}

/*
 * register events
 */
function gigyaRegister() {
  if (typeof gigya !== 'undefined') {
    gigya.socialize.addEventHandlers({
        onLogin: gigyaFunctions.login
    });
  }
}

gigyaRegister();

document.observe("dom:loaded", function() {
  if (typeof gigyaSettings !== 'undefined'){
    $H(gigyaSettings).each( function (plugin) {
      delete plugin.value.enable;
      switch (plugin.key)
      {
      case 'login':
        gigya.socialize.showLoginUI(plugin.value);
        break;
      case 'linkAccount':
        gigya.socialize.showAddConnectionsUI(plugin.value);
        break;
      case 'sharebar':
        gigyaFunctions.shareBar(plugin.value);
        break;
      case 'shareAction':
        gigyaFunctions.shareAction(plugin.value);
        break;
      case 'reactions':
        gigyaFunctions.reactions(plugin.value);
        break;
      case 'comments':
        gigya.socialize.showCommentsUI(plugin.value);
        break;
      case 'activityFeed':
        gigya.socialize.showFeedUI(plugin.value);
        break;
      case 'gm':
        gigyaFunctions.gm(plugin.value);
        break;
      case 'ratings':
        gigyaFunctions.ratings(plugin.value);
        break;
      case 'RnR':
        gigyaFunctions.RnR(plugin.value);
        break;
      }
    });
  }
});
