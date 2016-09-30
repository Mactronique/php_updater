<?php

/**
 * This file is part of package Php Updater.
 *
 * @license MIT
 * @author Jean-Baptiste Nahan <jb@nahan.fr>
 * @copyright 2015 Jean-Baptiste Nahan
 */
namespace JbNahan\PhpUpdate\Manager;

class PHPSourceManager
{
    private $master;
    private $archives;
    private $versions;

    public function __construct($master, $archives, array $versions)
    {
        $this->master = $master;
        $this->archives = $archives;
        $this->versions = $versions;
    }

    public function latestVersionForBranch($branch)
    {
        $branch_ver = $this->versions[$branch];

        natsort($branch_ver);
        return end($branch_ver);
    }

    public function zipNameForVersion($branch, $version)
    {
        if (!$this->branchExists($branch)) {
            return null;
        }
        $branch_ver = $this->versions[$branch];
        if (!array_key_exists($version, $branch_ver)) {
            return null;
        }
        return $branch_ver[$version];
    }

    public function branchExists($branch)
    {
        return array_key_exists($branch, $this->versions);
    }

    public function master()
    {
        return $this->master;
    }
    public function archives()
    {
        return $this->archives;
    }
}
