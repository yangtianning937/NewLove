<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Supplier $supplier
 */
?>
<head>
    <style>
        input[name="name"], input[name="email"], input[name="phone_no"], input[name="website"], input[name="location"]{
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
            <?= $this->Html->link(__('To List of Suppliers'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg']) ?>
        </aside>
        <div class="column-responsive column-80">
            <div class="supplier form content">
                <?= $this->Form->create($supplier) ?>
                <fieldset>
                    <h2><?= __('Add a New Supplier') ?></h2>
                    <legend><?= __('Fields Marked * Are Mandatory') ?></legend>
                    <?php
                        echo $this->Form->label('name', 'Name *');
                        echo $this->Form->control('name', ['label' => false]);

                        echo $this->Form->label('email', 'Email Address');
                        echo $this->Form->control('email', ['label' => false]);

                        echo $this->Form->label('phone_no', 'Phone Number');
                        echo $this->Form->control('phone_no', [
                            'type' => 'number',
                            'label' => false
                        ]);

                        echo $this->Form->label('website', 'Wesbite');
                        echo $this->Form->control('website', ['label' => false]);

                        echo $this->Form->label('location', 'Location');
                        echo $this->Form->control('location', ['label' => false]);
                    ?>
                </fieldset>
                <?= $this->Form->button(__('Submit New Supplier'), ['class' => 'btn btn-dark btn-lg']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>
