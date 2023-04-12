@php
    $customizerHidden = 'customizer-hide';
    $pageConfigs = ['myLayout' => 'blank'];
@endphp

@extends('admin.layouts/layoutMaster' , ['body_class' => 'authentication'])

@section('title', 'Login')

@section('vendor-style')
    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
@endsection

@section('page-style')
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs2/2.1.0/fingerprint2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ua-parser-js@0.7.21/src/ua-parser.min.js"></script> 



    <script>
    function detectIncognito() {
        return new Promise(function (resolve, reject) {
            var browserName = "Unknown";
            function __callback(isPrivate) {
                resolve({
                    isPrivate: isPrivate,
                    browserName: browserName,
                });
            }
            function identifyChromium() {
                var ua = navigator.userAgent;
                if (ua.match(/Chrome/)) {
                    if (navigator.brave !== undefined) {
                        return "Brave";
                    }
                    else if (ua.match(/Edg/)) {
                        return "Edge";
                    }
                    else if (ua.match(/OPR/)) {
                        return "Opera";
                    }
                    return "Chrome";
                }
                else {
                    return "Chromium";
                }
            }
            function assertEvalToString(value) {
                return value === eval.toString().length;
            }
            function isSafari() {
                var v = navigator.vendor;
                return (v !== undefined && v.indexOf("Apple") === 0 && assertEvalToString(37));
            }
            function isChrome() {
                var v = navigator.vendor;
                return (v !== undefined && v.indexOf("Google") === 0 && assertEvalToString(33));
            }
            function isFirefox() {
                return (document.documentElement !== undefined &&
                    document.documentElement.style.MozAppearance !== undefined &&
                    assertEvalToString(37));
            }
            function isMSIE() {
                return (navigator.msSaveBlob !== undefined && assertEvalToString(39));
            }
            /**
             * Safari (Safari for iOS & macOS)
             **/
            function macOS_safari14() {
                try {
                    window.safari.pushNotification.requestPermission("https://example.com", "private", {}, function () { });
                }
                catch (e) {
                    return __callback(!new RegExp("gesture").test(e));
                }
                return __callback(false);
            }
            function iOS_safari14() {
                var tripped = false;
                var iframe = document.createElement("iframe");
                iframe.style.display = "none";
                document.body.appendChild(iframe);
                iframe.contentWindow.applicationCache.addEventListener("error", function () {
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
                }
                catch (e) {
                    return __callback(true);
                }
                try {
                    storage.setItem("test", "1");
                    storage.removeItem("test");
                }
                catch (e) {
                    return __callback(true);
                }
                return __callback(false);
            }
            function safariPrivateTest() {
                var w = window;
                if (navigator.maxTouchPoints !== undefined) {
                    if (w.safari !== undefined && w.DeviceMotionEvent === undefined) {
                        browserName = "Safari for macOS";
                        macOS_safari14();
                    }
                    else if (w.DeviceMotionEvent !== undefined) {
                        browserName = "Safari for iOS";
                        iOS_safari14();
                    }
                    else {
                        reject(new Error("detectIncognito Could not identify this version of Safari"));
                    }
                }
                else {
                    browserName = "Safari";
                    oldSafariTest();
                }
            }
            /**
             * Chrome
             **/
            function getQuotaLimit() {
                var w = window;
                if (w.performance !== undefined &&
                    w.performance.memory !== undefined &&
                    w.performance.memory.jsHeapSizeLimit !== undefined) {
                    return performance.memory.jsHeapSizeLimit;
                }
                return 1073741824;
            }
            // >= 76
            function storageQuotaChromePrivateTest() {
                navigator.webkitTemporaryStorage.queryUsageAndQuota(function (usage, quota) {
                    __callback(quota < getQuotaLimit());
                }, function (e) {
                    reject(new Error("detectIncognito somehow failed to query storage quota: " +
                        e.message));
                });
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
                }
                else {
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
                }
                else if (isChrome()) {
                    browserName = identifyChromium();
                    chromePrivateTest();
                }
                else if (isFirefox()) {
                    browserName = "Firefox";
                    firefoxPrivateTest();
                }
                else if (isMSIE()) {
                    browserName = "Internet Explorer";
                    msiePrivateTest();
                }
                else {
                    reject(new Error("detectIncognito cannot determine the browser"));
                }
            }
            main();
        });
    };
            var incognito =     detectIncognito().then(info => {
                if (info.isPrivate) {
                    return true;
                } else {
                    return false;
                }
            })
    </script>
<script>
    function getFooComponent() {
      return incognito;
    }
  
    function getBarComponent() {
        return window.navigator.userAgent;
    }

    async function getVisitorCountrycode() {
        const {data} = await axios.get("https://api.country.is")
      return data.country;
    }
    async function getVisitorIPaddress() {
        const {data} = await axios.get("https://api.country.is")
      return data.ip;
    }
    async function initFingerprintJS() {
      const fp = await FingerprintJS.load()
      const result = await fp.get()
    //   console.log("------------------result-------------------");
    //   console.dir(result);
    //   console.log("--------------result---------------------");

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
        enumerateDevices, ...components } = result.components

      // Add a few custom components
      const extendedComponents = {
        ...components,
        incognito: { value: await getFooComponent() },
        userAgent: { value: await getBarComponent() },
        ipAddress: { value: await getVisitorIPaddress() },
        countryCode: { value: await getVisitorCountrycode() },
  
      }
    //   console.log("------------------extendedComponents--------------------");
    //   console.dir(extendedComponents);
    //   console.log("---------------extendedComponents-----------------------");
      // Make a visitor identifier from your custom list of components
      const visitorId = FingerprintJS.hashComponents(extendedComponents)
      $('input#fingerprint').val(visitorId)
      console.log('visitorId:', visitorId)
    }
  </script>

          <script
          async
          src="//cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@3/dist/fp.min.js"
          onload="initFingerprintJS()"
        ></script>
    {{-- <script src="https://cdn.jsdelivr.net/gh/Joe12387/detectIncognito@main/dist/detectIncognito.min.js"></script> --}}
@endsection

@section('content')
@include('admin._partials.auth-section')
<div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">

            <div class="authentication-inner py-4">
                <!-- Login -->
                <div class="card">
                  <!-- Start Header -->
                  @include('admin._partials.auth-svg-top')
                 <!-- End Header -->                  
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4 mt-2">
                            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                <span class="app-brand-mainlogo demo">@include('admin._partials.mainlogo', ['height' => 150, 'withbg' => 'fill: #000;'])</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h6 class="mb-1 pt-2">Welcome to {{ config('variables.templateName') }}!</h6>
                        <p class="mb-4">Please sign-in to your account and start the adventure</p>
                        @if (session('status'))
                            <p class="text-success mb-3">{{ session('status') }}</p>
                        @endif
                        <form id="formAuthentication" class="mb-3" action="" method="POST">
                            <input type="hidden" class="form-control" id="fingerprint" name="fingerprint">
                            @csrf
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    {!! implode('<br/>', $errors->all('<span>:message</span>')) !!}
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="email" class="form-label">Email or Username</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter your email or username" autofocus>
                                    
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                    <a href="{{ route('admin.password.request') }}">
                                        <small>Forgot Password?</small>
                                    </a>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me">
                                    <label class="form-check-label" for="remember-me">
                                        Remember Me
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>
@endsection
