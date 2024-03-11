<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\JSHelper;
use function AppLocalize\pt;
use function AppUtils\sb;

$collection = HairArchiveCollection::factory();
$archives = $collection->getAll();
$ui = UserInterface::getInstance();

usort($archives, static function(HairArchive $a, HairArchive $b) : int {
    return strnatcasecmp($a->getPrettyLabel(), $b->getPrettyLabel());
});

if(isset($_REQUEST['save']) && $_REQUEST['save'] === 'yes')
{
    foreach($archives as $archive)
    {
        $id = $archive->getID();
        $archive->setNumbers(trim((string)$_REQUEST[$id]['number']));
        $archive->setPrettyLabel(htmlspecialchars(trim((string)$_REQUEST[$id]['label'])));
    }

    $ui->addSuccessMessage(
        sprintf(
            'The slot numbers have been saved successfully at %1$s.',
            sb()->time()
        ))
        ->redirectTo($ui->getActivePage()->getAdminURL());
}

?>
<p>
    <?php echo sb()
        ->noteBold()
        ->t('Archives can contain several hair slots.')
        ->t('In this case, specify the slot numbers separated with dashes (-).')
        ->t(
            'If an archive file contains a number range, e.g. %1$s, it is automatically converted to a list of numbers.',
            sb()->code('no1-5')
        )
    ?>
</p>
<form method="post">
    <table class="table table-hover">
        <thead>
        <tr>
            <th class="small nowrap align-right"><?php pt('Slot number') ?></th>
            <th class="small"><?php pt('Hairstyle display name') ?></th>
            <th>
                <?php pt('Source file'); ?>
                <small class="xs">(<?php pt('Mod file in tooltip'); ?>)</small>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php

        foreach($archives as $archive)
        {
            $id = JSHelper::nextElementID();
            ?>
            <tr>
                <td class="small align-right">
                    <input  type="text"
                            name="<?php echo $archive->getID() ?>[number]"
                            id="<?php echo $id ?>"
                            value="<?php echo $archive->getNumbersAsString() ?>"
                            style="width: 4rem"
                    />
                </td>
                <td class="small">
                    <input type="text"
                           name="<?php echo $archive->getID() ?>[label]"
                           value="<?php echo htmlspecialchars($archive->getPrettyLabel()) ?>"
                           style="width: 20rem"
                </td>
                <td>
                    <small class="s monospace" title="<?php echo $archive->getDownload()->getName() ?>">
                        <?php echo $archive->getName() ?>
                    </small>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <hr>
    <p>
        <input type="hidden" name="save" value="yes">
        <button type="submit" class="btn btn-primary">
            <?php echo Icons::SAVE ?>
            <?php pt('Save settings') ?>
        </button>
    </p>
</form>
