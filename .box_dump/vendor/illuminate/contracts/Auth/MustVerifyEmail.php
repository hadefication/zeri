<?php

namespace Illuminate\Contracts\Auth;

interface MustVerifyEmail
{





public function hasVerifiedEmail();






public function markEmailAsVerified();






public function sendEmailVerificationNotification();






public function getEmailForVerification();
}
