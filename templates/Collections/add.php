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
        h2{
            text-align: center;
        }
    </style>
</head>
<section>
    <div class="row justify-content-center">
        <aside class="column">
            <?= $this->Html->link(__('To List of Collections'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg']) ?>
        </aside>
        <div class="column-responsive column-80">
            <div class="productCollection form content">
                <?= $this->Form->create($usingFirestore ? null : $collection) ?>
                <fieldset>
                    <h2><?= __('Add a New Collection') ?></h2>
                    <legend><?= __('Fields Marked * Are Mandatory') ?></legend>
                    <?php
                        echo $this->Form->label('name', 'Name *');
                        echo $this->Form->control('name', [
                            'label' => false,
                            'value' => $usingFirestore ? ($collection->name ?? '') : null,
                        ]);
                    ?>
                </fieldset>
                <?= $this->Form->button(__('Submit New Collection'), ['class' => 'btn btn-dark btn-lg']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>
