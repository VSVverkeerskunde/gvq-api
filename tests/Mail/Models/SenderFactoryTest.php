<?php declare(strict_types=1);

namespace VSV\GVQ_API\Mail\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class SenderFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_create_a_sender_from_native_strings()
    {
        $actualSender = SenderFactory::fromNative(
            'info@gvq.be',
            'Info GVQ'
        );

        $this->assertEquals(
            ModelsFactory::createSender(),
            $actualSender
        );
    }
}
