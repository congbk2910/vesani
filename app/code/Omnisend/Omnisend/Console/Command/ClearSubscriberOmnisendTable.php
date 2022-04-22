<?php

namespace Omnisend\Omnisend\Console\Command;

use Omnisend\Omnisend\Model\ResourceModel\OmnisendGuestSubscriber as OmnisendGuestSubscriberResource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearSubscriberOmnisendTable extends Command
{
    const COMMAND_NAME = 'omnisend:contacts:clear-guest-subscribers';
    const COMMAND_DESCRIPTION = 'Clears guest_subscriber_omnisend table.';

    /**
     * @var OmnisendGuestSubscriberResource
     */
    private $omnisendGuestSubscriberResource;

    /**
     * @param OmnisendGuestSubscriberResource $omnisendGuestSubscriberResource
     * @param null $name
     */
    public function __construct(OmnisendGuestSubscriberResource $omnisendGuestSubscriberResource, $name = null)
    {
        $this->omnisendGuestSubscriberResource = $omnisendGuestSubscriberResource;

        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Clearing omnisend guest subscribers table...');
        $this->omnisendGuestSubscriberResource->clearTable();
        $output->writeln('Omnisend guest subscribers table was cleared.');
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)->setDescription(self::COMMAND_DESCRIPTION);
    }
}
