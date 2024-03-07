<?php

declare(strict_types=1);

namespace Mistralys\CPHairChooser;

use AppUtils\JSHelper;
use function AppUtils\sb;

$collection = HairArchiveCollection::factory();
$archives = $collection->getAll();
$ui = UserInterface::getInstance();

if(isset($_REQUEST['save']) && $_REQUEST['save'] === 'yes')
{
    foreach($archives as $archive)
    {
        $id = $archive->getID();
        $archive->setNumber((int)$_REQUEST[$id]['number']);
        $archive->setPrettyLabel((string)$_REQUEST[$id]['label']);
    }

    $ui->addSuccessMessage(
        sprintf(
            'The numbers have been saved successfully at %1$s.',
            sb()->time()
        ))
        ->redirectTo($ui->getActivePage()->getAdminURL());
}

?>
<form method="post">
    <table class="table table-hover">
        <thead>
        <tr>
            <th style="text-align: right">Name</th>
            <th>Number</th>
            <th>Pretty name</th>
        </tr>
        </thead>
        <tbody>
        <?php

        foreach($archives as $archive)
        {
            $id = JSHelper::nextElementID();
            ?>
            <tr>
                <td style="text-align: right;white-space: nowrap;width:1%;vertical-align: top">
                    <label for="<?php echo $id ?>"><?php echo $archive->getLabel(); ?></label><br>
                    <small><small><code><?php echo $archive->getName() ?></code></small></small>
                </td>
                <td style="vertical-align: top;width: 1%">
                    <input  type="number"
                            name="<?php echo $archive->getID() ?>[number]"
                            id="<?php echo $id ?>"
                            value="<?php echo $archive->getNumber() ?>"
                            style="width: 4rem"
                    />
                </td>
                <td>
                    <input type="text"
                           name="<?php echo $archive->getID() ?>[label]"
                           value="<?php echo htmlspecialchars($archive->getPrettyLabel()) ?>"
                           style="width: 30rem"
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <hr>
    <p>
        <input type="hidden" name="save" value="yes">
        <button type="submit" class="btn btn-primary">Save settings</button>
    </p>
</form>