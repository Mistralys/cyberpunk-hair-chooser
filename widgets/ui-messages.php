<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

$ui = UserInterface::getInstance();
$messages = $ui->getMessages();

if(!empty($messages))
{
    foreach($messages as $message)
    {
        ?>
        <div class="alert alert-<?php echo $message['type'] ?> alert-dismissible fade show" role="alert">
            <?php echo $message['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
    }

    $ui->clearMessages();
}
