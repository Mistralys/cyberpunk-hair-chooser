<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\ArrayDataCollection;
use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper\JSONFile;
use AppUtils\ImageHelper;
use AppUtils\Interfaces\StringPrimaryRecordInterface;
use function AppLocalize\t;

class HairArchive implements StringPrimaryRecordInterface
{
    public const KEY_LABEL = 'label';
    public const KEY_NUMBER = 'number';
    public const ORIENTATION_LANDSCAPE = 'landscape';
    public const ORIENTATION_SQUARE = 'square';
    public const ORIENTATION_PORTRAIT = 'portrait';

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

    /**
     * @var string[]
     */
    private array $imageExtensions = array(
        'png',
        'webp',
        'jpg',
        'jpeg',
        'gif'
    );

    public function getImageSuffix(int $number) : string
    {
        if($this->isMultiSlot()) {
            return '['.$number.']';
        }

        return '';
    }

    public function getImageFile(int $number) : ?FileInfo
    {
        $suffix = $this->getImageSuffix($number);

        // Do we know this file already?
        foreach($this->imageExtensions as $ext) {
            $file = FileInfo::factory(__DIR__.'/../img/previews/'.$this->getID().$suffix.'.'.$ext);
            if($file->exists()) {
                return $file;
            }
        }

        $path = $this->archiveFile->getPath();

        // Try to find an image with the same file name
        foreach($this->imageExtensions as $ext) {
            $file = FileInfo::factory(str_replace('.archive', $suffix.'.'.$ext, $path));
            if($file->exists()) {
                $this->savePreviewCache($file, $number);
                return $file;
            }
        }

        return null;
    }

    private function savePreviewCache(FileInfo $imageFile, int $number) : void
    {
        $cached = FileInfo::factory(__DIR__.'/../img/previews/'.$this->getID().$this->getImageSuffix($number).'.'.$imageFile->getExtension());
        $imageFile->copyTo($cached);
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

    public function getNumbers() : array
    {
        $numbers = $this->data->getArray(self::KEY_NUMBER);
        if(!empty($numbers)) {
            return $numbers;
        }

        return $this->autoDetectNumbers($this->archiveFile->getBaseName());
    }

    public function getNumbersAsString() : string
    {
        $numbers = $this->getNumbers();

        if(empty($numbers)) {
            return '';
        }
        
        if(count($numbers) === 1) {
            return (string)$numbers[0];
        }

        return array_shift($numbers).'-'.array_pop($numbers);
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

    public function getPrettyLabel(?int $number=null) : string
    {
        $saved = $this->data->getString(self::KEY_LABEL);
        if(!empty($saved)) {
            $label = $saved;
        } else {
            $label = $this->getLabel();
        }

        if($number !== null && count($this->getNumbers()) > 1) {
            $label .= ' ('.t('Nr %1$s', $number).')';
        }

        return $label;
    }

    /**
     * @return int[]
     */
    private function autoDetectNumbers(string $name) : array
    {
        preg_match('/no(\d+)-(\d+)|no(\d+)/i', $name, $matches);

        if(isset($matches[3])) {
            return array((int)$matches[3]);
        }

        if(isset($matches[1])) {
            return range((int)$matches[1], (int)$matches[2]);
        }

        return array();
    }

    public function setNumbers(string $number) : self
    {
        $range = $this->autoDetectNumbers('no'.$number);

        $this->data->setKey(self::KEY_NUMBER, $range);

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

    public function getImageURL(int $number) : string
    {
        $params = array();
        $imageFile = $this->getImageFile($number);

        if($imageFile !== null) {
            $params[HairArchiveCollection::REQUEST_VAR_HAIR_SLOT] = $number;
            $params[HairArchiveCollection::REQUEST_VAR_ARCHIVE_ID] = $this->getID();
        }

        return UserInterface::getInstance()
            ->getPageByID(UserInterface::PAGE_MEDIA_VIEWER)
            ->getAdminURL($params);
    }

    private ?string $noPreviewOrientation = null;

    public function getNoPreviewOrientation() : string
    {
        if(!isset($this->noPreviewOrientation)) {
            $this->noPreviewOrientation = $this->resolveOrientation(__DIR__.'/../img/no-preview.jpg');
        }

        return $this->noPreviewOrientation;
    }

    private function resolveOrientation(string $path) : string
    {
        $helper = ImageHelper::createFromFile($path);
        $size = $helper->getSize();
        $helper->dispose();

        if($size->getWidth() > $size->getHeight()) {
            return self::ORIENTATION_LANDSCAPE;
        }

        if($size->getWidth() === $size->getHeight()) {
            return self::ORIENTATION_SQUARE;
        }

        return self::ORIENTATION_PORTRAIT;
    }

    public function getImageOrientation(int $number) : string
    {
        $imageFile = $this->getImageFile($number);
        if($imageFile !== null) {
            return $this->resolveOrientation($imageFile->getPath());
        }

        return $this->getNoPreviewOrientation();
    }

    private function isMultiSlot() : bool
    {
        return count($this->getNumbers()) > 1;
    }
}
