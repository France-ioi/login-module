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

var debugMode = false;

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
      loginManager.logged(message.login, message.token, message.provider, message.loginData);
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
   var endIndex = window.location.href.indexOf('#');
   endIndex = endIndex == -1 ? undefined : endIndex;
   var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1, endIndex).split('&');
   for (var i = 0; i < hashes.length; i++) {
      var hash = hashes[i].split('=');
      vars[hash[0]] = decodeURIComponent(hash[1]);
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
   return $http.get(url).then(function(response) {
      var data = response.data;
      session = data;
      $scope.session = session;
   });
}

function translateError(ajaxData) {
   return window.i18next.t(ajaxData.error, ajaxData.errorArgs);
}

// for browsers not supporting parent url simply
var parent_url = decodeURIComponent(document.location.hash.replace(/^#/, ''));

// here we do a few things to work around the incapacity of IE to talk to
// its popups
var channelToParent = null;
var potentialCommunicationImpossibility = true;
function getChannelToParent(callback) {
   if (channelToParent) {
      callback(channelToParent); 
      return;
   }
   channelToParent = Channel.build({
      window: window.opener,
      origin: "*",
      scope: "loginModule",
      onReady: function() {
         callback(channelToParent);
      }
   });
}

if (window.opener) {
   getChannelToParent(function() {
      potentialCommunicationImpossibility = false;
   });
}

var lastrequest = null; // for the unfamous IE hack
var lastcontent = null;

function postLoginMessage(request, content, callback) {
   lastrequest = request;
   lastcontent = content;
   var target = window.parent;
   if (window.parent == window.self) {
      // postMessage doesn't work for popups in IE11
      target = window.opener;
      if (!target) return;
      if (potentialCommunicationImpossibility) {
         callback();
      }
      getChannelToParent(function() {
         channelToParent.call({
            method: "loginMessage",
            params: {request: request, content: content},
            success: function(v) {
               callback();
            },
            error: function(v) {
               callback();
            }
         });
      });
   } else {
      target.postMessage(JSON.stringify({
         source: 'loginModule',
         content: content,
         request: request
      }), '*');
      callback();
   }
}

// the IE hack
function closeAfterMessage() {
   if (potentialCommunicationImpossibility && loginManager.scope.fallbackReturnUrl && lastrequest == "login") {
      var newUrl = loginManager.scope.fallbackReturnUrl;
      newUrl += '?request='+encodeURIComponent(lastrequest);
      newUrl += '&content='+encodeURIComponent(JSON.stringify(lastcontent));
      if (!debugMode) window.location.replace(newUrl);
   } else {
      if (!debugMode) window.close();
   }
}

var session;

function checkAgainstRequired(infos, requiredFields) {
   for (var i = 0; i < requiredFields.length; i++) {
      var requiredField = requiredFields[i];
      if (!infos[requiredField]) {
         return false;
      }
   }
   return true;
}

function checkAgainstRequiredBadge(session, requiredBadge) {
   if (!session || !requiredBadge) {
      return true;
   }
   if (!session.aBadges) return false;
   for (var i = 0; i < session.aNotBadges.length; i++) {
      if (session.aNotBadges[i] == requiredBadge) {
         return true;
      }
   }
   for (var i = 0; i < session.aBadges.length; i++) {
      if (session.aBadges[i] == requiredBadge) {
         return true;
      }
   }
   return false;
}

angular.module('login', ['jm.i18next'])
   .controller('LoginCtrl', function($scope, $http, $timeout, $interval, $i18next) {
      $scope.step = "";
      $scope.hasPMS = false;
      $scope.autoLogout = false;
      $scope.popupMode = false;
      $scope.session = session;
      $scope.infos = {};
      $scope.badgeInfos = {};
      loginManager.scope = $scope;
      loadSession($scope, $http).then(function() {
         $scope.infos.sFirstName = session.sFirstName;
         $scope.infos.sLastName = session.sLastName;
         $scope.infos.sStudentId = session.sStudentId;
         var params = getUrlVars();
         if (params.large === "1") {
            $scope.largeMode = true;
         }
         if (params.requiredFields) {
            $scope.requiredFields = params.requiredFields.split(',');
         }
         // not sure about the way to specify multiple required badges with their types
         if (params.requiredBadge) {
            $scope.requiredBadge = params.requiredBadge;
            $scope.requiredBadgeType = $scope.requiredBadgeType ? $scope.requiredBadgeType : 'code';
            if (params.badgeCode) {
               $scope.badgeInfos.code = params.badgeCode;
            }
         }
         if (params.autoLogout === '1') {
            $scope.autoLogout = true;
         }
         if (params.mode === "popup") {
            $scope.popupMode = true;
         }
         if (params.hasPMS === "1") {
            $scope.hasPMS = true;
         }
         if (params.beInsistentWithBadge === "1") {
            $scope.beInsistentWithBadge = true;
         }
         $scope.fallbackReturnUrl = params.fallbackReturnUrl;
         if (params.login === "1") {
            if ($scope.requiredBadge) {
               $scope.step = "badgeOrNormal";
            } else {
               $scope.step = "login";
            }
         } else if (params.properties === "1") {
            $scope.step = "properties";
         } else if (params.changePass === "1") {
            $scope.step = "changePass";
         } else if (params.recover === "1") {
            $scope.step = "recover";
         } else if (params.setInfos === "1") {
            $scope.step = "infos";
         } else if (params.sRecover) {
            $scope.step = "recovered";
            $scope.recoverLoading = true;
            loginManager.checkRecoverCode(params.sRecover, params.sLogin);
         } else if (params.newUser === "1") {
            $scope.step = "newLogin";
         } else if (params.close === "1") {
            if (!debugMode) window.close();
         } else if (!loginManager.handleOAuthData()) {
            if ((session.idUser === -1) || (session.idUser === undefined)) {
               if ($scope.requiredBadge) {
                  if ($scope.badgeInfos.code) {
                     $scope.step = 'badgeCreationConfirm';
                     $scope.submitBadgeInfos();
                  } else {
                     $scope.step = "notConnected";
                  }
               } else {
                  $scope.step = "notConnected";
                  postLoginMessage('notlogged', null, function() {});
               }
            } else {
               $scope.step = "connected";
               if ($scope.autoLogout) {
                  $scope.logout();
               } else {
                  if ($scope.requiredFields && !checkAgainstRequired($scope.infos, $scope.requiredFields)) {
                     $scope.infosError = null;
                     $scope.step = 'infos';
                     $scope.$applyAsync();
                  } else if ($scope.requiredBadge && !checkAgainstRequiredBadge($scope.session, $scope.requiredBadge)) {
                     $scope.step = 'badgeInfos';
                     $scope.autoVerifyBadgeFromUrl();
                     $scope.$applyAsync();
                  } else {
                     postLoginMessage('login', {
                        login: session.sLogin,
                        token: session.sToken
                     }, function() {
                        if ($scope.popupMode) {
                           closeAfterMessage();
                        }
                     });
                  }
               }
            }
            $scope.setInterval();
         }
      });

      $scope.autoVerifyBadgeFromUrl = function() {
         if ($scope.badgeInfos.code) {
            $scope.submitBadgeInfos();
         }
      };

      $scope.iDontHaveThisBadge = function() {
         $scope.badgeLoading = true;
         var badge = $scope.requiredBadge;
         $.ajax({
            url: config.selfBaseUrl + "badgeApi.php",
            context: document.body,
            dataType: 'json',
            method: 'POST',
            data: {action: 'iDontHaveThisBadge', badgeUrl: badge},
            success: function(data) {
               if (!data.success) {
                  $scope.badgeLoading = false;
                  $scope.badgeError = translateError(data);
                  $scope.$applyAsync();
                  $scope.forceLogin();
                  return;
               }
               $scope.session.aNotBadges.push(badge);
               $scope.forceLogin();
            }
         });
      };

      $scope.submitBadgeInfos = function() {
         $scope.badgeError = null;
         if (!$scope.requiredBadge) {
            //?
            return;
         }
         if ($scope.requiredBadgeType == 'code') {
            if (!$scope.badgeInfos.code) {
               $scope.badgeError = 'missing_code';
            }            
         } else {
            console.error('unknown badge verification type!');
            return;
         }
         var action = 'getInfos';
         if ($scope.session && $scope.session.idUser) {
            action = 'attachBadge';
         }
         $scope.badgeLoading = true;
         $.ajax({
            url: config.selfBaseUrl + "badgeApi.php",
            context: document.body,
            dataType: 'json',
            method: 'POST',
            data: {action: action, badgeUrl: $scope.requiredBadge, verifInfos: $scope.badgeInfos, verifType: $scope.requiredBadgeType},
            success: function(data) {
               if (!data.success) {
                  $scope.badgeLoading = false;
                  $scope.badgeError = translateError(data);
                  $scope.$applyAsync();
                  return;
               }
               $scope.badgeInfos = {};
               if ($scope.session && $scope.session.idUser) {
                  loadSession($scope, $http).then(function() {
                     $scope.badgeLoading = false;
                     $scope.step = "connected";
                     postLoginMessage('login', {
                        login: session.sLogin,
                        token: session.sToken
                     }, function() {
                        if ($scope.popupMode) {
                           closeAfterMessage();
                        }
                     });
                     $scope.$applyAsync();
                  });
               } else {
                  $scope.badgeLoading = false;
                  $scope.step = "badgeCreationConfirm";
                  $scope.infos = data.userInfos;
                  $scope.infos.sPassword = $scope.badgeInfos.code;
                  $scope.infos.sPasswordConfirm = $scope.badgeInfos.code;
               }
               $scope.$applyAsync();
            }
         });
      }

      $scope.confirmCreationFromBadge = function() {
         $scope.badgeError = null;
         if (!$scope.requiredBadge || ($scope.session && $scope.session.idUser)) {
            //?
            return;
         }
         if ($scope.requiredBadgeType == 'code') {
            if (!$scope.badgeInfos.code) {
               $scope.badgeError = 'missing_code';
            }         
         } else {
            console.error('unknown badge verification type!');
            return;
         }
         $scope.badgeLoading = true;
         $.ajax({
            url: config.selfBaseUrl + "badgeApi.php",
            context: document.body,
            dataType: 'json',
            method: 'POST',
            data: {action: 'confirmAccountCreation', badgeUrl: $scope.requiredBadge, verifInfos: $scope.badgeInfos, verifType: $scope.requiredBadgeType, userInfos: $scope.infos},
            success: function(data) {
               if (!data.success) {
                  $scope.badgeLoading = false;
                  $scope.badgeError = translateError(data);
                  $scope.$applyAsync();
                  return;
               }
               loadSession($scope, $http).then(function() {
                  $scope.badgeLoading = false;
                  $scope.badgeInfos = {};
                  $scope.step = "connected";
                  postLoginMessage('login', {
                     login: session.sLogin,
                     token: session.sToken
                  }, function() {
                     if ($scope.popupMode) {
                        closeAfterMessage();
                     }
                  });
                  $scope.$applyAsync();
               });
               $scope.$applyAsync();
            }
         });
      };

      $scope.forceLogin = function() {
         $scope.badgeLoading = false;
         $scope.badgeInfos = {};
         $scope.step = "connected";
         $scope.$applyAsync();
         postLoginMessage('login', {
            login: session.sLogin,
            token: session.sToken
         }, function() {
            if ($scope.popupMode) {
               closeAfterMessage();
            }
         });
      };

      $scope.changeInfos = function() {
         if ($scope.requiredFields) {
            var missingFields = [];
            for (var i = 0; i < $scope.requiredFields.length; i++) {
               var requiredField = $scope.requiredFields[i];
               if (!$scope.infos[requiredField]) {
                  missingFields.push(requiredField);
               }
            }
            if (missingFields.length) {
               $scope.infosError = $i18next.t('missing_fields')+missingFields.join(', ');
               return;
            }
         }
         $.ajax({
            url: config.selfBaseUrl + "updateInfos.php",
            context: document.body,
            dataType: 'json',
            method: 'POST',
            data: {infos: $scope.infos},
            success: function(data) {
               session = data;
               postLoginMessage('login', {
                  login: session.sLogin,
                  token: session.sToken
               }, function() {
                  if ($scope.popupMode) {
                     closeAfterMessage();
                  }
               });
            }
         });
      }

      $scope.refreshSession = function() {
         var infosTimeZone = checkTimeZone();
         var url = config.selfBaseUrl + "session.php?sTimeZoneOffset=" + infosTimeZone.offset + "&bDLS=" + infosTimeZone.bDLS;
         $http.get(url).then(function(data) {
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
      $scope.openPasswordChange = function() {
         $scope.step = 'changePass';
      },
      $scope.changePassDone = function() {
         if ($scope.popupMode) {
            if (!debugMode) window.close();
         } else {
            $scope.step = 'connected';
         }
      },
      $scope.properties = function() {
         loginManager.properties();
      };
      $scope.apply = function(f) {
         $timeout(function(){$scope.$apply(f);});
      };
      $scope.sendNewPassword = function(from) {
         var suffix = '';
         if (from == 'recovered') {
            suffix = 'Recovered';
         } else if (from == 'changePass') {
            suffix = 'ChangePass';
         }
         var newPassword = $('#newPassword'+suffix).val();
         var newPasswordConf = $('#newPasswordConf'+suffix).val();
         var oldPassword;
         if (from == 'changePass') {
            oldPassword = $('#oldPassword'+suffix).val();
         }
         if (!newPassword || newPassword != newPasswordConf) {
            alert($i18next.t('passwords_different'));
         } else if (newPassword.length < 6) {
            alert($i18next.t('error_password_length', {passwordLength: 6}));
         } else {
            loginManager.updatePassword(newPassword, from, oldPassword);
         }
      };
   });

var loginManager = {
   accessToken: "",
   accessProvider: "",
   loggedOnFacebook: false,

   logged: function(login, token, provider, loginData) {
      session = loginData;
      loginManager.scope.session = loginData;
      loginManager.scope.infos = {
         sFirstName: loginData.sFirstName,
         sLastName: loginData.sLastName,
         sStudentId: loginData.sStudentId
      };
      if (typeof selfTarget !== "undefined") { // If used on france-ioi's website TODO: find a better way to check that
         window.location.href = config.selfBaseUrl + selfTarget;
      } else {
         this.accessToken = token;
         this.accessProvider = provider;
         var scope = angular.element("#LoginCtrl").scope();
         scope.session = loginData;
         scope.infos = {
            sFirstName: loginData.sFirstName,
            sLastName: loginData.sLastName,
            sStudentId: loginData.sStudentId
         };
         if (scope.requiredFields && !checkAgainstRequired(scope.infos, scope.requiredFields)) {
            scope.$apply(function() {
               scope.infosError = null;
               scope.step = 'infos';
            });
         } else if (scope.requiredBadge && !checkAgainstRequiredBadge(scope.session, scope.requiredBadge)) {
            scope.$apply(function() {
               scope.badgeError = null;
               scope.step = 'badgeInfos';
               scope.autoVerifyBadgeFromUrl();
            });
         } else {
            scope.$apply(function() {
               scope.step = "connected";
            });
            postLoginMessage('login', {
               login: login,
               token: token
            }, function() {
               if (loginManager.scope.popupMode) {
                  closeAfterMessage();
               } else {
                  scope_setInterval();
               }
            });
         }
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
         loginManager.setErrorMessage(i18next.t("login_failed"));
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
            refWindow.postMessage(JSON.stringify({action: "logged", login: login, token: token, provider: data.provider, loginData: data.loginData}), '*');
            if (window.opener) {
               var params = getUrlVars();
               if (params.properties !== '1') {
                  if (!debugMode) window.close();
               }
            }
         }
      } else {
         if (data.error) {
            console.error(translateError(data));
         }
         if (closeIfFailed) {
            refWindow.postMessage(JSON.stringify({action: "loginFailed"}), '*');
            if (!debugMode) window.close();
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
                  window.opener.postMessage(JSON.stringify({action: "logged", login: data.login, token: data.token, provider: 'password', loginData: data.loginData}), '*');
                  //window.opener.loginManager.logged(data.login, data.token, 'password', data.loginData);
                  if (!debugMode) window.close();
               } else {
                  loginManager.logged(data.login, data.token, 'password', data.loginData);
               }
            } else {
               alert(translateError(data));
            }
         }
      }).done(function() {});
   },

   createUser: function(login, email, password1, password2) {
      if (password1 !== password2) {
         alert(i18next.t("passwords_different"));
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

   updatePassword: function(newPassword, from, oldPassword) {
      var scope = angular.element("#LoginCtrl").scope();
      if (from == 'recovered') {
         scope.step = 'recoveredPasswordChanged';
         scope.recoverPasswordChangeLoading = true;
         scope.recoverPasswordChangeError = false;
      } else if (from == 'changePass') {
         scope.changePassLoading = true;
      }
      $.get(config.selfBaseUrl + 'validateUser.php', {action: 'updatePassword', newPassword: newPassword, oldPassword: oldPassword}, function(res) {
         if (!res.success) {
            scope.$apply(function() {
               scope.recoverPasswordChangeLoading = false;
               scope.changePassLoading = false;
            });
            alert(translateError(res));
            return;
         }
         if (from == "recovered") {
            scope.$apply(function() {
               scope.recoverPasswordChangeLoading = false;
            });
            return;
         } else if (from == 'changePass') {
            scope.$apply(function() {
               scope.changePassLoading = false;
               scope.step = 'changePassDone';
            });
         }
         session.hasPassword = false;
         scope.apply(function() {
            scope.session.hasPassword = true;
         });
      }, 'json');
   },

   removeGoogle: function() {
      if (!session.hasGoogle){
         alert(i18next.t('no_entitity_to_remove', {identityName: 'Google'}));
         return;
      }
      if (!session.hasPassword && !session.hasFacebook){
         alert(i18next.t('cannot_remove_identity', {identityName: 'Google'}));
         return;
      }
      $.get(config.selfBaseUrl + 'validateUser.php', {action: 'removeGoogle'}, function(res) {
         if (!res.success) {
            alert(translateError(res));
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
         alert(i18next.t('no_entitity_to_remove', {identityName: 'Facebook'}));
         return;
      }
      if (!session.hasPassword && !session.hasGoogle){
         alert(i18next.t('cannot_remove_identity', {identityName: 'Facebook'}));
         return false;
      }
      $.get(config.selfBaseUrl + 'validateUser.php', {action: 'removeFacebook'}, function(res) {
         if (!res.success) {
            alert(translateError(res));
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
         alert(error);
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
         var loggingoutfromprovider = false;
         if (provider && (provider == 'facebook' || provider == 'google')) {
            if (confirm(i18next.t("logout_from_provider", {identityName: provider}))) {
               loginManager.logoutFromProvider(provider);
               loggingoutfromprovider = true;
               this.accessProvider = '';
               scope.session.sProvider = '';
            }
         }
         // for simplicity's sake (the code has grown in complexity quite a lot with the IE bug),
         // we don't send messages when the user logs out, we suppose the platform already knows
         // (and asked it)
         postLoginMessage('logout', null, function() {});
         if (loginManager.scope.popupMode && !loggingoutfromprovider) {
            closeAfterMessage();
         }
      });
   },

   openLoginPopup: function(url, provider) {
      if(typeof(window.loginPopup) != 'undefined'){
         window.loginPopup.close();
      }
      var width=510;
      if (provider == 'pms') {
         width=768;
      }
      this.popup = window.open(url, "loginPopup", "height=555, width="+width+", toolbar=yes, menubar=yes, scrollbars=no, resizable=no, location=no, directories=no, status=no");
      this.popup.focus();
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
      var that = this;
      if (provider === "facebook") {
         if (!this.loggedOnFacebook) {
            FB.login(function(response) {
               if (response.authResponse) {
                  that.connectLoggedFacebookUser();
               }
            });
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
      } else if (provider === "pms") {
         url = config.selfBaseUrl+'oauth2-client.php?authId=5';
      } else {
         this.setErrorMessage("Invalid login provider : " + provider);
         return;
      }
      this.openLoginPopup(url, provider);
   },

   properties: function() {
      this.openLoginPopup(config.selfBaseUrl+'login.html?properties=1');
   },

   openPasswordRecovery: function() {
      this.openLoginPopup(config.selfBaseUrl+'login.html?recover=1');
   },

   checkRecoverCode: function(recoverCode, recoverLogin) {
      $.get(config.selfBaseUrl + 'validateUser.php', {action: 'checkRecoverCode', recoverLogin: recoverLogin, recoverCode: recoverCode, language: window.language, customStringsName: window.customStringsName}, function(res) {
         var scope = angular.element("#LoginCtrl").scope();
         if (!res.success) {
            alert(translateError(res));
            scope.apply(function() {
               scope.recoverLoading = false;
               scope.recoverError = !res.success;
            });
            return;
         }
         scope.apply(function() {
            scope.recoverLoading = false;
            scope.recoverError = !res.success;
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
            alert(translateError(res));
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
                     alert(translateError(dataObject));
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

function i18ninit() {
   var urlVars = getUrlVars();
   var lang = config.defaultLanguage;
   if (urlVars.lang) {
      lang = urlVars.lang;
   }
   window.language = lang;
   var customStrings = config.customStringsName;
   if (urlVars.customStrings) {
      customStrings = urlVars.customStrings;
   }
   window.customStringsName = customStrings;
   window.i18next.use(window.i18nextXHRBackend);
   window.i18next.init({
    'lng': lang,
    'fallbackLng': ['fr'],
    'debug': true,
    'ns': customStrings ? [customStrings, 'login'] : ['login'],
    'fallbackNS':'login',
    'backend' : {
      'allowMultiLoading' : false,
      'loadPath' : '/i18n/{{lng}}/{{ns}}.json'
    }
   });
   window.i18next.on('initialized', function (options) {
    window.i18nextOptions = options;
    angular.bootstrap(document, ['login']);
   });
}

i18ninit();