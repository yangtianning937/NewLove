<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rawmaterial $rawmaterial
 */
?>
<head>
    <style>
        textarea {
            font-size: 16px;
            width: 800px;
        }
        select[name="delivery_time_unit"], select[name="colour_id"], input[name="name"], input[name="delivery_time_unit"], input[name="delivery_time_value"], input[name="cost_price"], select[name="supplier_id"]{
            font-size: 16px;
            width: 450px;
        }
    </style>
</head>
<section>
    <div class="row justify-content-center">
        <aside class="column">
            <?= $this->Html->link(__('To List of Materials'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg float-left']) ?>
            <?= $this->Html->link(__('Add New Material'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg float-right']) ?>
        </aside>
        <div class="container text-center">
            <div class="row justify-content-center">
                <h1>Options</h1>
                <div class="row row-cols-auto justify-content-center">
                    <div class="col">
                        <?= $this->Form->postLink(__('Delete This Material'), ['action' => 'delete', $rawmaterial->id], ['confirm' => __('Are you sure you want to delete material {0} from the system?', $rawmaterial->name), 'class' => 'btn btn-dark btn-lg']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="column-responsive column-80">
            <div class="rawmaterial form content">
                <?= $this->Form->create($rawmaterial, ['type' => 'file']) ?>
                <fieldset>
                    <legend><?= __('Add New Material') ?></legend>
                    <?php
                    echo $this->Form->label('name', 'Name');
                    echo $this->Form->control('name', ['label' => false]);

                    // Delivery time selector
                    echo $this->Form->label('delivery_time_unit', 'Delivery Time Unit');
                    echo $this->Form->control('delivery_time_unit', [
                        'type' => 'select',
                        'options' => ['days' => 'Days', 'weeks' => 'Weeks'],
                        'label' => false
                    ]);
                    // Delivery time quantity input box
                    echo $this->Form->label('delivery_time_value', 'Delivery Time Quantity');
                    echo $this->Form->control('delivery_time_value', [
                        'type' => 'number',
                        'label' => false
                    ]);

                    echo $this->Form->label('cost_price', 'Price per Unit');
                    echo $this->Form->control('cost_price', ['label' => false]);

                    echo $this->Form->label('supplier_id', 'Supplier');
                    echo $this->Form->control('supplier_id', [
                        'options' => $supplierName,
                        'empty' => true,
                        'label' => false
                    ]);

                    echo $this->Form->label('colour_id', 'Colour');
                    echo $this->Form->control('colour_id', [
                        'options' => $colourName,
                        'empty' => true,
                        'label' => false
                    ]);

                    echo $this->Form->label('description', 'Material Description');
                    echo $this->Form->control('description', [
                        'type' => 'textarea',
                        'rows' => 5,
                        'label' => false
                    ]);
                    
                    echo $this->Form->control('photo', ['type' => 'file', 'label' => 'Material Image']);
                    ?>
                </fieldset>
                <?= $this->Form->button(__('Update Material'), ['class' => 'btn btn-dark btn-lg']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>
