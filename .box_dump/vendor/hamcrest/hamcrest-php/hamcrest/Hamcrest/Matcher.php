<?php
namespace Hamcrest;




















interface Matcher extends SelfDescribing
{











public function matches($item);

/**
@@return 








*/
public function describeMismatch($item, Description $description);
}
