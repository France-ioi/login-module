"use strict";
/* Main JS file for server-side login iframe, July 2014
 *
 * This file in based on the angularJS technology: it registers a "login"
 * controller. Most functions are in the global object "loginManager", which
 * is equivalent to an angularJS service, but not registered as such.
 *
 * This file also loads the Facebook JS SDK.
 *
 */

function checkTimeZone() {
   var rightNow = new Date();
   var date1 = new Date(rightNow.getFullYear(), 0, 1, 0, 0, 0, 0);
   var date2 = new Date(rightNow.getFullYear(), 6, 1, 0, 0, 0, 0);
   var temp = date1.toGMTString();
   var date3 = new Date(temp.substring(0, temp.lastIndexOf(" ") - 1));
   temp = date2.toGMTString();
   var date4 = new Date(temp.substring(0, temp.lastIndexOf(" ") - 1));
   var hoursDiffStdTime = (date1 - date3) / (1000 * 60 * 60);
   var hoursDiffDaylightTime = (date2 - date4) / (1000 * 60 * 60);
   if (hoursDiffDaylightTime == hoursDiffStdTime)
      return {
         offset: hoursDiffStdTime,
         bDLS: 0
      };
   else
      return {
         offset: hoursDiffStdTime,
         bDLS: 1
      };
}

// simple and clean postMessage exchange. Might have to be changed to complex
// and ugly jQuery.postMessage for old browser compatibility.
function getMessageFromPopup(event)  {
   //if (event.origin != "https://loginaws.algorea.org")
   //   return;
   try {
   var message = JSON.parse(event.data);
   } catch (e) {return;}
   if (message.action === 'logged') {
      loginManager.logged(message.login, message.token, message.provider);
   } else if (message.action === 'loginFailed') {
      loginManager.loginFailed();
   }
}

window.addEventListener("message", getMessageFromPopup, false);

function consoleLog(message) {
   if (window.console) {
      console.log(message);
   }
}

function getUrlVars() {
   var vars = {};
   var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
   for (var i = 0; i < hashes.length; i++) {
      var hash = hashes[i].split('=');
      vars[hash[0]] = hash[1];
   }
   return vars;
}

function getHashParams() {
   // First, parse the query string
   var params = {};
   var queryString = location.hash.substring(1);
   var regex = /([^&=]+)=([^&]*)/g;
   var param;
   while ((param = regex.exec(queryString))) {
      params[decodeURIComponent(param[1])] = decodeURIComponent(param[2]);
   }
   return params;
}

function preventDefault(event) {
   if (event.preventDefault) {
      event.preventDefault();
   } else {
      event.returnValue = false;
   }
}

function loadSession($scope, $http) {
   var infosTimeZone = checkTimeZone();
   var url = config.selfBaseUrl + "session.php?sTimeZoneOffset=" + infosTimeZone.offset + "&bDLS=" + infosTimeZone.bDLS;
   return $http.get(url).success(function(data) {
      session = data;
      $scope.session = session;
   });
}

// for browsers not supporting parent url simply
var parent_url = decodeURIComponent(document.location.hash.replace(/^#/, ''));

function postLoginMessage(request, content) {
   parent.postMessage(JSON.stringify({
      source: 'loginModule',
      content: content,
      request: request
   }), '*');
}

var session;

angular.module('login', [])
   .controller('LoginCtrl', function($scope, $http, $timeout, $interval) {
      $scope.step = "";
      $scope.session = session;
      loginManager.scope = $scope;
      loadSession($scope, $http).then(function() {
         var params = getUrlVars();
         if (params.login === "1") {
            $scope.step = "login";
            $("#loginForm").show();
         } else if (params.properties === "1") {
            $scope.step = "properties";
         } else if (params.recover === "1") {
            $scope.step = "recover";
         } else if (params.sRecover) {
            $scope.step = "recovered";
            $scope.recoverLoading = true;
            loginManager.checkRecoverCode(params.sRecover, params.sLogin);
         } else if (params.newUser === "1") {
            $scope.step = "newLogin";
            $("#newLoginForm").show();
         } else if (params.close === "1") {
            window.close();
         } else if (!loginManager.handleOAuthData()) {
            if ((session.idUser === -1) || (session.idUser === undefined)) {
               $scope.step = "notConnected";
               postLoginMessage('notlogged', null);
               //            $("#loginButtons").show();
            } else {
               $scope.step = "connected";
               postLoginMessage('login', {
                  login: session.sLogin,
                  token: session.sToken
               });
               //            $("#logoutButton").show();
            }
            $scope.setInterval();
         }
      });

      $scope.refreshSession = function() {
         var infosTimeZone = checkTimeZone();
         var url = config.selfBaseUrl + "session.php?sTimeZoneOffset=" + infosTimeZone.offset + "&bDLS=" + infosTimeZone.bDLS;
         $http.get(url).success(function(data) {
              // we just want to refresh the session, not handle differences, each client must handle differences himself
//            if (data.idUser != $scope.session.idUser) {
//               session = data;
//               $scope.session = data;
//               if ((session.idUser === -1) || (session.idUser === undefined)) {
//                  $scope.step = "notConnected";
//                  postLoginMessage('notlogged', null);
//               } else {
//                  $scope.step = "connected";
//                  postLoginMessage('login', {
//                     login: session.sLogin,
//                     token: session.sToken
//                  });
//               }
//            }
         });
      };

      $scope.interval = null;
      $scope.setInterval = function() {
         if (!$scope.interval) {
            $scope.interval = $interval($scope.refreshSession, 600000);
         }
      };
      // I like global variables
      window.scope_setInterval = $scope.setInterval;

      $scope.submitNewLogin = function() {
         loginManager.createAccount($scope.login, "", "");
      };
      $scope.recoverPassword = function() {
         loginManager.recoverPassword();
      };
      $scope.openPasswordRecovery = function() {
         loginManager.openPasswordRecovery();
      };
      $scope.createUser = function() {
         loginManager.createUser($scope.login, $scope.email, $scope.password1, $scope.password2);
      };
      $scope.removeGoogle = loginManager.removeGoogle;
      $scope.removeFacebook = loginManager.removeFacebook;
      $scope.passwordLogin = function() {
         $scope.session.errorMessage = "";
         loginManager.passwordLogin($scope.login, $scope.password1);
      };
      $scope.loginWith = function(event, provider) {
         preventDefault(event);
         loginManager.loginWith(provider);
      };
      $scope.openerLoginWith = function(event, provider) {
         preventDefault(event);
         window.opener.loginManager.loginWith(provider);
      };
      $scope.loginCancelled = function(event) {
         preventDefault(event);
         loginManager.loginCancelled();
      };
      $scope.logout = function() {
         loginManager.logout();
      };
      $scope.properties = function() {
         loginManager.properties();
      };
      $scope.apply = function(f) {
         $timeout(function(){$scope.$apply(f);});
      };
      $scope.sendNewPassword = function() {
         var newPassword = $('#newPassword').val();
         var newPasswordConf = $('#newPasswordConf').val();
         if (!newPassword || newPassword != newPasswordConf) {
            alert('Les mots de passe sont différents');
         } else if (newPassword.length < 6) {
            alert('le mot de passe doit faire au moins 6 caractères');
         } else {
            loginManager.updatePassword(newPassword);
         }
      };
   });

var loginManager = {
   accessToken: "",
   accessProvider: "",
   loggedOnFacebook: false,

   logged: function(login, token, provider) {
      if (typeof selfTarget !== "undefined") { // If used on france-ioi's website TODO: find a better way to check that
         window.location.href = config.selfBaseUrl + selfTarget;
      } else {
         this.accessToken = token;
         this.accessProvider = provider;
         var scope = angular.element("#LoginCtrl").scope();
         scope.apply(function() {
            scope.step = "connected";
            scope.session.sLogin = login;
            scope.session.sProvider = provider;
         });
         postLoginMessage('login', {
            login: login,
            token: token
         });
         scope_setInterval();
      }
   },

   loginCancelled: function() {
      postLoginMessage('cancel');
   },

   setErrorMessage: function(message) {
      var scope = angular.element("#LoginCtrl").scope();
      scope.session.sErrorMessage = message;
      scope.apply(function() {
         scope.session.sErrorMessage = message;
      });
   },

   loginFailed: function() {
      setTimeout(function() {
         loginManager.setErrorMessage(translate("login_failed"));
      }, 100);
   },

   checkLogged: function(data, token, closeIfFailed) {
      data = $.parseJSON(data);
      if (!token && data.token && data.token !== '') {
         token = data.token;
      }
      var refWindow = window;
      if (window.opener) {
         refWindow = window.opener;
      }
      if (data.success) {
         var login = data.login;
         if (login === "") {
            if (data.provider != 'facebook') {
               window.location = config.selfBaseUrl + "login.html?newUser=1&provider=" + data.provider;
            } else {
               // prevent loop with facebook auth:
               var urlVars = getUrlVars();
               if (urlVars.newUser != 1 || urlVars.provider != 'facebook') {
                  window.location = config.selfBaseUrl + "login.html?newUser=1&provider=facebook";
               }
            }
         } else {
            //refWindow.loginManager.logged(login, token, data.provider);
            refWindow.postMessage(JSON.stringify({action: "logged", login: login, token: token, provider: data.provider}), '*');
            if (window.opener) {
               var params = getUrlVars();
               if (params.properties !== '1') {
                  window.close();
               }
            }
         }
      } else {
         if (data.error) {
            console.error(data.error);
         }
         if (closeIfFailed) {
            refWindow.postMessage(JSON.stringify({action: "loginFailed"}), '*');
            window.close();
         } else {
            loginManager.loginFailed();
         }
      }
   },

   facebookLoginCallback: function(user) {
      var token = window.location.hash.substring(1);
      var encodedEmail = encodeURIComponent(user.email);
      var encodedID = encodeURIComponent("http://www.facebook.com/" + user.id);
      $.ajax({
         url: config.selfBaseUrl + "validateUser.php?" + "access_token=" + token + "&provider=facebook&id=" + encodedID + "&email=" + encodedEmail,
         context: document.body,
         async: false,
         success: function(data) {
            loginManager.checkLogged(data, token, true);
         }
      }).done(function() {});
   },

   handleOAuthData: function() {
      var params = getHashParams();
      if (params.state === undefined) {
         return false;
      }
      var accessToken = params.access_token;
      var provider = params.state;
      if (provider === "facebook") {
         var path = "https://graph.facebook.com/me?";
         var queryParams = ["access_token=" + accessToken, 'callback=loginManager.facebookLoginCallback'];
         // use jsonp to call the graph
         var script = document.createElement('script');
         script.src = path + queryParams.join('&');
         document.body.appendChild(script);
      }
      return true;
   },

   passwordLogin: function(login, password) {
      $.ajax({
         url: config.selfBaseUrl + "validateUser.php?login=" + encodeURIComponent(login) +
            "&password=" + encodeURIComponent(password),
         context: document.body,
         async: false,
         success: function(data) {
            loginManager.checkLogged(data, null, false);
         }
      }).done(function() {});
      return true;
   },

   createAccount: function(login, email, password) {
      $.ajax({
         url: config.selfBaseUrl + "validateUser.php?" +
            "newLogin=" + encodeURIComponent(login) +
            "&email=" + encodeURIComponent(email) +
            "&password=" + encodeURIComponent(password),
         context: document.body,
         async: false,
         success: function(data) {
            data = $.parseJSON(data);
            if (data.success) {
               if (window.opener) {
                  window.opener.postMessage(JSON.stringify({action: "logged", login: data.login, token: data.token, provider: 'password'}), '*');
                  //window.opener.loginManager.logged(data.login, data.token, 'password');
                  window.close();
               } else {
                  loginManager.logged(data.login, data.token, 'password');
               }
            } else {
               alert("Erreur : " + data.error);
            }
         }
      }).done(function() {});
   },

   createUser: function(login, email, password1, password2) {
      if (password1 !== password2) {
         alert(translate("passwords_different"));
         return;
      }
      if (!email) {email = '';}
      this.createAccount(login, email, password1);
   },

   logoutFromProvider: function(provider) {
      if (provider === "facebook") {
         FB.logout(function() {
            consoleLog("you are now logged out of facebook");
         });
      } else if (provider === "google") {
         var url = "https://accounts.google.com/Logout";
         this.openLoginPopup(url);
      }
   },

   updatePassword: function(newPassword) {
      $.get(config.selfBaseUrl + 'validateUser.php', {action: 'updatePassword', newPassword: newPassword}, function(res) {
         if (!res.success) {
            alert("Erreur : " + res.error);
            return;
         }
         session.hasPassword = false;
         var scope = angular.element("#LoginCtrl").scope();
         scope.apply(function() {
            scope.session.hasPassword = true;
         });
      }, 'json');
   },

   removeGoogle: function() {
      if (!session.hasGoogle){
         alert('Vous n\'avez pas d\'identité Google à supprimer');
         return;
      }
      if (!session.hasPassword && !session.hasFacebook){
         alert('Vous ne pouvez pas supprimer votre identité Google car vous n\'avez pas d\'autre moyen de vous identifier à votre compte.');
         return;
      }
      $.get(config.selfBaseUrl + 'validateUser.php', {action: 'removeGoogle'}, function(res) {
         if (!res.success) {
            alert("Erreur : " + res.error);
            return;
         }
         session.hasGoogle = false;
         var scope = angular.element("#LoginCtrl").scope();
         scope.apply(function() {
            scope.session.hasGoogle = false;
         });
      }, 'json');
   },

   removeFacebook: function() {
      if (!session.hasFacebook){
         alert('Vous n\'avez pas d\'identité Facebook à supprimer');
         return;
      }
      if (!session.hasPassword && !session.hasGoogle){
         alert('Vous ne pouvez pas supprimer votre identité Facebook car vous n\'avez pas d\'autre moyen de vous identifier à votre compte.');
         return false;
      }
      $.get(config.selfBaseUrl + 'validateUser.php', {action: 'removeFacebook'}, function(res) {
         if (!res.success) {
            alert("Erreur : " + res.error);
            return;
         }
         session.hasFacebook = false;
         var scope = angular.element("#LoginCtrl").scope();
         scope.apply(function() {
            scope.session.hasFacebook = false;
         });
         FB.logout(function() {
            consoleLog("you are now logged out of facebook");
         });
      }, 'json');
   },

   googleAdded: function(success, error) {
      if (error) {
         alert('Erreur: '+error);
         return;
      }
      session.hasGoogle = true;
      var scope = angular.element("#LoginCtrl").scope();
      scope.apply(function() {
         scope.session.hasGoogle = true;
      });
   },

   logout: function() {
      $.ajax({
         url: config.selfBaseUrl + "logout.php",
         success: function() {},
         method: 'POST',
         data: { token: session.sToken },
      }).done(function() {
         var scope = angular.element("body").scope();
         scope.apply(function() {
            scope.session.sLogin = "";
            scope.step = "notConnected";
         });
         var provider = session.sProvider ? session.sProvider : this.accessProvider;
         if (provider && provider !== "password") {
            if (confirm(translate("logout_from_provider", [provider]))) {
               loginManager.logoutFromProvider(provider);
               this.accessProvider = '';
               scope.session.sProvider = '';
            }
         }
         postLoginMessage('logout', null);
      });
   },

   openLoginPopup: function(url) {
      this.popup = window.open(url, "loginPopup", "height=555, width=510, toolbar=yes, menubar=yes, scrollbars=no, resizable=no, location=no, directories=no, status=no");
   },

   urlLoginWithOpenID: function(provider, reauthenticate) {
      var realm = encodeURIComponent(config.Google0Auth2.realm);
      var redirect_uri = encodeURIComponent(config.Google0Auth2.redirect_uri);
      var client_id = encodeURIComponent(config.Google0Auth2.client_id);
      // This url is what is generated by LightOpenID. We avoid a return trip by hard-coding it.
      var url = "https://accounts.google.com/o/oauth2/auth" +
         "?response_type=code" +
         "&redirect_uri=" + redirect_uri +
         "&client_id=" + client_id +
         "&scope=openid+email" +
         "&access_type=online" +
         "&approval_prompt=auto" +
         "&openid.realm=" + realm; // thanks to this line, we can have old IDs
      if (provider === "google" && reauthenticate) {
         url += "&max_auth_age=0"; // untested... when is reauthenticate true anyway?
      }
      return url;
   },

   urlLoginWithPassword: function() {
      return config.selfBaseUrl + "login.html?login=1";
   },

   loginWith: function(provider) {
      this.accessProvider = provider;
      var url;
      if (provider === "facebook") {
         if (!this.loggedOnFacebook) {
            FB.login();
         } else {
            this.connectLoggedFacebookUser();
         }
         //$('#loginbutton,#feedbutton').removeAttr('disabled');
         //FB.getLoginStatus(updateStatusCallback);
         return;
      } else if (provider === "google") {
         url = this.urlLoginWithOpenID('google', false);
      } else if (provider === "password") {
         url = this.urlLoginWithPassword(false);
      } else {
         this.setErrorMessage("Invalid login provider : " + provider);
         return;
      }
      this.openLoginPopup(url);
   },

   properties: function() {
      this.openLoginPopup(config.selfBaseUrl+'login.html?properties=1');
   },

   openPasswordRecovery: function() {
      this.openLoginPopup(config.selfBaseUrl+'login.html?recover=1');
   },

   checkRecoverCode: function(recoverCode, recoverLogin) {
      $.get(config.selfBaseUrl + 'validateUser.php', {action: 'checkRecoverCode', recoverLogin: recoverLogin, recoverCode: recoverCode}, function(res) {
         var scope = angular.element("#LoginCtrl").scope();
         if (!res.success) {
            alert("Erreur : " + res.error);
            scope.apply(function() {
               scope.recoverLoading = false;
               scope.recoverError = !res.success;
            });
            return;
         }
         scope.apply(function() {
            scope.recoverLoading = false;
            scope.recoverError = !res.success;
            scope.recoverPassword = res.newPassword;
         });
      }, 'json');
   },

   recoverPassword: function() {
      var recoverLogin = $('#recoverLogin').val();
      var recoverEmail = $('#recoverEmail').val();
      var scope = angular.element("#LoginCtrl").scope();
      scope.apply(function() {
         scope.recoverLoading = true;
      });
      $.get(config.selfBaseUrl + 'validateUser.php', {action: 'recoverPassword', recoverLogin: recoverLogin, recoverEmail: recoverEmail}, function(res) {
         scope.recoverLoading = false;
         if (!res.success) {
            alert("Erreur : " + res.error);
            return;
         }
         scope.apply(function() {
            scope.mailSent = true;
         });
      }, 'json');
   },

   connectLoggedFacebookUser:  function() {
      var that = this;
      FB.api('/me', function() {
         $.ajax({
            url: config.selfBaseUrl + "validateUser.php?provider=facebook",
            context: document.body,
            async: false,
            success: function(data) {
               var dataObject = $.parseJSON(data);
               if (!dataObject.addingId) {
                  loginManager.checkLogged(data, null, true);
               } else {
                  if (!dataObject.success) {
                     FB.logout(function() {
                        consoleLog("you are now logged out of facebook");
                     });
                     alert('Erreur: '+dataObject.error);
                     return;
                  }
                  if(that.popup && !that.popup.closed){
                     that.popup.location.reload(true);
                  }
                  var scope = angular.element("body").scope();
                  scope.apply(function() {
                     scope.session.hasFacebook = true;
                  });
               }
            }
         });
      });
   }
};

// Load the SDK asynchronously
(function(d, s, id) {
   var js, fjs = d.getElementsByTagName(s)[0];
   if (d.getElementById(id)) return;
   js = d.createElement(s); js.id = id;
   js.src = "//connect.facebook.net/fr_FR/sdk.js";
   fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

window.fbAsyncInit = function() {
   // Here we subscribe to the auth.authResponseChange JavaScript event. This event is fired
   // for any authentication related change, such as login, logout or session refresh. This means that
   // whenever someone who was previously logged out tries to log in again, the correct case below
   // will be handled.
   FB.init({
      appId:   config.Facebook.appId, // App ID
      version: 'v2.2',
      status:  false, // check login status
      cookie:  true, // enable cookies to allow the server to access the session
      xfbml:   false // parse XFBML
   });
   FB.Event.subscribe('auth.authResponseChange', function(response) {
      // Here we specify what we do with the response anytime this event occurs.
      if (response.status === 'connected') {
         // The response object is returned with a status field that lets the app know the current
         // login status of the person. In this case, we're handling the situation where they
         // have logged in to the app.
         loginManager.loggedOnFacebook = true;
      } else if (response.status === 'not_authorized') {
         // In this case, the person is logged into Facebook, but not into the app, so we call
         // FB.login() to prompt them to do so.
         // In real-life usage, you wouldn't want to immediately prompt someone to login
         // like this, for two reasons:
         // (1) JavaScript created popup windows are blocked by most browsers unless they
         // result from direct interaction from people using the app (such as a mouse click)
         // (2) it is a bad experience to be continually prompted to login upon page load.
         //console.log('login not authorized');
         //FB.login();
      } else {
         // In this case, the person is not logged into Facebook, so we call the login()
         // function to prompt them to do so. Note that at this stage there is no indication
         // of whether they are logged into the app. If they aren't then they'll see the Login
         // dialog right after they log in to Facebook.
         // The same caveats as above apply to the FB.login() call here.
         FB.login();
      }
   });
};
