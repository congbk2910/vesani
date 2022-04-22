<?php

namespace Omnisend\Omnisend\Console\Command;

use Omnisend\Omnisend\Model\ResourceModel\Customer as CustomerResource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetCustomerImportFlags extends Command
{
    const COMMAND_NAME = 'omnisend:import-status:reset-customer-flags';
    const COMMAND_DESCRIPTION = 'Resets imported customers flag values.';

    /**
     * @var CustomerResource
     */
    private $customerResource;

    /**
     * @param CustomerResource $customerResource
     * @param null $name
     */
    public function __construct(CustomerResource $customerResource, $name = null)
    {
        $this->customerResource = $customerResource;

        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Resetting customer flags...');
        $affectedRows = $this->customerResource->resetIsImportedValues();
        $output->writeln('Action complete, total affected rows - ' . $affectedRows . '.');
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)->setDescription(self::COMMAND_DESCRIPTION);
    }
}
