<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\ConvertHelper;
use AppUtils\FileHelper\FileInfo;
use AppUtils\JSHelper;
use DirectoryIterator;
use function AppLocalize\pt;
use function AppLocalize\pts;
use function AppLocalize\t;
use function AppUtils\sb;

$ui = UserInterface::getInstance();
$activePage= $ui->getActivePage();
$d = new DirectoryIterator(MODS_DOWNLOAD_FOLDER);
$collection = DownloadedFileCollection::getInstance();

if(isset($_REQUEST['action']) && $_REQUEST['action'] === 'extract')
{
    $selected = $_REQUEST['selected'] ?? array();

    $collection->clearSelected();

    foreach($selected as $fileName)
    {
        $collection->setFileSelected((string)$fileName);
    }

    $collection->saveSelected();

    $ui->addSuccessMessage(t('The selection has been saved successfully at %1$s.', sb()->time()));
    $ui->redirectTo($activePage->getAdminURL());
}

?>
<p>
    <?php
    pts('Choose the mod ZIP files you want to use from this list.');
    pts('Remember to save the selection.');
    ?>
</p>
<form method="post">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="small"></th>
                <th><?php pt('File'); ?></th>
                <th class="align-right"><?php pt('Size'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($d as $item)
            {
                if($item->isDot()) {
                    continue;
                }

                $file = FileInfo::factory($item);
                $name = $file->getName();
                $id = JSHelper::nextElementID();

                ?>
                <tr>
                    <td class="small"><input id="<?php echo $id ?>" type="checkbox" name="selected[]" value="<?php echo $name ?>" <?php if($collection->isFileSelected($name)) { echo 'checked'; } ?>></td>
                    <td><label for="<?php echo $id ?>"><?php echo $name; ?></label></td>
                    <td class="align-right"><?php echo ConvertHelper::bytes2readable($file->getSize()); ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <p>
        <input type="hidden" name="page" value="<?php echo $activePage->getID() ?>">
        <button class="btn btn-primary" type="submit" name="action" value="extract">
            <?php echo Icons::SAVE ?>
            <?php pt('Save selection'); ?>
        </button>
    </p>
</form>