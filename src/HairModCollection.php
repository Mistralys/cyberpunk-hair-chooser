<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\Collections\BaseStringPrimaryCollection;
use AppUtils\FileHelper\JSONFile;
use DirectoryIterator;

/**
 * @method HairMod[] getAll()
 * @method HairMod getByID(string $id)
 * @method HairMod getDefault()
 */
class HairModCollection extends BaseStringPrimaryCollection
{
    public const REQUEST_PARAM_MOD_ID = 'modID';

    public function getByRequest() : ?HairMod
    {
        if(isset($_REQUEST[self::REQUEST_PARAM_MOD_ID]) && $this->idExists($_REQUEST[self::REQUEST_PARAM_MOD_ID])) {
            return $this->getByID($_REQUEST[self::REQUEST_PARAM_MOD_ID]);
        }

        return null;
    }

    public static function factory() : HairModCollection
    {
        return new self();
    }

    protected function registerItems(): void
    {
        $d = new DirectoryIterator(__DIR__.'/../storage/mods');
        foreach($d as $item) {
            if($item->isDot() || !$item->isDir()) {
                continue;
            }

            $jsonFile = JSONFile::factory($item->getPathname().'/info.json');
            if($jsonFile->exists()) {
                $this->registerItem(new HairMod($jsonFile));
            }
        }
    }

    public function getDefaultID(): string
    {
        return '';
    }
}
