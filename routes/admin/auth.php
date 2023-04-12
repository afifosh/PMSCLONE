<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController;
use Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\EmailVerificationPromptController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\ProfileInformationController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController;
use Laravel\Fortify\Http\Controllers\VerifyEmailController;
use Laravel\Fortify\Features;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Auth\RedirectToMailOTP as RedirectToTwoFactorMailOTPAuthentication;

//'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class

Route::post('check-code', [RedirectToTwoFactorMailOTPAuthentication::class, 'verify'])
->middleware(array_filter([
    'guest:web',
]))->name('check_code');





Route::prefix('admin')->middleware('guest:web')->group(function () {
    Route::name('admin.')->group(function () {     
    $enableViews = config('fortify.views', true);

    // Authentication...
    if ($enableViews) {
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])
            ->middleware(['guest:admin'])
            ->name('login');
    }

    $limiter = config('fortify.limiters.login');
    $twoFactorLimiter = config('fortify.limiters.two-factor');
    $verificationLimiter = config('fortify.limiters.verification', '6,1');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            'guest:admin',
            $limiter ? 'throttle:'.$limiter : null,
        ]));

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Password Reset...
    if (Features::enabled(Features::resetPasswords())) {
        if ($enableViews) {
            Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
                ->middleware(['guest:admin'])
                ->name('password.request');

            Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
                ->middleware(['guest:admin'])
                ->name('password.reset');
        }

        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
            ->middleware(['guest:admin'])
            ->name('password.email');

        Route::post('/reset-password', [NewPasswordController::class, 'store'])
            ->middleware(['guest:admin'])
            ->name('password.update');
    }

    // Registration...
    if (Features::enabled(Features::registration())) {
        if ($enableViews) {
            Route::get('/sign-up', [RegisteredUserController::class, 'create'])
                ->middleware(['guest:admin'])
                ->name('register');
        }

        Route::post('/sign-up', [RegisteredUserController::class, 'store'])
            ->middleware(['guest:admin']);
    }

    // Email Verification...
    if (Features::enabled(Features::emailVerification())) {
        if ($enableViews) {
            Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])
                ->middleware([config('fortify.auth_middleware', 'auth').':admin'])
                ->name('verification.notice');
        }

        Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
            ->middleware([config('fortify.auth_middleware', 'auth').':admin', 'signed', 'throttle:'.$verificationLimiter])
            ->name('verification.verify');

        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware([config('fortify.auth_middleware', 'auth').':admin', 'throttle:'.$verificationLimiter])
            ->name('verification.send');
    }

    // Profile Information...
    if (Features::enabled(Features::updateProfileInformation())) {
        Route::put('/user/profile-information', [ProfileInformationController::class, 'update'])
            ->middleware([config('fortify.auth_middleware', 'auth').':admin'])
            ->name('user-profile-information.update');
    }

    // Passwords...
    if (Features::enabled(Features::updatePasswords())) {
        Route::put('/user/password', [PasswordController::class, 'update'])
            ->middleware([config('fortify.auth_middleware', 'auth').':admin'])
            ->name('user-password.update');
    }


    // Password Confirmation...
    if ($enableViews) {
        Route::get('/user/confirm-password', [ConfirmablePasswordController::class, 'show'])
            ->middleware('auth:admin');
    }


    Route::get('/user/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show'])
        ->middleware('auth:admin')
        ->name('password.confirmation');

    Route::post('/user/confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware('auth:admin')
        ->name('password.confirm');


    // Two Factor Authentication...
    if (Features::enabled(Features::twoFactorAuthentication())) {
        if ($enableViews) {
            Route::get('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'create'])
                ->middleware(['guest:admin'])
                ->name('two-factor.login');
        }
       
       // Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
        Route::post('/two-factor-challenge', [RedirectToTwoFactorMailOTPAuthentication::class, 'store'])
            ->middleware(array_filter([
                'guest:admin',
                $twoFactorLimiter ? 'throttle:'.$twoFactorLimiter : null,
            ]));

            Route::get('/user/send-email-otp', [AdminUsersController::class, 'resendCode'])
            ->name('send.email.otp');

            Route::post('/user/confirmed-two-factor-email-authentication', [AdminUsersController::class, 'verifyCode'])
            ->middleware('auth:admin')
            ->name('two-factor-email.confirm');
            
            // Route::delete('/user/two-factor-email-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
            // ->middleware('auth:admin')
            // ->name('two-factor-email.disable');     
            
            Route::post('/user/two-factor-email-authentication', [AdminUsersController::class, 'confirmPassword'])
            ->middleware('auth:admin')
            ->name('two-factor-email.disable');   

            Route::post('/user/security/setting', [AdminUsersController::class, 'securitySetting'])
            ->middleware('auth:admin')
            ->name('security.setting');   

            // Route::post('/user/two-factor-email-authentication', [AdminUsersController::class, 'twoFactorGoogle'])
            // ->middleware('auth:admin')
            // ->name('two-factor-email.disable');  
            //->name('two-factor-google.enable');               

        $twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
            ? [config('fortify.auth_middleware', 'auth').':'.config('fortify.guard'), 'password.confirm:admin.password.confirm']
            : [config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')];


        Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.enable');

        Route::post('/user/confirmed-two-factor-authentication', [ConfirmedTwoFactorAuthenticationController::class, 'store'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.confirm');
            
        Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.disable');

        Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.qr-code');

        Route::get('/user/two-factor-secret-key', [TwoFactorSecretKeyController::class, 'show'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.secret-key');

        Route::get('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.recovery-codes');

        Route::post('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store'])
            ->middleware($twoFactorMiddleware);
    }

    Route::post('check-code', [RedirectToTwoFactorMailOTPAuthentication::class, 'verify'])
->middleware(array_filter([
    'guest:admin',
]))->name('check_code');


    
});

});