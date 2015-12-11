<?php
/**
 * This file is part of package Php Updater.
 *
 * @license MIT
 * @author Jean-Baptiste Nahan <jb@nahan.fr>
 * @copyright 2015 Jean-Baptiste Nahan
 */

namespace JbNahan\PhpUpdate\Output;

use Symfony\Component\Console\Output\ConsoleOutput;
use Psr\Log\LoggerInterface;

class MonologOutput extends ConsoleOutput
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct();
    }

    public function write($messages, $newline = false, $options = self::OUTPUT_NORMAL)
    {
        $messagesToLog = (array) $messages;
        foreach ($messagesToLog as $message) {
            if (strlen($message) === 0) {
                continue;
            }
            //$message = strip_tags($this->getFormatter()->format($message));
            $message = strip_tags($message);
            $this->logger->info($message);
        }
        parent::write($messages, $newline, $options);
    }
}
