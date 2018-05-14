<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Controllers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_a_response()
    {
        $controller = new CategoryController();
        $this->assertEquals(
            new Response('Categories getall'),
            $controller->getAll()
        );
    }
}
