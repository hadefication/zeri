<?php

namespace Illuminate\Contracts\Pagination;

/**
@template
@template-covariant
@extends


*/
interface LengthAwarePaginator extends Paginator
{







public function getUrlRange($start, $end);






public function total();






public function lastPage();
}
