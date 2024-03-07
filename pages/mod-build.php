<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use function AppLocalize\pt;
use function AppLocalize\t;
use function AppUtils\sb;

$ui = UserInterface::getInstance();
$mod = HairModCollection::factory()->getByRequest();
$activePage = $ui->getActivePage();

if($mod === null) {
    $ui->redirectTo($ui->getPageModList()->getAdminURL());
}

$activePage->setTitle($mod->getLabel());
$builder = $mod->createBuilder();

if(isset($_REQUEST['build']) && $_REQUEST['build'] === 'yes') {
    $builder->build();
    $ui->addSuccessMessage(t('The mod has been built successfully at %1$s.', sb()->time()));
    $ui->redirectTo($mod->getAdminBuildURL());
}

?>
<p>
    <?php pt('The mod will be saved to the following file:'); ?>
</p>
<pre><?php echo $builder->getTargetFile()->getPath() ?></pre>
<p>
    <a class="btn btn-primary" href="<?php echo $mod->getAdminBuildURL(array('build' => 'yes')) ?>">
        <i class="fa-solid fa-bolt"></i>
        <?php echo t('Build now'); ?>
    </a>
</p>