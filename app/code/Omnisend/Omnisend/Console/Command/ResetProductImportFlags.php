<?php

namespace Omnisend\Omnisend\Console\Command;

use Omnisend\Omnisend\Model\ResourceModel\Product as ProductResource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetProductImportFlags extends Command
{
    const COMMAND_NAME = 'omnisend:import-status:reset-product-flags';
    const COMMAND_DESCRIPTION = 'Resets imported products flag values.';

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @param ProductResource $productResource
     * @param null $name
     */
    public function __construct(ProductResource $productResource, $name = null)
    {
        $this->productResource = $productResource;

        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Resetting product flags...');
        $affectedRows = $this->productResource->resetIsImportedValues();
        $output->writeln('Action complete, total affected rows - ' . $affectedRows . '.');
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)->setDescription(self::COMMAND_DESCRIPTION);
    }
}
