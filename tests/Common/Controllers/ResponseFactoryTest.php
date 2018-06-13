<?php

namespace VSV\GVQ_API\Common\Controllers;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class ResponseFactoryTest extends TestCase
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    protected function setUp():void
    {
        $this->responseFactory = new ResponseFactory();
    }

    /**
     * @test
     */
    public function it_can_create_a_csv_response()
    {
        $usersAsCsv = ModelsFactory::readCsv('users');

        $csvResponse = $this->responseFactory->createCsvResponse(
            $usersAsCsv,
            'users'
        );

        $this->assertEquals(
            'UTF-8',
            $csvResponse->headers->get('Content-Encoding')
        );
        $this->assertEquals(
            'application/csv; charset=UTF-8',
            $csvResponse->headers->get('Content-Type')
        );
        $this->assertEquals(
            'binary',
            $csvResponse->headers->get('Content-Transfer-Encoding')
        );
        $this->assertContains(
            'attachment; filename="users_',
            $csvResponse->headers->get('Content-Disposition')
        );

        $csvData = chr(0xFF).chr(0xFE);
        $csvData .= mb_convert_encoding('sep=,'.PHP_EOL.$usersAsCsv, 'UTF-16LE', 'UTF-8');
        $this->assertEquals(
            $csvData,
            $csvResponse->getContent()
        );
    }
}
