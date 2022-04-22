<?php

namespace Omnisend\Omnisend\Console\Command;

use Omnisend\Omnisend\Model\ResourceModel\Order as OrderResource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetOrderImportFlags extends Command
{
    const COMMAND_NAME = 'omnisend:import-status:reset-order-flags';
    const COMMAND_DESCRIPTION = 'Resets imported orders flag values.';

    /**
     * @var OrderResource
     */
    private $orderResource;

    /**
     * @param OrderResource $orderResource
     * @param null $name
     */
    public function __construct(OrderResource $orderResource, $name = null)
    {
        $this->orderResource = $orderResource;

        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Resetting order flags...');
        $affectedRows = $this->orderResource->resetIsImportedValues();
        $output->writeln('Action complete, total affected rows - ' . $affectedRows . '.');
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)->setDescription(self::COMMAND_DESCRIPTION);
    }
}
