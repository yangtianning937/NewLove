<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Products $products
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
        h2{
            text-align: center;
        }
    </style>
</head>
<section>
    <div class="row justify-content-center">
        <aside class="column">
            <?= $this->Html->link(__('To List of Products'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg']) ?>
        </aside>
        <div class="column-responsive column-80">
            <div class="product form content">
                <?= $this->Form->create($product, ['type' => 'file']) ?>
                <fieldset>
                    <h2><?= __('Add a New Product') ?></h2>
                    <legend><?= __('Fields Marked * Are Mandatory') ?></legend>
                    <?php
                    echo $this->Form->label('name', 'Name *');
                    echo $this->Form->control('name', ['label'=>false]);

                    echo $this->Form->label('collection_id', 'Collection');
                    echo $this->Form->control('collection_id', [
                        'options' => $collectionNames, // Use the fetched collection names
                        'empty' => true, // Optional, adds an empty option
                        'label' => false
                    ]);

                    echo $this->Form->label('colour_id', 'Colour *');
                    echo $this->Form->control('colour_id', [
                        'options' => $colourName, // Use the fetched collection names
                        'empty' => true, // Optional, adds an empty option
                        'label' => false
                    ]);

                    echo $this->Form->label('description', 'Product Description');
                    echo $this->Form->control('description', [
                        'type' => 'textarea', // Use the fetched collection names
                        'label' => false,
                        'rows' => 5
                    ]);

                    ?>
                    <?php
                    echo $this->Form->control('photo', ['type' => 'file', 'label' => 'Photo *']);
                    ?>
                </fieldset>
                <?= $this->Form->button(__('Submit New Product'), ['class' => 'btn btn-dark btn-lg']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>
