<?php

namespace App\Component;

use Symfony\Component\HttpFoundation\Request;

class Paginator
{
    public const PER_PAGE = 2;

    public $currentPage;
    public $selfPath;
    public $pages;

    public function __construct(Request $request, int $count)
    {
        $this->selfPath = $request->get('_route');
        $this->currentPage = $request->get('page',  1);
        $this->pages = ceil($count/self::PER_PAGE);
    }

    public function getOffset()
    {
        return ($this->currentPage-1)*self::PER_PAGE;
    }
}