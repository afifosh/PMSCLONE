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
/*!*************************************************************!*\
  !*** ./Modules/Chat/Resources/assets/js/set-user-on-off.js ***!
  \*************************************************************/
window.setLastSeenOfUser = function (status) {
  $.ajax({
    type: 'post',
    url: route('update-last-seen'),
    data: {
      status: status
    },
    success: function success(data) {}
  });
};

//set user status online
setLastSeenOfUser(1);
window.onbeforeunload = function () {
  Echo.leave('user-status');
  setLastSeenOfUser(0);
  //return undefined; to prevent dialog while window.onbeforeunload
  return undefined;
};
Echo.join("user-status");
/******/ 	return __webpack_exports__;
/******/ })()
;
});