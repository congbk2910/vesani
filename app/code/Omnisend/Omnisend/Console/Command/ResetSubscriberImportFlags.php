<?php

namespace Omnisend\Omnisend\Console\Command;

use Omnisend\Omnisend\Model\ResourceModel\Subscriber as SubscriberResource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetSubscriberImportFlags extends Command
{
    const COMMAND_NAME = 'omnisend:import-status:reset-subscriber-flags';
    const COMMAND_DESCRIPTION = 'Resets imported subscribers flag values.';

    /**
     * @var SubscriberResource
     */
    private $subscriberResource;

    /**
     * @param SubscriberResource $subscriberResource
     * @param null $name
     */
    public function __construct(SubscriberResource $subscriberResource, $name = null)
    {
        $this->subscriberResource = $subscriberResource;

        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Resetting subscriber flags...');
        $affectedRows = $this->subscriberResource->resetIsImportedValues();
        $output->writeln('Action complete, total affected rows - ' . $affectedRows . '.');
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)->setDescription(self::COMMAND_DESCRIPTION);
    }
}
