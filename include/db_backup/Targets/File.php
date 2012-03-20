<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *********************************************************************************/

require_once 'include/db_backup/Targets/Response.php';

/**
 * Description of File
 *
 * @author MAK
 */
class File extends Response {
	private $defaultPath;
	private $filePath = null;

	public function __construct($dbConfig, $fileName=null, $supportUTF8 = true) {
		parent::__construct($dbConfig, $supportUTF8);
		$this->defaultPath = 'backup'.DIRECTORY_SEPARATOR;
		if(empty($fileName)) {
			$this->filePath = $this->getDefaultFilePath();
		}else{
			$folder = $this->getDefaultFolderPath();
			$this->filePath = $folder.$fileName;
		}
	}

	private function getDefaultFolderPath() {
		require 'config.inc.php';
		$rootPath = (strrpos($root_directory, DIRECTORY_SEPARATOR) !== false)? $root_directory.
				DIRECTORY_SEPARATOR:$root_directory;
		$rootPath = $this->fixPathSeparator($rootPath);
		return $rootPath.$this->defaultPath;
	}

	public function fixPathSeparator($path) {
		$start = 0;
		do {
			$done = false;
			$index = strpos($path, '/',$start);
			$start = $index + 1;
			if($index != false && $path[$index - 1] == '\\'.DIRECTORY_SEPARATOR) {
				continue;
			}else if($index != false){
				$path[$index] = DIRECTORY_SEPARATOR;
			}
		}while($index != false);
		return $path;
	}

	public function getDefaultFilePath() {
		$folder = $this->getDefaultFolderPath();
		return $folder.strtotime("now").'.sql';
	}

	public function startBackup() {
		$this->file = fopen($this->filePath, 'w');
		parent::startBackup();
	}

	protected function writeLine($string) {
		fwrite($this->file,$string."\n");
	}

	public function finishBackup() {
		parent::finishBackup();
		fclose($this->file);
	}

	public function getFilePath() {
		return $this->filePath;
	}

}
?>