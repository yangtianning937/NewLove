<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RawmaterialInventories $rawmaterialInventories
 */
?>
<head>
    <style>
        select[name="rawmaterial_id"], input[name="quantity"]{
            font-size: 16px;
            width: 450px;
        }
    </style>
</head>
<section>
    <div class="row justify-content-center">
        <aside class="column">
            <?= $this->Html->link(__('To Raw Material Inventory'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg float-left']) ?>
            <?= $this->Html->link(__('Add New Raw Material Inventory'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg float-right']) ?>
        </aside>
        <div class="container text-center">
            <div class="row justify-content-center">
                <h1>Options</h1>
                <div class="row row-cols-auto justify-content-center">
                    <div class="col">
                        <?= $this->Form->postLink(__('Delete This Raw Material Inventory'), ['action' => 'delete', $rawmaterialInventory->id], ['confirm' => __('Are you sure you want to delete raw material inventory: {0} from the system?', $rawmaterialInventory->id), 'class' => 'btn btn-dark btn-lg']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="column-responsive column-80">
            <div class="rawmaterialInventories form content">
                <?= $this->Form->create($rawmaterialInventory) ?>
                <fieldset>
                    <legend><?= __('Edit Raw Material Inventory') ?></legend>
                    <?php
                        echo $this->Form->label('rawmaterial_id', 'Raw Material');
                        echo $this->Form->control('rawmaterial_id', ['label' => false, 'empty' => true]);

                        echo $this->Form->label('quantity', 'Quantity');
                        echo $this->Form->control('quantity', ['label' => false]);

                        echo $this->Form->label('lowStockLimit', 'Low Stock Threshold *');
                        echo $this->Form->control('lowStockLimit', ['label' => false]);
                    ?>
                </fieldset>
                <?= $this->Form->button(__('Update Material Inventory'), ['class' => 'btn btn-dark btn-lg']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>
