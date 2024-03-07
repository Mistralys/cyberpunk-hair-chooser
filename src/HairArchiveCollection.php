<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\Collections\BaseStringPrimaryCollection;
use AppUtils\FileHelper;
use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper\FolderInfo;

/**
 * @method HairArchive[] getAll()
 * @method HairArchive getByID(string $id)
 * @method HairArchive getDefault()
 */
class HairArchiveCollection extends BaseStringPrimaryCollection
{
    /**
     * @var FolderInfo[]
     */
    private array $folders = array();

    public function __construct(array $folders)
    {
        foreach ($folders as $folder) {
            $this->folders[] = FolderInfo::factory($folder);
        }
    }

    public static function factory() : HairArchiveCollection
    {
        return new HairArchiveCollection(HAIR_MOD_FOLDERS);
    }

    public function getDefaultID(): string
    {
        return '';
    }

    /**
     * @return array<string, HairArchive[]>
     */
    public function getGroupedByNumber() : array
    {
        $grouped = array();
        $archives = $this->getAll();

        foreach($archives as $archive)
        {
            $number = $archive->getNumber();

            if(!isset($grouped[$number])) {
                $grouped[$number] = array();
            }

            $grouped[$number][] = $archive;
        }

        foreach($grouped as $number => $items) {
            usort($items, static function(HairArchive $a, HairArchive $b) {
                return strnatcasecmp($a->getPrettyLabel(), $b->getPrettyLabel());
            });

            $grouped[$number] = $items;
        }

        ksort($grouped);

        return $grouped;
    }

    protected function registerItems(): void
    {
        foreach($this->folders as $folder) {
            $this->registerFolderItems($folder);
        }

        uasort($this->items, static function(HairArchive $a, HairArchive $b) {
            return $a->getLabel() <=> $b->getLabel();
        });
    }

    private function registerFolderItems(mixed $folder) : void
    {
        $archives = FileHelper::createFileFinder($folder)
            ->includeExtension('archive')
            ->setPathmodeAbsolute()
            ->makeRecursive()
            ->getAll();

        foreach($archives as $archive) {
            $this->registerItem(new HairArchive(FileInfo::factory($archive)));
        }
    }
}
