<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\ImageHelper;

$noPreview = __DIR__.'/../img/no-preview.jpg';
$targetFile = $noPreview;

$archive = HairArchiveCollection::factory()->getByRequest();

if($archive !== null) {
    $image = $archive->getImageFile();
    if($image !== null) {
        $targetFile = $image->getPath();
    }
}

ImageHelper::displayImage($targetFile);
exit;
