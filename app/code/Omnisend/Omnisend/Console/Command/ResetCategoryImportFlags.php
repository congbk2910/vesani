<?php

namespace Omnisend\Omnisend\Console\Command;

use Omnisend\Omnisend\Model\ResourceModel\Category as CategoryResource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ResetCategoryImportFlags
 * @package Omnisend\Omnisend\Console\Command
 */
class ResetCategoryImportFlags extends Command
{
    const COMMAND_NAME = 'omnisend:import-status:reset-category-flags';
    const COMMAND_DESCRIPTION = 'Resets imported categories flag values.';

    /**
     * @var CategoryResource
     */
    private $categoryResource;

    /**
     * ResetCategoryImportFlags constructor.
     * @param CategoryResource $categoryResource
     * @param null $name
     */
    public function __construct(
        CategoryResource $categoryResource,
        $name = null
    ) {
        $this->categoryResource = $categoryResource;
        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Resetting category flags...');
        $affectedRows = $this->categoryResource->resetIsImportedValues();
        $output->writeln('Action complete, total affected rows - ' . $affectedRows . '.');
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)->setDescription(self::COMMAND_DESCRIPTION);
    }
}
