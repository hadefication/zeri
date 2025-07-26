<?php

namespace Illuminate\Foundation;

class Precognition
{






public static function afterValidationHook($request)
{
return function ($validator) use ($request) {
if ($validator->messages()->isEmpty() && $request->headers->has('Precognition-Validate-Only')) {
abort(204, headers: ['Precognition-Success' => 'true']);
}
};
}
}
