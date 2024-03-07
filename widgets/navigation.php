<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

$ui = UserInterface::getInstance();
$activePageID = $ui->getActivePageID();

?>
<ul class="nav nav-pills" style="margin-bottom: 3rem">
    <?php
    foreach ($ui->getPages() as $page)
    {
        if(!$page->isInNav()) {
            continue;
        }

        $active = '';
        if($activePageID === $page->getID()) {
            $active = 'active';
        }

        ?>
        <li class="nav-item">
            <a href="<?php echo $page->getAdminURL() ?>" class="nav-link <?php echo $active ?>">
                <?php echo $page->getNavLabel() ?>
            </a>
        </li>
        <?php
    }
    ?>
</ul>