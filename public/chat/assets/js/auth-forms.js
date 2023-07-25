(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else {
		var a = factory();
		for(var i in a) (typeof exports === 'object' ? exports : root)[i] = a[i];
	}
})(self, function() {
return /******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************************************!*\
  !*** ./Modules/Chat/Resources/assets/js/auth-forms.js ***!
  \********************************************************/
$(document).on('click', '#loginBtn', function (event) {
  event.preventDefault();
  if (!validateEmail() || !validatePassword()) {
    return false;
  }
  $('#loginForm').submit();
});
$(document).on('click', '#registerBtn', function (event) {
  event.preventDefault();
  if (!validateName() || !validateEmail() || !validatePassword() || !validatePasswordConfirmation() || !validateMatchPasswords()) {
    return false;
  }
  $('#registerForm').submit();
});
$(document).on('click', '#forgetPasswordBtn', function (event) {
  event.preventDefault();
  if (!validateEmail()) {
    return false;
  }
  $('#forgetPasswordForm').submit();
});
$(document).on('click', '#resetPasswordBtn', function (event) {
  event.preventDefault();
  if (!validateEmail() || !validatePassword() || !validatePasswordConfirmation() || !validateMatchPasswords()) {
    return false;
  }
  $('#resetPasswordForm').submit();
});
var form = $('form');
form.on('keypress', function (e) {
  if (e.keyCode === 13) {
    var loginBtn = form.find('#loginBtn');
    var registerBtn = form.find('#registerBtn');
    var forgetPasswordBtn = form.find('#forgetPasswordBtn');
    var resetPasswordBtn = form.find('#resetPasswordBtn');
    if (loginBtn.length > 0) {
      loginBtn.trigger('click');
    } else if (registerBtn.length > 0) {
      registerBtn.trigger('click');
    } else if (forgetPasswordBtn.length > 0) {
      forgetPasswordBtn.trigger('click');
    } else if (resetPasswordBtn.length > 0) {
      resetPasswordBtn.trigger('click');
    }
  }
});

//validations
var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
window.validateName = function () {
  var name = $('#name').val();
  if (name === '') {
    displayToastr('Error', 'error', 'The name field is required.');
    return false;
  }
  return true;
};
window.validatePasswordConfirmation = function () {
  var passwordConfirmation = $('#password_confirmation').val();
  if (passwordConfirmation === '') {
    displayToastr('Error', 'error', 'The password confirmation field is required.');
    return false;
  }
  return true;
};
window.validateMatchPasswords = function () {
  var passwordConfirmation = $('#password_confirmation').val();
  var password = $('#password').val();
  if (passwordConfirmation !== password) {
    displayToastr('Error', 'error', 'The password and password confirmation did not match.');
    return false;
  }
  return true;
};
window.validatePassword = function () {
  var password = $('#password').val();
  if (password === '') {
    displayToastr('Error', 'error', 'The password field is required.');
    return false;
  }
  return true;
};
window.validateEmail = function () {
  var email = $('#email').val();
  if (email === '') {
    displayToastr('Error', 'error', 'The email field is required.');
    return false;
  } else if (!validateEmailFormat(email)) {
    displayToastr('Error', 'error', 'Please enter valid email.');
    return false;
  }
  return true;
};
window.validateEmailFormat = function (email) {
  return emailReg.test(email);
};
window.avoidSpace = function (event) {
  var k = event ? event.which : window.event.keyCode;
  if (k === 32) {
    event.stopPropagation();
    return false;
  }
};
/******/ 	return __webpack_exports__;
/******/ })()
;
});