<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use Symfony\Component\HttpFoundation\Response;

class CategoryController
{
    /**
     * @return Response
     */
    public function getAll(): Response
    {
        return new Response('Categories getall');
    }
}
