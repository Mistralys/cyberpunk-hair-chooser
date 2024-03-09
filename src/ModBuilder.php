<?php

namespace Mistralys\CPHairChooser;

use AppUtils\ConvertHelper;
use AppUtils\ConvertHelper\JSONConverter;
use AppUtils\FileHelper;
use AppUtils\FileHelper\FileInfo;
use AppUtils\ZIPHelper;

class ModBuilder
{
    private HairMod $mod;
    private FileInfo $zipFile;

    public function __construct(HairMod $mod)
    {
        $this->mod = $mod;
        $this->zipFile = FileInfo::factory(sprintf(
            '%s/%s v%s.zip',
            BUILT_MODS_FOLDER,
            $mod->getLabel(),
            $mod->getVersion()
        ));
    }

    public function getTargetFile() : FileInfo
    {
        return $this->zipFile;
    }

    public function build() : void
    {
        FileHelper::createFolder($this->zipFile->getFolderPath());

        if($this->zipFile->exists()) {
            $this->zipFile->delete();
        }

        $zip = new ZIPHelper($this->zipFile->getPath());

        $archives = $this->mod->getArchives();

        foreach($archives as $archive)
        {
            $baseName = sprintf(
                '%s-no%s',
                ConvertHelper::transliterate($archive->getPrettyLabel()),
                $archive->getNumbersAsString()
            );

            $zip->addFile(
                $archive->getFilePath(),
                $baseName.'.archive'
            );

            $numbers = $archive->getNumbers();
            foreach($numbers as $number) {
                $imageFile = $archive->getImageFile($number);
                if ($imageFile !== null) {
                    $zip->addFile(
                        $imageFile->getPath(),
                        $baseName . $archive->getImageSuffix($number) . '.' . $imageFile->getExtension()
                    );
                }
            }
        }

        $zip->save();
    }
}