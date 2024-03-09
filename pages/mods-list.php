<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\ConvertHelper;
use function AppLocalize\pt;

$collection = HairModCollection::factory();

?>
<table class="table table-hover">
    <thead>
        <tr>
            <th><?php pt('Name') ?></th>
            <th class="align-right"><?php pt('Hair slots') ?></th>
            <th class="align-right"><?php pt('Version') ?></th>
            <th><?php pt('Last modified') ?></th>
            <th></th>
        </tr>
        </thead>
    <tbody>
    <?php
    foreach($collection->getAll() as $mod)
    {
        ?>
        <tr>
            <td>
                <?php echo $mod->getLabel(); ?>
            </td>
            <td class="align-right">
                <?php echo $mod->countSlots() ?>
            </td>
            <td class="align-right">
                <?php echo $mod->getVersion() ?>
            </td>
            <td>
                <?php echo ConvertHelper::date2listLabel($mod->getLastModified(), true, true) ?>
            </td>
            <td>
                <a href="<?php echo $mod->getAdminEditURL(); ?>"><i class="fa-solid fa-screwdriver-wrench"></i> <?php pt('Settings') ?></a>
                |
                <a href="<?php echo $mod->getAdminBuildURL(); ?>"><i class="fa-solid fa-bolt"></i> <?php pt('Build') ?></a>
                |
                <a href="<?php echo $mod->getAdminDeleteURL(); ?>"><i class="fa-solid fa-circle-xmark"></i> <?php pt('Delete') ?></a>

            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
