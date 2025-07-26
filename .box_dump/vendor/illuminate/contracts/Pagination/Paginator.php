<?php

namespace Illuminate\Contracts\Pagination;

/**
@template
@template-covariant



*/
interface Paginator
{






public function url($page);








public function appends($key, $value = null);







public function fragment($fragment = null);






public function withQueryString();






public function nextPageUrl();






public function previousPageUrl();






public function items();






public function firstItem();






public function lastItem();






public function perPage();






public function currentPage();






public function hasPages();






public function hasMorePages();






public function path();






public function isEmpty();






public function isNotEmpty();








public function render($view = null, $data = []);
}
