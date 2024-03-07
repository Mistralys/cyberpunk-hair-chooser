<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use function AppLocalize\t;
use function AppUtils\sb;

$ui = UserInterface::getInstance();
$mod = HairModCollection::factory()->getByRequest();

if($mod === null) {
    $ui->redirectTo($ui->getPageModList()->getAdminURL());
}

$mod->delete();

$ui
    ->addSuccessMessage(t(
        'The mod %1$s has been deleted successfully at %2$s.',
        $mod->getLabel(),
        sb()->time()
    ))
    ->redirectTo($ui->getPageModList()->getAdminURL());
