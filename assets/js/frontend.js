/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!***************************************!*\
  !*** ./resources/scripts/frontend.js ***!
  \***************************************/
var $ = jQuery;

//  // END AGE GATE FIX
var firstDomain = window.location.hostname;
var maskDomain = wccPrimaryDomainHostname; //without www
console.log(firstDomain);
console.log(maskDomain);
jQuery(function () {
  console.log($.inArray(firstDomain, [maskDomain, "www.".concat(maskDomain)]));
  if ($.inArray(firstDomain, [maskDomain, "www.".concat(maskDomain)]) == -1) {
    $(document).on('click', 'a[href*="/checkout"]', function (e) {
      e.preventDefault();
      $.ajax({
        url: '/wp-json/wc/store/v1/cart/items',
        method: 'get',
        async: false,
        success: function success(response, status, xhr) {
          var token = xhr.getResponseHeader("Cart-Token");
          console.log(token);
          console.log(token.lenght);
          if (token !== '') {
            var redirect_url = "https://".concat(maskDomain, "/?token=").concat(token);
            console.log(redirect_url);
            window.location.href = redirect_url;
          }
        }
      });
    });
  }
});
/******/ })()
;