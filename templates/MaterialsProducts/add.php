<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MaterialsProduct $materialsProduct
 * @var \Cake\Collection\CollectionInterface|string[] $products
 * @var \Cake\Collection\CollectionInterface|string[] $rawmaterials
 */
$usingFirestore = $usingFirestore ?? false;
?>
<head>
    <style>
        select[name="product_id"], select[name="rawmaterial_id"], input[name="quantity"]{
            font-size: 16px;
            width: 450px;
        }
        h2{
            text-align: center;
        }
    </style>
</head>
<div class="row justify-content-center">
    <aside class="column">
        <?php if ($materialsProduct->product_id): ?>
            <?= $this->Html->link(__('Back to Product Details'), ['controller' => 'Products', 'action' => 'view', $materialsProduct->product_id], ['class' => 'btn btn-dark btn-lg']) ?>
        <?php endif; ?>
    </aside>
    <div class="column-responsive column-80">
        <div class="materialsProducts form content">
            <?= $this->Form->create($usingFirestore ? null : $materialsProduct) ?>
            <fieldset>
                <h2><?= __('Add a New Material to a Product') ?></h2>
                <legend><?= __('Fields Marked * Are Mandatory') ?></legend>
                <?php

                echo $this->Form->label('product_id', 'Product *');
                echo $this->Form->control('product_id', [
                    'options' => $products,
                    'empty' => 'Choose a product',
                    'label' => false,
                    'type' => 'select',
                    'value' => $usingFirestore ? ($materialsProduct->product_id ?? '') : null,
                ]);

                echo $this->Form->label('rawmaterial_id', 'Material *');
                echo $this->Form->control('rawmaterial_id', [
                    'options' => $rawmaterials,
                    'empty' => 'Choose a raw material',
                    'label' => false,
                    'type' => 'select',
                    'value' => $usingFirestore ? ($materialsProduct->rawmaterial_id ?? '') : null,
                ]);

                echo $this->Form->label('quantity', 'Quantity *');
                echo $this->Form->control('quantity', [
                    'label' => false,
                    'value' => $usingFirestore ? ($materialsProduct->quantity ?? '') : null,
                ]);
                ?>
            </fieldset>

            <?= $this->Form->button(__('Add Material to Product'), ['class' => 'btn btn-dark btn-lg']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
