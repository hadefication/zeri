<?php

namespace staabm\SideEffectsDetector;




final class SideEffect {



const PROCESS_EXIT = 'process_exit';




const SCOPE_POLLUTION = 'scope_pollution';




const INPUT_OUTPUT = 'input_output';




const STANDARD_OUTPUT = 'standard_output';




const UNKNOWN_CLASS = 'unknown_class';




const MAYBE = 'maybe_has_side_effects';

private function __construct() {

}
}