<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Collections $collections
 */
$usingFirestore = $usingFirestore ?? false;
?>
<head>
    <style>
        input[name="name"]{
            font-size: 16px;
            width: 450px;
        }
    </style>
</head>
<section>
    <div class="row justify-content-center">
        <aside class="column">
            <?= $this->Html->link(__('To List of Product Collections'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg float-left']) ?>
            <?= $this->Html->link(__('Add New Product Collection'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg float-right']) ?>
        </aside>
        <div class="container text-center">
            <div class="row justify-content-center">
                <h1>Options</h1>
                <div class="row row-cols-auto justify-content-center">
                    <div class="col">
                        <?= $this->Form->postLink(__('Delete This Product Collection'), ['action' => 'delete', $collection->id], ['confirm' => __('Are you sure you want to delete collection: {0} from the system?', $collection->name), 'class' => 'btn btn-dark btn-lg']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="column-responsive column-80">
            <div class="productCollection form content">
                <?= $this->Form->create($usingFirestore ? null : $collection) ?>
                <fieldset>
                    <legend><?= __('Edit Product Collection') ?></legend>
                    <?php
                        echo $this->Form->label('name', 'Name');
                        echo $this->Form->control('name', [
                            'label' => false,
                            'value' => $usingFirestore ? ($collection->name ?? '') : null,
                        ]);
                    ?>
                </fieldset>
                <?= $this->Form->button(__('Update Collection'), ['class' => 'btn btn-dark btn-lg']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>
