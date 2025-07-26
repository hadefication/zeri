<?php





namespace Whoops\Inspector;

interface InspectorInterface
{



public function getException();




public function getExceptionName();




public function getExceptionMessage();




public function getPreviousExceptionMessages();




public function getPreviousExceptionCodes();






public function getExceptionDocrefUrl();





public function hasPreviousException();






public function getPreviousExceptionInspector();





public function getPreviousExceptions();









public function getFrames(array $frameFilters = []);
}
