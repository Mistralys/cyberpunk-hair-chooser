<?php
/**
 * @see \Mistralys\CPHairChooser\HairArchive::getImageURL()
 */

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\ImageHelper;

$noPreview = __DIR__.'/../img/no-preview.jpg';
$targetFile = $noPreview;

$archive = HairArchiveCollection::factory()->getByRequest();
$number = (int)($_REQUEST[HairArchiveCollection::REQUEST_VAR_HAIR_SLOT] ?? null);

if($archive !== null && $number > 0) {
    $image = $archive->getImageFile($number);
    if($image !== null) {
        $targetFile = $image->getPath();
    }
}

ImageHelper::displayImage($targetFile);
exit;
