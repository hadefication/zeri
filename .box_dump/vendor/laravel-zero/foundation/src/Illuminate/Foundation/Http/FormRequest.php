<?php

namespace Illuminate\Foundation\Http;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidatesWhenResolvedTrait;

class FormRequest extends Request implements ValidatesWhenResolved
{
use ValidatesWhenResolvedTrait;






protected $container;






protected $redirector;






protected $redirect;






protected $redirectRoute;






protected $redirectAction;






protected $errorBag = 'default';






protected $stopOnFirstFailure = false;






protected $validator;






protected function getValidatorInstance()
{
if ($this->validator) {
return $this->validator;
}

$factory = $this->container->make(ValidationFactory::class);

if (method_exists($this, 'validator')) {
$validator = $this->container->call($this->validator(...), compact('factory'));
} else {
$validator = $this->createDefaultValidator($factory);
}

if (method_exists($this, 'withValidator')) {
$this->withValidator($validator);
}

if (method_exists($this, 'after')) {
$validator->after($this->container->call(
$this->after(...),
['validator' => $validator]
));
}

$this->setValidator($validator);

return $this->validator;
}







protected function createDefaultValidator(ValidationFactory $factory)
{
$rules = $this->validationRules();

$validator = $factory->make(
$this->validationData(),
$rules,
$this->messages(),
$this->attributes(),
)->stopOnFirstFailure($this->stopOnFirstFailure);

if ($this->isPrecognitive()) {
$validator->setRules(
$this->filterPrecognitiveRules($validator->getRulesWithoutPlaceholders())
);
}

return $validator;
}






public function validationData()
{
return $this->all();
}






protected function validationRules()
{
return method_exists($this, 'rules') ? $this->container->call([$this, 'rules']) : [];
}









protected function failedValidation(Validator $validator)
{
$exception = $validator->getException();

throw (new $exception($validator))
->errorBag($this->errorBag)
->redirectTo($this->getRedirectUrl());
}






protected function getRedirectUrl()
{
$url = $this->redirector->getUrlGenerator();

if ($this->redirect) {
return $url->to($this->redirect);
} elseif ($this->redirectRoute) {
return $url->route($this->redirectRoute);
} elseif ($this->redirectAction) {
return $url->action($this->redirectAction);
}

return $url->previous();
}








protected function passesAuthorization()
{
if (method_exists($this, 'authorize')) {
$result = $this->container->call([$this, 'authorize']);

return $result instanceof Response ? $result->authorize() : $result;
}

return true;
}








protected function failedAuthorization()
{
throw new AuthorizationException;
}







public function safe(?array $keys = null)
{
return is_array($keys)
? $this->validator->safe()->only($keys)
: $this->validator->safe();
}








public function validated($key = null, $default = null)
{
return data_get($this->validator->validated(), $key, $default);
}






public function messages()
{
return [];
}






public function attributes()
{
return [];
}







public function setValidator(Validator $validator)
{
$this->validator = $validator;

return $this;
}







public function setRedirector(Redirector $redirector)
{
$this->redirector = $redirector;

return $this;
}







public function setContainer(Container $container)
{
$this->container = $container;

return $this;
}
}
