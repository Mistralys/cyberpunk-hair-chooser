<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\ConvertHelper;
use function AppLocalize\pt;
use function AppLocalize\t;

$ui = UserInterface::getInstance();
$activePage = $ui->getActivePage();
$collection = DownloadedFileCollection::getInstance();

if(isset($_REQUEST['extract']) && $_REQUEST['extract'] === 'yes')
{
    $selected = $collection->getAll();

    foreach($selected as $downloadedFile)
    {
        if(!$downloadedFile->isExtracted()) {
            $downloadedFile->extract();
        }
    }

    $ui->addSuccessMessage(t('All non-extracted files have been extracted.'));
    $ui->redirectTo($activePage->getAdminURL());
}

?>
<p>
    <?php pt('Files are extracted to folder:') ?>
</p>
<pre><?php echo MODS_EXTRACT_FOLDER ?></pre>
<table class="table table-hover">
    <thead>
        <tr>
            <th class="align-center"><?php pt('Extracted?') ?></th>
            <th><?php pt('File'); ?></th>
            <th class="align-right"><?php pt('Size'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
    $selected = $collection->getAll();

    foreach($selected as $downloadedFile)
    {
        ?>
        <tr>
            <td class="align-center small">
                <?php
                if($downloadedFile->isExtracted()) {
                    echo 'Yes';
                } else {
                    echo 'No';
                }
                ?>
            </td>
            <td><?php echo $downloadedFile->getName(); ?></label></td>
            <td class="align-right"><?php echo ConvertHelper::bytes2readable($downloadedFile->getSize()); ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
<p>
    <a type="submit" class="btn btn-primary" href="<?php echo $activePage->getAdminURL(array('extract' => 'yes')) ?>">
        <?php echo Icons::EXTRACT ?>
        <?php pt('Extract all non-extracted') ?>
    </a>
</p>