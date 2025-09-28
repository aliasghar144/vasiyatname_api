<?php
namespace App\Helpers;

use App\Enums\ApiSlug;

class Slug
{
    public static function login(ApiSlug $slug): string
    {
        return match($slug) {
            ApiSlug::LOGIN_SUCCESS => 'login.success',
            ApiSlug::LOGIN_FAILED => 'login.failed',
            ApiSlug::OTP_SENT => 'login.otp_sent',
            ApiSlug::OTP_INVALID => 'login.otp_invalid',
            default => throw new \InvalidArgumentException("Invalid login slug")
        };
    }

    public static function profile(ApiSlug $slug): string
    {
        return match($slug) {
            ApiSlug::PROFILE_UPDATED => 'profile.updated',
            ApiSlug::PROFILE_NOT_FOUND => 'profile.not_found',
            default => throw new \InvalidArgumentException("Invalid profile slug")
        };
    }

    public static function financial(ApiSlug $slug): string
    {
        return match($slug) {
            ApiSlug::DEBT_ADDED => 'financial.debt_added',
            ApiSlug::DEBT_REMOVED => 'financial.debt_removed',
            default => throw new \InvalidArgumentException("Invalid financial slug")
        };
    }

    public static function general(ApiSlug $slug): string
    {
        return match($slug) {
            ApiSlug::UNAUTHORIZED => 'general.unauthorized',
            ApiSlug::SERVER_ERROR => 'general.server_error',
            default => throw new \InvalidArgumentException("Invalid general slug")
        };
    }
}
