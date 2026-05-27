<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MaterialsProduct $materialsProduct
 * @var string[]|\Cake\Collection\CollectionInterface $products
 * @var string[]|\Cake\Collection\CollectionInterface $rawmaterials
 */
?>
<section>
    <div class="row justify-content-center">
        <aside class="column">
            <?= $this->Html->link(__('Back to Assigned Materials'), ['action' => 'index', $materialsProduct->product_id], ['class' => 'btn btn-dark btn-lg float-left']) ?>
        </aside>
        <div class="column-responsive column-80">
            <div class="materialsProducts form content">
                <?= $this->Form->create($materialsProduct) ?>
                <fieldset>
                    <legend><?= __('Edit Materials Product') ?></legend>
                    <?php
                    echo $this->Form->control('product_id', [
                        'options' => $products,
                        'empty' => 'Choose a product',
                        'label' => 'Product Name',
                        'type' => 'select'
                    ]);

                    echo $this->Form->control('rawmaterial_id', [
                        'options' => $rawmaterials,
                        'empty' => 'Choose a raw material',
                        'label' => 'Material Name',
                        'type' => 'select'
                    ]);

                    echo $this->Form->control('quantity');
                    ?>
                </fieldset>
                <?= $this->Form->button(__('Update Assigned Material'), ['class' => 'btn btn-dark btn-lg']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>
