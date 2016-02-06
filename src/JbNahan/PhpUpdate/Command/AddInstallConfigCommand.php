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
use Symfony\Component\Console\Question\Question;

class AddInstallConfigCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('config:add')
            ->setDescription('Add the configuration for one install of PHP')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = [];
        $helper = $this->getHelper('question');

        $target = new Question('<info>Please enter the install name : </info>', 'php70');
        $target->setValidator(function ($answer) {
            if ($this->getApplication()->phpInstallExists($answer)) {
                throw new \RuntimeException(
                    'This install name already exists !'
                );
            }

            return $answer;
        });
        $target->setMaxAttempts(2);

        $name = $helper->ask($input, $output, $target);

        $tmp_dir = new Question('<info>Please enter the temporary folder : </info>', 'c:\\sites\\outils');
        $tmp_dir->setValidator(function ($answer) {
            if (!file_exists($answer)) {
                throw new \RuntimeException(
                    'The temporary folder does not exists !'
                );
            }

            return $answer;
        });
        $tmp_dir->setMaxAttempts(2);

        $config['tmp_dir'] = $helper->ask($input, $output, $tmp_dir);

        $php_dir = new Question('<info>Please enter the PHP folder : </info>', 'c:\\sites\\outils');
        $php_dir->setValidator(function ($answer) {
            if (!file_exists($answer)) {
                throw new \RuntimeException(
                    'The PHP folder does not exists !'
                );
            }
            if (null !== $this->getApplication()->configFromPhpDir($answer)) {
                throw new \RuntimeException(
                    'The PHP folder is already used in another configuration !'
                );
            }

            return $answer;
        });
        $php_dir->setMaxAttempts(2);

        $config['php_dir'] = $helper->ask($input, $output, $php_dir);

        $backup_dir = new Question('<info>Please enter the backup folder : </info>', 'c:\\sites\\outils');
        $backup_dir->setValidator(function ($answer) {
            if (!file_exists($answer)) {
                throw new \RuntimeException(
                    'The backup folder does not exists !'
                );
            }

            return $answer;
        });
        $backup_dir->setMaxAttempts(2);

        $config['backup_dir'] = $helper->ask($input, $output, $backup_dir);

        $php_branch = new Question('<info>PHP branch : </info>', 'php56');
        $app = $this->getApplication();
        $php_branch->setValidator(function ($answer) use ($app) {
            if (!$app->getSources()->branchExists($answer)) {
                throw new \RuntimeException(
                    'The branch does not exists !'
                );
            }

            return $answer;
        });
        $php_branch->setMaxAttempts(2);

        $config['php_branch'] = $helper->ask($input, $output, $php_branch);

        $app->addPhpInstall($name, $config);//['install' => [$name => $config]]);

        $app->saveCurrentConfig();

        $output->writeln('<comment>Configuration  end. Use \'config:show\' command for check configuration.</comment>');
    }
}
