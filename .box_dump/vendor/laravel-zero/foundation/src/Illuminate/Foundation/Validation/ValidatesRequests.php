<?php

namespace Illuminate\Foundation\Validation;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Precognition;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait ValidatesRequests
{









public function validateWith($validator, ?Request $request = null)
{
$request = $request ?: request();

if (is_array($validator)) {
$validator = $this->getValidationFactory()->make($request->all(), $validator);
}

if ($request->isPrecognitive()) {
$validator->after(Precognition::afterValidationHook($request))
->setRules(
$request->filterPrecognitiveRules($validator->getRulesWithoutPlaceholders())
);
}

return $validator->validate();
}












public function validate(Request $request, array $rules,
array $messages = [], array $attributes = [])
{
$validator = $this->getValidationFactory()->make(
$request->all(), $rules, $messages, $attributes
);

if ($request->isPrecognitive()) {
$validator->after(Precognition::afterValidationHook($request))
->setRules(
$request->filterPrecognitiveRules($validator->getRulesWithoutPlaceholders())
);
}

return $validator->validate();
}













public function validateWithBag($errorBag, Request $request, array $rules,
array $messages = [], array $attributes = [])
{
try {
return $this->validate($request, $rules, $messages, $attributes);
} catch (ValidationException $e) {
$e->errorBag = $errorBag;

throw $e;
}
}






protected function getValidationFactory()
{
return app(Factory::class);
}
}
