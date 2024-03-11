<?php
/**
 * @package CPHairChooser
 */

namespace Mistralys\CPHairChooser;

use AppUtils\ConvertHelper;
use AppUtils\ConvertHelper\JSONConverter;
use AppUtils\FileHelper;
use AppUtils\FileHelper\FileInfo;
use AppUtils\ZIPHelper;

/**
 * Builds a ZIP file containing the mod's archives and images.
 *
 * @package CPHairChooser
 */
class ModBuilder
{
    private HairMod $mod;
    private FileInfo $zipFile;
    private ZIPHelper $zip;

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

        $this->zip = new ZIPHelper($this->zipFile->getPath());

        $archives = $this->mod->getArchives();

        foreach($archives as $archive)
        {
            $this->addArchive($archive);
        }

        $this->zip->save();
    }

    private function addArchive(HairArchive $archive) : void
    {
        $baseName = sprintf(
            '[%s]-%s',
            $archive->getNumbersAsString(),
            ConvertHelper::transliterate($archive->getPrettyLabel())
        );

        $this->zip->addFile(
            $archive->getFilePath(),
            $baseName.'.archive'
        );

        $numbers = $archive->getNumbers();

        foreach($numbers as $number)
        {
            $imageFile = $archive->getImageFile($number);

            if ($imageFile !== null) {
                $this->zip->addFile(
                    $imageFile->getPath(),
                    $baseName . $archive->getImageSuffix($number) . '.' . $imageFile->getExtension()
                );
            }
        }
    }
}
