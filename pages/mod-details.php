<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\JSHelper;
use function AppLocalize\pt;
use function AppLocalize\t;
use function AppUtils\sb;

$ui = UserInterface::getInstance();
$collection = HairArchiveCollection::factory();
$activeMod = HairModCollection::factory()->getByRequest();
$activePage = $ui->getActivePage();

if(isset($_REQUEST['save']) && $_REQUEST['save'] === 'yes')
{
    $modName = htmlspecialchars($_REQUEST['label'] ?? '');

    if(empty($modName)) {
        throw new HairChooserException('Please enter a mod name.');
    }

    if(!isset($_REQUEST['archives']) || !is_array($_REQUEST['archives'])) {
        throw new HairChooserException('Please select at least one hair.');
    }

    if($activeMod instanceof HairMod) {
        $mod = $activeMod;
    } else {
        $mod = HairMod::createNew($modName);
    }

    $mod->clearArchives();
    $mod->setVersion(htmlspecialchars(trim((string)$_REQUEST['version'])));
    $mod->setLabel(htmlspecialchars(trim((string)$_REQUEST['label'])));

    foreach($_REQUEST['archives'] as $archiveID)
    {
        if(empty($archiveID)) {
            continue;
        }

        if(!$collection->idExists($archiveID)) {
            throw new HairChooserException('Invalid hair ID: '.$archiveID);
        }

        $mod->addArchive($collection->getByID($archiveID));

    }

    $ui->addSuccessMessage(
        t(
            'The mod %s has been added successfully at %s.',
            $mod->getLabel(),
            sb()->time()
        ))
        ->redirectTo($ui->getPageModList()->getAdminURL());
}

$defaultData = array(
    'label' => '',
    'version' => '1.0',
    'archives' => array()
);

if($activeMod instanceof HairMod)
{
    $defaultData['label'] = $activeMod->getLabel();
    $defaultData['version'] = $activeMod->getVersion();
    $defaultData['archives'] = $activeMod->getArchiveList();

    $activePage->setTitle($activeMod->getLabel());
}

?>
<form method="post">
    <h3><?php pt('Mod settings')  ?></h3>
    <div class="mb-3">
        <label for="elModName" class="form-label"><?php pt('Mod name') ?></label>
        <input type="text" name="label" class="form-control" id="elModName" value="<?php echo htmlspecialchars($defaultData['label']) ?>">
        <div class="form-text">
            <?php echo sb()
                ->t('The name of the mod to generate.')
                ->t('Used as file name for the ZIP archive.')
                ->t('Make sure not to use any system-reserved special characters.')
                ->nl()
                ->noteBold()
                ->t('Using the same name overwrites the existing mod.')
            ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="elModVersion" class="form-label"><?php pt('Version') ?></label>
        <input type="text" name="version" class="form-control" id="elModVersion" value="<?php echo htmlspecialchars($defaultData['version']) ?>">
        <div class="form-text">
            <?php echo sb()
                ->t('This gets added to the built file name to track changes.')
                ->noteBold()
                ->t('It must be updated manually.')
            ?>
        </div>
    </div>

    <h3><?php pt('Hair slots') ?></h3>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Number</th>
            <th>Hair</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $grouped = $collection->getGroupedByNumber();

        foreach($grouped as $number => $archives)
        {
            $id = JSHelper::nextElementID();

            ?>
            <tr>
                <td style="text-align: right;width: 1%">
                    <label for="<?php echo $id ?>"><?php echo $number ?></label>
                </td>
                <td>
                    <select name="archives[<?php echo $number ?>]" id="<?php echo $id ?>">
                        <option value="">[No hair]</option>
                        <?php
                        $value = '';
                        if(!empty($defaultData['archives'][$number])) {
                            $value = $defaultData['archives'][$number];
                        }

                        foreach($archives as $archive)
                        {
                            $selected = '';
                            if($value === $archive->getID()) {
                                $selected = 'selected';
                            }

                            ?>
                            <option value="<?php echo $archive->getID() ?>" <?php echo $selected ?>>
                                <?php echo htmlspecialchars($archive->getPrettyLabel($number)) ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                    <div class="previews">
                    <?php
                    foreach($archives as $archive)
                    {
                        ?><img  src="<?php echo $archive->getImageURL($number) ?>"
                                title="<?php echo $archive->getPrettyLabel($number) ?>"
                                alt=""
                                class="thumbnail clickable"
                                onclick="document.getElementById('<?php echo $id ?>').value = '<?php echo $archive->getID() ?>'"
                        ><?php
                    }
                    ?>
                    </div>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <hr>
    <p>
        <input type="hidden" name="page" value="<?php echo UserInterface::PAGE_ADD_MOD ?>">
        <input type="hidden" name="save" value="yes">
        <button type="submit" class="btn btn-primary">
            <?php echo Icons::SAVE ?>
            <?php pt('Save settings') ?>
        </button>
    </p>
</form>