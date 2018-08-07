<?php declare(strict_types=1);

namespace VSV\GVQ_API\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('gvq:test-redis')
            ->setDescription('Test redis');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Testing redis...');

        $redis = new \Redis();
        $redis->connect('127.0.0.1', 63799);

        $output->writeln('Connected to redis 127.0.0.1:63799');

        $cache = $redis->get('key');

        if ($cache === false) {
            $output->writeln('No value found for key');

            $output->writeln('Writing value for key...');
            $redis->set('key', 'value');

            $output->writeln('Deleting value for key...');
            $redis->delete('key');
        } else {
            $output->writeln('Value found for key');
        }

        $output->writeln('Testing finished.');
    }
}
