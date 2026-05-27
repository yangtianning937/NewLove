<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Product $product
 */
?>
<head>
    <style>
        textarea {
            font-size: 16px;
            width: 800px;
        }
        select[name="collection_id"], select[name="colour_id"], input[name="name"]{
            font-size: 16px;
            width: 450px;
        }
    </style>
</head>
<section>
    <div class="row justify-content-center">
        <aside class="column">
            <?= $this->Html->link(__('To List of Products'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg float-left']) ?>
            <?= $this->Html->link(__('Add New Product'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg float-right']) ?>
        </aside>
        <div class="container text-center">
            <div class="row justify-content-center">
                <h1>Options</h1>
                <div class="row row-cols-auto justify-content-center">
                    <div class="col">
                        <?= $this->Form->postLink(__('Delete This Product'), ['action' => 'delete', $product->id], ['confirm' => __('Are you sure you want to delete product {0} from the system?', $product->name), 'class' => 'btn btn-dark btn-lg']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="column-responsive column-80">
            <div class="products form content">
                <?= $this->Form->create($product, ['type' => 'file']) ?>
                <fieldset>
                    <legend><?= __('Edit Product') ?></legend>
                    <?php
                        echo $this->Form->label('name', 'Name');
                        echo $this->Form->control('name', ['label'=>false]);
    
                        echo $this->Form->label('collection_id', 'Collection');
                        echo $this->Form->control('collection_id', [
                            'options' => $collectionNames, // Use the fetched collection names
                            'empty' => true, // Optional, adds an empty option
                            'label' => false
                        ]);
    
                        echo $this->Form->label('colour_id', 'Colour');
                        echo $this->Form->control('colour_id', [
                            'options' => $colourName, // Use the fetched collection names
                            'empty' => true, // Optional, adds an empty option
                            'label' => false
                        ]);
    
                        echo $this->Form->label('description', 'Product Description');
                        echo $this->Form->control('description', [
                            'type' => 'textarea', 
                            'label' => false,
                            'rows' => 5
                        ]);
    
                    ?>
                    <?php
                        echo $this->Form->control('photo', ['type' => 'file']);
                    ?>
                </fieldset>
                <?= $this->Form->button(__('Update Product'), ['class' => 'btn btn-dark btn-lg']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section
