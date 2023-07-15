function getFooComponent() {
  return incognito;
}

function getBarComponent() {
  return window.navigator.userAgent;
}

function blockForm(){
  $('#login-form').block({
    message:
      '<div class="ms-5 mt-2 sk-fold sk-primary"><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div><div class="sk-fold-cube"></div></div><h5>LOADING...</h5>',

    css: {
      backgroundColor: 'transparent',
      border: '0'
    },
    overlayCSS: {
      backgroundColor: $('html').hasClass('dark-style') ? '#000' : '#fff',
      opacity: 0.55
    }
  });
}

function unblockForm(){
  $('#login-form').unblock();
}

async function getVisitorCountrycode() {
  const { data } = await axios.get('https://api.country.is');
  return data.country;
}
async function getVisitorIPaddress() {
  const { data } = await axios.get('https://api.country.is');
  return data.ip;
}
window.initFingerprintJS = async function () {
  blockForm();
  const fp = await FingerprintJS.load();
  const result = await fp.get();
  const {
    webdriver,
    colorDepth,
    deviceMemory,
    pixelRatio,
    hardwareConcurrency,
    indexedDb,
    addBehavior,
    openDatabase,
    cpuClass,
    plugins,
    canvas,
    webgl,
    webglVendorAndRenderer,
    adBlock,
    hasLiedLanguages,
    hasLiedResolution,
    hasLiedOs,
    hasLiedBrowser,
    touchSupport,
    fonts,
    fontsFlash,
    audio,
    colorGamut,
    architecture,
    pdfViewerEnabled,
    reducedMotion,
    vendor,
    hdr,
    forcedColors,
    fontPreferences,
    monochrome,
    vendorFlavors,
    videoCard,
    math,
    invertedColors,
    indexedDB,
    contrast,
    domBlockers,
    enumerateDevices,
    ...components
  } = result.components;

  // Add a few custom components
  const extendedComponents = {
    ...components,
    incognito: { value: await getFooComponent() },
    userAgent: { value: await getBarComponent() },
    ipAddress: { value: await getVisitorIPaddress() },
    countryCode: { value: await getVisitorCountrycode() }
  };
  //   console.log("------------------extendedComponents--------------------");
  //   console.dir(extendedComponents);
  //   console.log("---------------extendedComponents-----------------------");
  // Make a visitor identifier from your custom list of components
  const visitorId = FingerprintJS.hashComponents(extendedComponents);
  $('input#fingerprint').val(visitorId);
  unblockForm();
  if (!visitorId) {
    alert('Please disable your adblocker and refresh the page');
  }
  console.log('visitorId:', visitorId);
};

function detectIncognito() {
  return new Promise(function (resolve, reject) {
    var browserName = 'Unknown';
    function __callback(isPrivate) {
      resolve({
        isPrivate: isPrivate,
        browserName: browserName
      });
    }
    function identifyChromium() {
      var ua = navigator.userAgent;
      if (ua.match(/Chrome/)) {
        if (navigator.brave !== undefined) {
          return 'Brave';
        } else if (ua.match(/Edg/)) {
          return 'Edge';
        } else if (ua.match(/OPR/)) {
          return 'Opera';
        }
        return 'Chrome';
      } else {
        return 'Chromium';
      }
    }
    function assertEvalToString(value) {
      return value === eval.toString().length;
    }
    function isSafari() {
      var v = navigator.vendor;
      return v !== undefined && v.indexOf('Apple') === 0 && assertEvalToString(37);
    }
    function isChrome() {
      var v = navigator.vendor;
      return v !== undefined && v.indexOf('Google') === 0 && assertEvalToString(33);
    }
    function isFirefox() {
      return (
        document.documentElement !== undefined &&
        document.documentElement.style.MozAppearance !== undefined &&
        assertEvalToString(37)
      );
    }
    function isMSIE() {
      return navigator.msSaveBlob !== undefined && assertEvalToString(39);
    }
    /**
     * Safari (Safari for iOS & macOS)
     **/
    function macOS_safari14() {
      try {
        window.safari.pushNotification.requestPermission('https://example.com', 'private', {}, function () {});
      } catch (e) {
        return __callback(!new RegExp('gesture').test(e));
      }
      return __callback(false);
    }
    function iOS_safari14() {
      var tripped = false;
      var iframe = document.createElement('iframe');
      iframe.style.display = 'none';
      document.body.appendChild(iframe);
      iframe.contentWindow.applicationCache.addEventListener('error', function () {
        tripped = true;
        return __callback(true);
      });
      setTimeout(function () {
        if (!tripped) {
          __callback(false);
        }
      }, 100);
    }
    function oldSafariTest() {
      var openDB = window.openDatabase;
      var storage = window.localStorage;
      try {
        openDB(null, null, null, null);
      } catch (e) {
        return __callback(true);
      }
      try {
        storage.setItem('test', '1');
        storage.removeItem('test');
      } catch (e) {
        return __callback(true);
      }
      return __callback(false);
    }
    function safariPrivateTest() {
      var w = window;
      if (navigator.maxTouchPoints !== undefined) {
        if (w.safari !== undefined && w.DeviceMotionEvent === undefined) {
          browserName = 'Safari for macOS';
          macOS_safari14();
        } else if (w.DeviceMotionEvent !== undefined) {
          browserName = 'Safari for iOS';
          iOS_safari14();
        } else {
          reject(new Error('detectIncognito Could not identify this version of Safari'));
        }
      } else {
        browserName = 'Safari';
        oldSafariTest();
      }
    }
    /**
     * Chrome
     **/
    function getQuotaLimit() {
      var w = window;
      if (
        w.performance !== undefined &&
        w.performance.memory !== undefined &&
        w.performance.memory.jsHeapSizeLimit !== undefined
      ) {
        return performance.memory.jsHeapSizeLimit;
      }
      return 1073741824;
    }
    // >= 76
    function storageQuotaChromePrivateTest() {
      navigator.webkitTemporaryStorage.queryUsageAndQuota(
        function (usage, quota) {
          __callback(quota < getQuotaLimit());
        },
        function (e) {
          reject(new Error('detectIncognito somehow failed to query storage quota: ' + e.message));
        }
      );
    }
    // 50 to 75
    function oldChromePrivateTest() {
      var fs = window.webkitRequestFileSystem;
      var success = function () {
        __callback(false);
      };
      var error = function () {
        __callback(true);
      };
      fs(0, 1, success, error);
    }
    function chromePrivateTest() {
      if (Promise !== undefined && Promise.allSettled !== undefined) {
        storageQuotaChromePrivateTest();
      } else {
        oldChromePrivateTest();
      }
    }
    /**
     * Firefox
     **/
    function firefoxPrivateTest() {
      __callback(navigator.serviceWorker === undefined);
    }
    /**
     * MSIE
     **/
    function msiePrivateTest() {
      __callback(window.indexedDB === undefined);
    }
    function main() {
      if (isSafari()) {
        safariPrivateTest();
      } else if (isChrome()) {
        browserName = identifyChromium();
        chromePrivateTest();
      } else if (isFirefox()) {
        browserName = 'Firefox';
        firefoxPrivateTest();
      } else if (isMSIE()) {
        browserName = 'Internet Explorer';
        msiePrivateTest();
      } else {
        reject(new Error('detectIncognito cannot determine the browser'));
      }
    }
    main();
  });
}
var incognito = detectIncognito().then(info => {
  if (info.isPrivate) {
    return true;
  } else {
    return false;
  }
});
