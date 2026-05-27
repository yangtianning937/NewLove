<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ProductInventory $productInventory
 * @var \Cake\Collection\CollectionInterface|string[] $products
 */
?>
<head>
    <style>
        select[name="product_id"], input[name="quantity"] {
            font-size: 16px;
            width: 450px;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<section>
    <div class="row justify-content-center">
        <aside class="column">
            <?= $this->Html->link(__('To Product Inventory'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg']) ?>
        </aside>
        <div class="column-responsive column-80">
            <div class="productInventories form content">
                <?= $this->Form->create($productInventory) ?>
                <fieldset>
                    <h2><?= __('Add a New Product Inventory') ?></h2>
                    <legend><?= __('Fields Marked * Are Mandatory') ?></legend>
                    <?php
                    echo $this->Form->label('product_id', 'Product *');
                    echo $this->Form->control('product_id', ['type' => 'select', 'options' => $products, 'empty' => true, 'label' => false]);
                    echo $this->Form->label('quantity', 'Quantity *');
                    echo $this->Form->control('quantity', ['label' => false]);
                    ?>
                </fieldset>
                <?= $this->Form->button(__('Submit New Product Inventory'), ['class' => 'btn btn-dark btn-lg']) ?>
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
    var confirmEdit = confirm("This product already exists in the inventory. Do you want to update its quantity?");
    if (confirmEdit) {
    $("form").append('<input type="hidden" name="confirmed" value="1">');
    $("form").submit();
    } else {
    $("form")[0].reset();
    }
<?php endif; ?>
});
<?php
$this->Html->scriptEnd(['block' => true]);
?>
