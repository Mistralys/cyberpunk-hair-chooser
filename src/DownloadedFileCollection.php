<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\Collections\BaseStringPrimaryCollection;
use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper\JSONFile;

/**
 * @method DownloadedFile getByID(string $id)
 * @method DownloadedFile[] getAll()
 * @method DownloadedFile getDefault()
 */
class DownloadedFileCollection extends BaseStringPrimaryCollection
{
    /**
     * @var string[]
     */
    private array $selectedFiles = array();
    private FileInfo $dataFile;
    private static ?DownloadedFileCollection $instance = null;

    public function __construct()
    {
        $this->dataFile = JSONFile::factory(MODS_EXTRACT_FOLDER.'/selected-files.json');

        if($this->dataFile->exists()) {
            $this->selectedFiles = $this->dataFile->parse();
        }
    }

    public static function getInstance() : DownloadedFileCollection
    {
        if(is_null(self::$instance)) {
            self::$instance = new DownloadedFileCollection();
        }

        return self::$instance;
    }

    public function isFileSelected(string $fileName) : bool
    {
        return in_array($fileName, $this->selectedFiles, true);
    }

    public function clearSelected() : self
    {
        $this->selectedFiles = array();
        return $this;
    }

    public function setFileSelected(string $file) : self
    {
        if(!in_array($file, $this->selectedFiles, true)) {
            $this->selectedFiles[] = $file;
        }

        return $this;
    }

    public function getByFileName(string $fileName) : DownloadedFile
    {
        return $this->getByID($fileName);
    }

    /**
     * @return string[]
     */
    public function getSelectedFiles() : array
    {
        return $this->selectedFiles;
    }

    public function saveSelected() : self
    {
        $this->dataFile->putData($this->selectedFiles, true);
        return $this;
    }

    protected function registerItems(): void
    {
        $selected = $this->getSelectedFiles();

        foreach ($selected as $fileName)
        {
            $sourceFile = FileInfo::factory(MODS_DOWNLOAD_FOLDER.'/'.$fileName);

            if(!$sourceFile->exists()) {
                continue;
            }

            $this->registerItem(new DownloadedFile($sourceFile));
        }
    }

    public function getDefaultID(): string
    {
        return '';
    }
}
