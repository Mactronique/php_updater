<?php
/**
 * This file is part of package Php Updater.
 *
 * @license MIT
 * @author Jean-Baptiste Nahan <jb@nahan.fr>
 * @copyright 2015 Jean-Baptiste Nahan
 */

namespace JbNahan\PhpUpdate\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class ShowConfigCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('config:show')
            ->setDescription('Display the configuration')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        if (!$this->getApplication()->isConfigured()) {
            throw new \Exception("Cannot run update if app is not configured", 1);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getApplication()->getConfig();
        $config2 = [];
        foreach ($config as $key => $value) {
            $config2[] = [$key, $value];
        }

        $table = new Table($output);
        $table
            ->setHeaders(array('Key', 'Value'))
            ->setRows($config2);

        $table->render();

    }
}
