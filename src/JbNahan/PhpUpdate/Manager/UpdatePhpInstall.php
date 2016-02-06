<?php

/**
 * This file is part of package Php Updater.
 *
 * @license MIT
 * @author Jean-Baptiste Nahan <jb@nahan.fr>
 * @copyright 2015 Jean-Baptiste Nahan
 */
namespace JbNahan\PhpUpdate\Manager;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePhpInstall
{
	private $config;

	private $sources;

	public function __construct(array $config, PHPSourceManager $sources){
		$this->config = $config;
	}

	public function update($latest, OutputInterface $output){
        $this->download($latest, $output);

        $this->backup($output);

        $this->install($latest, $output);
	}



    private function download($latest, OutputInterface $output)
    {
        $completeDest = $this->config['tmp_dir'].DIRECTORY_SEPARATOR.$latest;
        if (file_exists($completeDest)) {
            $output->writeln('Source package : <info> Already download </info>');

            return;
        }

        $url = rtrim($this->sources->master(), '/\\').'/'.$latest;
        $urlArchive = rtrim($this->sources->archives(), '/\\').'/'.$latest;
        $urls = [$url, $urlArchive];

        foreach ($urls as $urlTmp) {
            $output->writeln(sprintf('Download from : <info>%s</info>', $urlTmp));
            $output->writeln(sprintf('To :            <info>%s</info>', $completeDest));

            if (!copy($urlTmp, $completeDest)) {
                $output->writeln('<error> Copy Error ! </error>');
            } else {
                $output->writeln('Download : <info> OK </info>');
                break;
            }
        }
        if (!file_exists($completeDest)) {
            $this->getApplication()->getLogger()->error('Unable to download this version.');
            throw new \Exception('Unable to download this version.', 1);
        }
    }

    private function backup(OutputInterface $output)
    {
        $pathInstall = $this->config['php_dir'];
        $pathBackup = $this->config['backup_dir'];
        $php_branch = $this->config['php_branch'];
        $pathZipBackup = $pathBackup.DIRECTORY_SEPARATOR.$php_branch.'.'.date('YmdHis').'.zip';

        $output->writeln(sprintf('Backup : <info>%s</info>', $pathInstall));
        $output->writeln(sprintf('Into :   <info>%s</info>', $pathZipBackup));

        if (false !== $this->zip($pathInstall, $pathZipBackup, $output)) {
            $output->writeln('Backup : <info> OK </info>');
        } else {
            $this->getApplication()->getLogger()->error('Error Processing Backup');
            throw new \Exception('Error Processing Backup', 1);
        }
    }

    private function install($latest, OutputInterface $output)
    {
        $pathInstall = $this->config['php_dir'];
        $completeDest = $this->config['tmp_dir'].DIRECTORY_SEPARATOR.$latest;

        $output->writeln(sprintf('Install into : <info> %s </info>', $pathInstall));

        $zip = new \ZipArchive();
        if ($zip->open($completeDest)) {
            $zip->extractTo($pathInstall);
            $zip->close();
            $output->writeln('Install : <info> OK </info>');
        } else {
            $this->getApplication()->getLogger()->error('Error in openning package file !');
            throw new \Exception('Error in openning package file !', 1);
        }
    }

    private function zip($source, $destination, OutputInterface $output)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new \ZipArchive();
        if (!$zip->open($destination, \ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                    continue;
                }

                $file = realpath($file);
                $file = str_replace('\\', '/', $file);
                $zipFile = str_replace($source.'/', '', $file);

                $output->writeln("Path : $file => $zipFile", OutputInterface::VERBOSITY_VERBOSE);

                if (is_dir($file) === true) {
                    $zip->addEmptyDir($zipFile.'/');
                } elseif (is_file($file) === true) {
                    $zip->addFromString($zipFile, file_get_contents($file));
                }
            }
        } elseif (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }
}