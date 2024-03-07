<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper\FolderInfo;
use AppUtils\Interfaces\StringPrimaryRecordInterface;
use AppUtils\ZIPHelper;

class DownloadedFile implements StringPrimaryRecordInterface
{
    private FileInfo $file;
    private FolderInfo $extractFolder;
    private FileInfo $proofFile;

    public function __construct(FileInfo $file)
    {
        $this->file = $file;
        $this->extractFolder = FolderInfo::factory(sprintf(
            '%s/%s-%s',
            MODS_EXTRACT_FOLDER,
            $this->file->getBaseName(),
            $this->file->getExtension()
        ));
        $this->proofFile = FileInfo::factory($this->extractFolder->getPath().'/proof.txt');
    }

    public function getID() : string
    {
        return $this->file->getName();
    }

    public function isExtracted() : bool
    {
        return $this->proofFile->exists();
    }

    public function getName() : string
    {
        return $this->file->getName();
    }

    public function getSize() : int
    {
        return $this->file->getSize();
    }

    public function extract() : self
    {
        $zip = new ZIPHelper($this->file->getPath());

        $this->extractFolder->create();

        $zip->extractAll($this->extractFolder->getPath());

        $this->proofFile->putContents('Used to verify that the file has been fully extracted.');

        return $this;
    }
}
