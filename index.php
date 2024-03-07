<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\OutputBuffering;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/paths.php';

session_start();

$ui = UserInterface::getInstance();
$activePage = $ui->getActivePage();

// Render the page before the HTML scaffold, to allow for adjustments
// like the page title or abstract being changed.
OutputBuffering::start();
include __DIR__ . '/widgets/content-scaffold.php';
$content = OutputBuffering::get();

$title = $activePage->getTitle();
if(empty($title)) {
    $title = $activePage->getNavLabel();
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title ?> - <?php echo $ui->getAppName() ?></title>
        <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <link href="vendor/fortawesome/font-awesome/css/all.min.css" rel="stylesheet">
        <link href="css/main.css" rel="stylesheet">
    </head>
    <body style="padding: 4rem">
        <?php echo $content; ?>
    </body>
</html>

