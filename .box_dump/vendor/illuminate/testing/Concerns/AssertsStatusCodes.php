<?php

namespace Illuminate\Testing\Concerns;

use Illuminate\Testing\Assert as PHPUnit;

trait AssertsStatusCodes
{





public function assertOk()
{
return $this->assertStatus(200);
}






public function assertCreated()
{
return $this->assertStatus(201);
}






public function assertAccepted()
{
return $this->assertStatus(202);
}







public function assertNoContent($status = 204)
{
$this->assertStatus($status);

PHPUnit::assertEmpty($this->getContent(), 'Response content is not empty.');

return $this;
}






public function assertMovedPermanently()
{
return $this->assertStatus(301);
}






public function assertFound()
{
return $this->assertStatus(302);
}






public function assertNotModified()
{
return $this->assertStatus(304);
}






public function assertTemporaryRedirect()
{
return $this->assertStatus(307);
}






public function assertPermanentRedirect()
{
return $this->assertStatus(308);
}






public function assertBadRequest()
{
return $this->assertStatus(400);
}






public function assertUnauthorized()
{
return $this->assertStatus(401);
}






public function assertPaymentRequired()
{
return $this->assertStatus(402);
}






public function assertForbidden()
{
return $this->assertStatus(403);
}






public function assertNotFound()
{
return $this->assertStatus(404);
}






public function assertMethodNotAllowed()
{
return $this->assertStatus(405);
}






public function assertNotAcceptable()
{
return $this->assertStatus(406);
}






public function assertRequestTimeout()
{
return $this->assertStatus(408);
}






public function assertConflict()
{
return $this->assertStatus(409);
}






public function assertGone()
{
return $this->assertStatus(410);
}






public function assertUnsupportedMediaType()
{
return $this->assertStatus(415);
}






public function assertUnprocessable()
{
return $this->assertStatus(422);
}






public function assertTooManyRequests()
{
return $this->assertStatus(429);
}






public function assertInternalServerError()
{
return $this->assertStatus(500);
}






public function assertServiceUnavailable()
{
return $this->assertStatus(503);
}
}
