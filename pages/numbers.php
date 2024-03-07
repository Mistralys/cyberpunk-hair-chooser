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
        $archive->setNumber((int)$_REQUEST[$id]['number']);
        $archive->setPrettyLabel((string)$_REQUEST[$id]['label']);
    }

    $ui->addSuccessMessage(
        sprintf(
            'The numbers have been saved successfully at %1$s.',
            sb()->time()
        ))
        ->redirectTo($ui->getActivePage()->getAdminURL());
}

?>
<form method="post">
    <table class="table table-hover">
        <thead>
        <tr>
            <th class="small"><?php pt('Number') ?></th>
            <th class="small"><?php pt('Name') ?></th>
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
                <td class="small">
                    <input  type="number"
                            name="<?php echo $archive->getID() ?>[number]"
                            id="<?php echo $id ?>"
                            value="<?php echo $archive->getNumber() ?>"
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