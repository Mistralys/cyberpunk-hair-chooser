<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\ArrayDataCollection;
use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper\JSONFile;
use AppUtils\Interfaces\StringPrimaryRecordInterface;

class HairArchive implements StringPrimaryRecordInterface
{
    public const KEY_LABEL = 'label';
    public const KEY_NUMBER = 'number';

    private FileInfo $archiveFile;
    private string $id;
    private JSONFile $dataFile;
    private ArrayDataCollection $data;
    private ?string $label = null;
    private DownloadedFile $download;

    public function __construct(DownloadedFile $download, FileInfo $archiveFile)
    {
        // The ID is based on the download's name and the archive file's name,
        // so that the same archive can be used in different downloads without
        // causing conflicts.
        //
        // Only the file names are used without paths to be path-independent.
        $this->id = md5($download->getName().'-'.$archiveFile->getName());

        $this->download = $download;
        $this->archiveFile = $archiveFile;
        $this->dataFile = JSONFile::factory(__DIR__.'/../storage/numbers/'.$this->id.'.json');
        $this->data = new ArrayDataCollection();

        $this->loadData();

        for($i=99; $i >= 1; $i--) {
            $this->stripWords[] = 'no'.$i;
        }
    }

    private function loadData() : void
    {
        if($this->dataFile->exists()) {
            $this->data->setKeys($this->dataFile->parse());
        }
    }

    public function getDownload(): DownloadedFile
    {
        return $this->download;
    }

    public function getID(): string
    {
        return $this->id;
    }

    public function getName() : string
    {
        return $this->archiveFile->getBaseName();
    }

    public function getNumber() : int
    {
        $nr = $this->data->getInt(self::KEY_NUMBER);
        if($nr > 0) {
            return $nr;
        }

        return $this->autoDetectNumber();
    }

    private array $replacements = array(
        '_' => ' ',
        '.' => ' ',
        '-' => ' ',
        '#' => ' ',
        '!' => ' ',
        '(' => ' ',
        ')' => ' ',
        '[' => ' ',
        ']' => ' ',
    );

    private array $stripWords = array(
        'hair',
        'femv'
    );

    public function getLabel() : string
    {
        if(isset($this->label)) {
            return $this->label;
        }

        $name = str_replace(
            array_keys($this->replacements),
            array_values($this->replacements),
            mb_strtolower($this->archiveFile->getBaseName())
        );

        while(str_contains($name, '  ')) {
            $name = str_replace('  ', ' ', $name);
        }

        $parts = explode(' ', $name);

        foreach($this->stripWords as $word) {
            $parts = array_diff($parts, array($word));
        }

        $this->label = ucwords(implode(' ', $parts));

        return $this->label;
    }

    public function getPrettyLabel() : string
    {
        $saved = $this->data->getString(self::KEY_LABEL);
        if(!empty($saved)) {
            return $saved;
        }

        return $this->getLabel();
    }

    private function autoDetectNumber() : int
    {
        preg_match('/no(\d+)/i', $this->archiveFile->getBaseName(), $matches);

        if(isset($matches[1])) {
            return (int)$matches[1];
        }

        return 0;
    }

    public function setNumber(int $number) : self
    {
        $this->data->setKey(self::KEY_NUMBER, $number);

        return $this->save();
    }

    public function setPrettyLabel(string $label) : self
    {
        $this->data->setKey(self::KEY_LABEL, $label);
        return $this->save();
    }

    private function save() : self
    {
        $this->dataFile->putData($this->data->getData(), true);
        return $this;
    }

    public function getFilePath() : string
    {
        return $this->archiveFile->getPath();
    }
}
