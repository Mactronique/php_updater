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
use Symfony\Component\Console\Input\InputArgument;
use JbNahan\PhpUpdate\Manager\UpdatePhpInstall;

class CurrentVersionCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('php:version')
            ->setDescription('Display php current version')
            ->addArgument(
                'install_name',
                InputArgument::OPTIONAL,
                'Install to update'
            )
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        if (false === getenv('OS') || 'Windows_NT' !== getenv('OS')) {
            throw new \Exception('This project can run only on Windows System', 1);
        }

        if (!$this->getApplication()->isConfigured()) {
            throw new \Exception('Cannot run update if app is not configured', 1);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $installName = $input->getArgument('install_name');
        $configs = $this->getApplication()->getConfig()['install'];
        if ($installName !== null) {
            if (!isset($configs[$installName])) {
                throw new \Exception("Unable to find configuration ".$installName, 1);
            }
            $configs = [$installName => $configs[$installName]];
        }

        foreach ($configs as $installName => $config) {
            $output->writeln('**********************************************************');
            $output->writeln('Check current version for install '.$installName.' :');
            $output->writeln('**********************************************************');

            $pathInstall = $config['php_dir'];

            exec($pathInstall.DIRECTORY_SEPARATOR.'php.exe -v', $out);
            if (empty($out)) {
                throw new \Exception('Unable to execute php.exe on php_dir', 1);
            }

            foreach ($out as $key => $value) {
                if ($key === 0) {
                    $value = sprintf('<info>%s</info>', $value);
                }
                $output->writeln($value);
            }

            exec($pathInstall.DIRECTORY_SEPARATOR.'php.exe -m', $out2);

            $output->writeln('');
            $output->writeln('<comment>Modules enabled :</comment>');
            $output->writeln(implode(', ', $out2));

            unset($out, $out2);

            $updater = new UpdatePhpInstall($config, $this->getApplication()->getSources());
            if (false !== $version = $updater->updateAvailable()) {
                $output->writeln('');
                $output->writeln('New version available : <comment>'.$version.'</comment>');
            }
            $output->writeln('');
            $output->writeln('');
        }
    }
}
