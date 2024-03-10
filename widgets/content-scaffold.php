<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use function AppUtils\parseThrowable;

$activePage = UserInterface::getInstance()->getActivePage();

try
{
    $rendered = $activePage->render();
}
catch(\Throwable $e)
{
    $rendered = '<pre>'.parseThrowable($e)->toString().'</pre>';
}

include __DIR__.'/navigation.php';
include __DIR__.'/ui-messages.php';

$title = $activePage->getTitle();
$abstract = $activePage->getAbstract();

if(!empty($title) || !empty($abstract)) {
    ?>
        <div class="page-header">
            <?php
            if(!empty($title)) {
                ?>
                <h1 class="page-title <?php if(!empty($abstract)) { echo 'with-abstract'; } ?>">
                    <?php echo $title?>
                </h1>
                <?php
            }

            if(!empty($abstract)) {
                ?>
                <p class="page-abstract">
                    <?php echo $abstract?>
                </p>
                <?php
            }
            ?>
        </div>
    <?php
}

echo $rendered;
