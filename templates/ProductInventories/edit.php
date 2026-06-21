<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ProductInventory $productInventory
 * @var string[]|\Cake\Collection\CollectionInterface $products
 */
$usingFirestore = $usingFirestore ?? false;
?>
<head>
    <style>
        input[name="quantity"]{
            font-size: 16px;
            width: 450px;
        }
    </style>
</head>
<section>
    <div class="row justify-content-center">
        <aside class="column">
            <?= $this->Html->link(__('To Product Inventory'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg float-left']) ?>
            <?= $this->Html->link(__('Add New Product Inventory'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg float-right']) ?>
        </aside>
        <div class="container text-center">
            <div class="row justify-content-center">
                <h1>Options</h1>
                <div class="row row-cols-auto justify-content-center">
                    <div class="col">
                        <?= $this->Form->postLink(__('Delete This Product Inventory'), ['action' => 'delete', $productInventory->product_id], ['confirm' => __('Are you sure you want to delete product inventory: {0} from the system?', $productInventory->product_id), 'class' => 'btn btn-dark btn-lg']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="column-responsive column-80">
            <div class="productInventories form content">
                <?= $this->Form->create($usingFirestore ? null : $productInventory) ?>
                <fieldset>
                    <legend><?= __('Edit Product Inventory') ?></legend>
                    <?php
                        echo $this->Form->label('quantity', 'Quantity');
                        echo $this->Form->control('quantity', [
                            'label' => false,
                            'value' => $usingFirestore ? ($productInventory->quantity ?? '') : null,
                        ]);
                    ?>
                </fieldset>
                <?= $this->Form->button(__('Update Product Inventory'), ['class' => 'btn btn-dark btn-lg']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>
