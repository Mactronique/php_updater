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

        krsort($branch_ver);
        return current($branch_ver);
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