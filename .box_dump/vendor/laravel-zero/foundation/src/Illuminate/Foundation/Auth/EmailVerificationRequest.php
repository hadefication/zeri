<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class EmailVerificationRequest extends FormRequest
{





public function authorize()
{
if (! hash_equals((string) $this->user()->getKey(), (string) $this->route('id'))) {
return false;
}

if (! hash_equals(sha1($this->user()->getEmailForVerification()), (string) $this->route('hash'))) {
return false;
}

return true;
}






public function rules()
{
return [

];
}






public function fulfill()
{
if (! $this->user()->hasVerifiedEmail()) {
$this->user()->markEmailAsVerified();

event(new Verified($this->user()));
}
}







public function withValidator(Validator $validator)
{
return $validator;
}
}
