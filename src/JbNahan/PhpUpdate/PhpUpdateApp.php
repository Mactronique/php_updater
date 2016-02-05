<?php

/**
 * This file is part of package Php Updater.
 *
 * @license MIT
 * @author Jean-Baptiste Nahan <jb@nahan.fr>
 * @copyright 2015 Jean-Baptiste Nahan
 */
namespace JbNahan\PhpUpdate;

use JbNahan\PhpUpdate\Config\PhpUpdateConfig;
use JbNahan\PhpUpdate\Config\SourceConfig;
use JbNahan\PhpUpdate\Output\MonologOutput;
use JbNahan\PhpUpdate\Manager\PHPSourceManager;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;

class PhpUpdateApp extends Application
{
    private $rootDir;

    private $configDir;

    private $configs;

    private $sources;

    private $logger;

    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct('PhpUpdate', '1.1.0');
        $this->add(new Command\ShowConfigCommand());
        $this->add(new Command\UpdateCommand());
        $this->add(new Command\AddInstallConfigCommand());
        $this->add(new Command\CurrentVersionCommand());

        $this->rootDir = realpath(__DIR__.'/../../../');
        $this->configDir = $this->rootDir.DIRECTORY_SEPARATOR.'config';

        /*
         * Démarrage de Monolog
         */

        $this->logger = new Logger('app');
        $this->logger->pushHandler(new StreamHandler($this->rootDir.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.date('Y-m-d').'.log', Logger::INFO));
        $this->logger->debug('Logger on');
        $this->logger->info('Context', $_SERVER);
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $input) {
            $input = new ArgvInput();
        }

        if (null === $output) {
            $output = new MonologOutput($this->logger);//ConsoleOutput
        }

        $this->configureIO($input, $output);

        try {
            $this->boot();
            $this->setCatchExceptions(false);
            $exitCode = parent::run($input, $output);
        } catch (\Exception $e) {
            $this->getLogger()->error('Exception : '.$e->getMessage(), ['e' => $e]);

            if ($output instanceof ConsoleOutputInterface) {
                $this->renderException($e, $output->getErrorOutput());
            } else {
                $this->renderException($e, $output);
            }

            $exitCode = $e->getCode();
            if (is_numeric($exitCode)) {
                $exitCode = (int) $exitCode;
                if (0 === $exitCode) {
                    $exitCode = 1;
                }
            } else {
                $exitCode = 1;
            }

            if ($exitCode > 255) {
                $exitCode = 255;
            }

            exit($exitCode);
        }

        return $exitCode;
    }

    public function getConfig()
    {
        return $this->configs;
    }

    public function configForInstall($install_name = null)
    {
        $allInstall = array_keys($this->configs['install']);

        //Une install et pas de valeur dans la ligne de commande.
        if (null === $install_name && 1 == count($allInstall)) {
            $install_name = $allInstall[0];
        }

        if (null === $install_name || !in_array($install_name, $allInstall)) {
            throw new \Exception('Install not found "'.((null === $install_name) ? 'null' : $install_name).'". Please select between ['.implode(', ', $allInstall).']', 1);
        }

        return $this->configs['install'][$install_name];
    }

    public function addPhpInstall($name, array $config){
        if($this->configs === null){
            $this->configs= ['install'=>[]];
        }
        if($this->phpInstallExists($name)){
            throw new \Exception("This configuration exists already '".$name."'", 1);
        }

        $this->configs['install'][$name] = $config;

        $this->setConfig($this->configs);
    }

    public function phpInstallExists($name){
        return ($this->configs !== null) && array_key_exists($name, $this->configs['install']);
    }

    public function configFromPhpDir($dir){
        if($this->configs === null) {
            return null;
        }
        foreach ($this->configs['install'] as $name => $conf) {
            if($conf['php_dir'] == $dir){
                return $conf;
            }
        }
        return null;
    }

    public function isConfigured()
    {
        return null !== $this->configs;
    }

    public function getSources()
    {
        return $this->sources;
    }

    public function setConfig(array $config)
    {
        $this->validateConfig($config);
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function saveCurrentConfig()
    {
        $dumper = new Dumper();

        $yaml = $dumper->dump($this->configs, 2);

        file_put_contents($this->configDir.DIRECTORY_SEPARATOR.'config.yml', $yaml);
    }

    private function boot()
    {
        $this->logger->debug('App boot');
        if (PHP_VERSION_ID < 50613) {
            $this->getLogger()->error('Error : This app cannot run on PHP version prior 5.6.13');
            throw new \Exception('Error : This app cannot run on PHP version prior 5.6.13', 1);
        }

        $sourcesFile = $this->configDir.DIRECTORY_SEPARATOR.'sources.yml';

        if (!file_exists($sourcesFile)) {
            $this->getLogger()->error('Error : Sources file not found');
            throw new \Exception('Error : Sources file not found', 1);
        }

        /*
         * Chargement du fichier source
         */

        $sources = Yaml::parse(
            file_get_contents($sourcesFile)
        );

        $configs = [$sources];

        $processor = new Processor();
        $configuration = new SourceConfig();
        $sources = $processor->processConfiguration(
            $configuration,
            $configs
        );

        $this->sources = new PHPSourceManager($sources['master'], $sources['archives'], $sources['versions']);

        /*
         * Chargement de la config si présente
         */

        $configFile = $this->configDir.DIRECTORY_SEPARATOR.'config.yml';
        if (!file_exists($configFile)) {
            $this->logger->debug('No config file found');

            return;
        }

        $config = Yaml::parse(
            file_get_contents($configFile)
        );

        if (null === $config || empty($config)) {
            $this->logger->debug('Empty config file');

            return;
        }

        $this->validateConfig($config);
    }

    private function validateConfig(array $config)
    {
        $processor = new Processor();
        $configuration = new PhpUpdateConfig();
        $this->configs = $processor->processConfiguration(
            $configuration,
            [$config]
        );

        $folder=[];
        foreach ($this->configs['install'] as $name => $conf) {
            if(in_array($conf['php_dir'], $folder)){
                throw new \Exception("This folder '".$conf['php_dir']."' is already used in another configuration.", 1);
            }
            $folder[] = $conf['php_dir'];
        }
    }
}
