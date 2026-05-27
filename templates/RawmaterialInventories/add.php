<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RawmaterialInventories $rawmaterialInventories
 */
?>
<head>
    <style>
        select[name="rawmaterial_id"], input[name="quantity"], input[name="lowStockLimit"] {
            font-size: 16px;
            width: 450px;
        }
        h2{
            text-align: center;
        }
    </style>
</head>
<section>
    <div class="row justify-content-center">
        <aside class="column">
            <?= $this->Html->link(__('To Raw Material Inventory'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg']) ?>
        </aside>
        <div class="column-responsive column-80">
            <div class="rawmaterialInventory form content">
                <?= $this->Form->create($rawmaterialInventory) ?>
                <fieldset>
                    <h2><?= __('Add a New Material Inventory') ?></h2>
                    <legend><?= __('Fields Marked * Are Mandatory') ?></legend>
                    <?php
                        echo $this->Form->label('rawmaterial_id', 'Raw Material *');
                        echo $this->Form->control('rawmaterial_id', ['label' => false, 'empty' => true]);

                        echo $this->Form->label('quantity', 'Quantity *');
                        echo $this->Form->control('quantity', ['label' => false]);

                        echo $this->Form->label('lowStockLimit', 'Low Stock Threshold *');
                        echo $this->Form->control('lowStockLimit', ['label' => false]);
                    ?>
                </fieldset>
                <?= $this->Form->button(__('Submit New Material Inventory'), ['class' => 'btn btn-dark btn-lg']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>

<?php
$this->Html->scriptStart(['block' => true]);
?>
$(document).ready(function() {
<?php if ($showConfirm): ?>
    var confirmEdit = confirm("This raw material already exists in the inventory. Do you want to update its quantity?");
    if (confirmEdit) {
    $("form").append('<input type="hidden" name="confirmed" value="1">');
    $("form").submit();
    } else {
    // Clear the form fields
    $("form")[0].reset();
    }
<?php endif; ?>
});
<?php
$this->Html->scriptEnd(['block' => true]);
?>
