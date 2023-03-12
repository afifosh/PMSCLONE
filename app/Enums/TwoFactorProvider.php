<?php

namespace App\Enums;

/**
 *  Two factor provider enum
 */
enum TwoFactorProviderEnum:string
{
    const SMS = 'sms';
    const MAIL = 'mail';
    const GOOGLE_AUTHENTICATOR = 'google_authenticator';
}