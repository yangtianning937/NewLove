<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Colours $colours
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
            <?= $this->Html->link(__('To List of Colours'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg float-left']) ?>
            <?= $this->Html->link(__('Add New Colour'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg float-right']) ?>
        </aside>
        <div class="container text-center">
            <div class="row justify-content-center">
                <h1>Options</h1>
                <div class="row row-cols-auto justify-content-center">
                    <div class="col">
                        <?= $this->Form->postLink(__('Delete This Colour'), ['action' => 'delete', $colour->id], ['confirm' => __('Are you sure you want to delete colour: {0} from the system?', $colour->name), 'class' => 'btn btn-dark btn-lg']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="column-responsive column-80">
            <div class="productColour form content">
                <?= $this->Form->create($usingFirestore ? null : $colour) ?>
                <fieldset>
                    <legend><?= __('Edit Colour') ?></legend>
                    <?php
                         echo $this->Form->label('name', 'Name');
                         echo $this->Form->control('name', [
                             'label' => false,
                             'value' => $usingFirestore ? ($colour->name ?? '') : null,
                         ]);
                    ?>
                </fieldset>
                <?= $this->Form->button(__('Update Colour'), ['class' => 'btn btn-dark btn-lg']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>
