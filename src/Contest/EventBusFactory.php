<?php


namespace VSV\GVQ_API\Contest;

use Broadway\EventHandling\SimpleEventBus;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EventBusFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var bool
     */
    private $contestClosed;

    /**
     * @var string
     */
    private $serviceWhenContestOpen;

    /**
     * @var string
     */
    private $serviceWhenContestClosed;


    public function __construct(
        ContainerInterface $container,
        bool $contestClosed,
        string $serviceWhenContestClosed,
        string $serviceWhenContestOpen
    ) {
        $this->container = $container;
        $this->contestClosed = $contestClosed;
        $this->serviceWhenContestClosed = $serviceWhenContestClosed;
        $this->serviceWhenContestOpen = $serviceWhenContestOpen;
    }

    public function createEventBus(): SimpleEventBus
    {
        $serviceName = $this->contestClosed ? $this->serviceWhenContestClosed : $this->serviceWhenContestOpen;

        /** @var SimpleEventBus $eventBus */
        $eventBus = $this->container->get($serviceName);

        return $eventBus;
    }
}
