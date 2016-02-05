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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use JbNahan\PhpUpdate\Manager\UpdatePhpInstall;

class UpdateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('php:update')
            ->setDescription('Update for available version or reinstall last update')
            ->addArgument(
                'install_name',
                InputArgument::OPTIONAL,
                'Install to update'
            )
            ->addOption(
                'no-install',
                null,
                InputOption::VALUE_NONE,
                'Prepare but not install the version'
            )
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        if (false === getenv('OS') || 'Windows_NT' !== getenv('OS')) {
            $this->getApplication()->getLogger()->error('This project can run only on Windows System');
            throw new \Exception('This project can run only on Windows System', 1);
        }

        if (!$this->getApplication()->isConfigured()) {
            $this->getApplication()->getLogger()->error('Cannot run update if app is not configured');
            throw new \Exception('Cannot run update if app is not configured', 1);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getApplication()->configForInstall($input->getArgument('install_name'));

        $updater = new UpdatePhpInstall($config, $this->getApplication()->getSources());

        $branch = $config['php_branch'];
        $output->writeln(sprintf('Branch PHP : <info>%s</info>', $branch));

        $latest = $this->getApplication()->getSources()->latestVersionForBranch($branch);
        $output->writeln(sprintf('Version to install : <info>%s</info>', $latest));

        if ($input->getOption('no-install')) {
            $output->writeln('<comment>No Install option !</comment>');

            return 0;
        }

        $updater->update($latest, $output);

        $command = $this->getApplication()->find('php:version');

        $command->run(new ArrayInput([]), $output);
    }
}
