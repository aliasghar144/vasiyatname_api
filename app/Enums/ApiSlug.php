<?php
namespace App\Enums;

enum ApiSlug: string
{
    // Login
    case LOGIN_SUCCESS = 'LOGIN_SUCCESS';
    case LOGIN_FAILED = 'LOGIN_FAILED';
    case NEED_SIGN_IN = 'NEED_SIGN_IN';
    case OTP_SENT = 'OTP_SENT';
    case OTP_INVALID = 'OTP_INVALID';
    case OTP_SEND = 'OTP_SEND';
    case OTP_EXPIRED = 'OTP_EXPIRED';
    case VERIFIED_SUCCESSFULLY = 'VERIFIED_SUCCESSFULLY';
    case MOBILE_REQUIRED = 'MOBILE_REQUIRED';

    // Profile
    case PROFILE_UPDATED = 'PROFILE_UPDATED';
    case PROFILE_NOT_FOUND = 'PROFILE_NOT_FOUND';

    // Financial
    case DEBT_ADDED = 'DEBT_ADDED';
    case DEBT_REMOVED = 'DEBT_REMOVED';

    // General
    case UNAUTHORIZED = 'UNAUTHORIZED';
    case SERVER_ERROR = 'SERVER_ERROR';
}
