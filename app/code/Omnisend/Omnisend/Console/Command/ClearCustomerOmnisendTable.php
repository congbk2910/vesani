<?php

namespace Omnisend\Omnisend\Console\Command;

use Omnisend\Omnisend\Model\ResourceModel\OmnisendContact as OmnisendContactResource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCustomerOmnisendTable extends Command
{
    const COMMAND_NAME = 'omnisend:contacts:clear-customers';
    const COMMAND_DESCRIPTION = 'Clears customer_omnisend table.';

    /**
     * @var OmnisendContactResource
     */
    private $omnisendContactResource;

    /**
     * @param OmnisendContactResource $omnisendContactResource
     * @param null $name
     */
    public function __construct(OmnisendContactResource $omnisendContactResource, $name = null)
    {
        $this->omnisendContactResource = $omnisendContactResource;

        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Clearing omnisend customers table...');
        $this->omnisendContactResource->clearTable();
        $output->writeln('Omnisend customers table was cleared.');
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)->setDescription(self::COMMAND_DESCRIPTION);
    }
}
