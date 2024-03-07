<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\ArrayDataCollection;
use AppUtils\Collections\CollectionException;
use AppUtils\ConvertHelper;
use AppUtils\FileHelper;
use AppUtils\FileHelper\JSONFile;
use AppUtils\Interfaces\StringPrimaryRecordInterface;
use AppUtils\Microtime;

class HairMod implements StringPrimaryRecordInterface
{
    public const KEY_LABEL = 'label';
    public const KEY_ARCHIVES = 'archives';
    public const KEY_ALIAS = 'alias';
    public const KEY_LAST_MODIFIED = 'last_modified';
    public const KEY_VERSION = 'version';

    private JSONFile $dataFile;
    private ArrayDataCollection $data;

    public function __construct(JSONFile $dataFile)
    {
        $this->dataFile = $dataFile;
        $this->data = new ArrayDataCollection($dataFile->parse());
    }

    public function getID() : string
    {
        return $this->data->getString(self::KEY_ALIAS);
    }

    public function getLabel() : string
    {
        return $this->data->getString(self::KEY_LABEL);
    }

    public function getLastModified() : Microtime
    {
        return $this->data->getMicrotime(self::KEY_LAST_MODIFIED);
    }

    public function addArchive(HairArchive $archive) : self
    {
        $archives = $this->getArchiveList();

        $archives[$archive->getNumber()] = $archive->getID();

        $this->data->setKey(self::KEY_ARCHIVES, $archives);

        return $this->save();
    }

    private function save() : self
    {
        $this->data->setMicrotime(self::KEY_LAST_MODIFIED, Microtime::createNow());

        $this->dataFile->putData($this->data->getData(), true);

        return $this;
    }

    public static function createNew(string $label) : HairMod
    {
        $alias = ConvertHelper::transliterate($label);

        $path = sprintf(
            __DIR__.'/../storage/mods/%s/info.json',
            $alias
        );

        $data = array(
            self::KEY_ALIAS => $alias,
            self::KEY_LABEL => $label,
            self::KEY_VERSION => '1.0'
        );

        return new HairMod(JSONFile::factory($path)->putData($data, true));
    }

    public function countSlots() : int
    {
        return count($this->getArchiveList());
    }

    public function getVersion() : string
    {
        $version = $this->data->getString(self::KEY_VERSION);
        if(!empty($version)) {
            return $version;
        }

        return '1.0';
    }

    public function setVersion(string $version) : self
    {
        $this->data->setKey(self::KEY_VERSION, $version);
        return $this->save();
    }

    public function getAdminEditURL(array $params=array()) : string
    {
        $params['page'] = UserInterface::PAGE_EDIT_MOD;

        return $this->getAdminURL($params);
    }

    public function getAdminBuildURL(array $params=array()) : string
    {
        $params['page'] = UserInterface::PAGE_BUILD_MOD;

        return $this->getAdminURL($params);
    }

    public function getAdminDeleteURL(array $params=array()) : string
    {
        $params['page'] = UserInterface::PAGE_DELETE_MOD;

        return $this->getAdminURL($params);
    }

    public function getAdminURL(array $params=array()) : string
    {
        $params[HairModCollection::REQUEST_PARAM_MOD_ID] = $this->getID();

        return '?'.http_build_query($params);
    }

    /**
     * @return array<int,string> Slot number => archive ID pairs.
     */
    public function getArchiveList() : array
    {
        return $this->data->getArray(self::KEY_ARCHIVES);
    }

    /**
     * @return HairArchive[]
     * @throws CollectionException
     */
    public function getArchives() : array
    {
        $ids = array_values($this->getArchiveList());
        $collection = HairArchiveCollection::factory();
        $result = array();

        foreach($ids as $id) {
            if($collection->idExists($id)) {
                $result[] = $collection->getByID($id);
            }
        }

        return $result;
    }

    public function clearArchives() : self
    {
        $this->data->setKey(self::KEY_ARCHIVES, array());
        return $this->save();
    }

    public function delete() : void
    {
        FileHelper::deleteTree($this->dataFile->getFolderPath());
    }

    public function build() : self
    {
        $this->createBuilder()->build();
        return $this;
    }

    public function createBuilder() : ModBuilder
    {
        return new ModBuilder($this);
    }
}