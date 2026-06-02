<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Supplier $supplier
 */
$usingFirestore = $usingFirestore ?? false;
?>
<head>
    <style>
        input[name="name"], input[name="email"], input[name="phone_no"], input[name="website"], input[name="location"]{
            font-size: 16px;
            width: 450px;
        }
    </style>
</head>
<section>
    <div class="row justify-content-center">
        <aside class="column">
            <?= $this->Html->link(__('To List of Suppliers'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg float-left']) ?>
            <?= $this->Html->link(__('Add New Supplier'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg float-right']) ?>
        </aside> 
        <div class="row row-cols-auto justify-content-center">
            <div class="col">
                <?= $this->Form->postLink(__('Delete This Supplier'), ['action' => 'delete', $supplier->id], ['confirm' => __('Are you sure you want to delete supplier {0} from the system?', $supplier->name), 'class' => 'btn btn-dark btn-lg']) ?>
            </div>
        </div>
        <div class="column-responsive column-60">
            <div class="supplier form content">
                <?= $this->Form->create($usingFirestore ? null : $supplier) ?>
                <fieldset>
                    <legend><?= __('Edit Supplier Details') ?></legend>
                    <?php
                        echo $this->Form->label('name', 'Name');
                        echo $this->Form->control('name', [
                            'label' => false,
                            'value' => $usingFirestore ? ($supplier->name ?? '') : null,
                        ]);

                        echo $this->Form->label('email', 'Email Address');
                        echo $this->Form->control('email', [
                            'label' => false,
                            'value' => $usingFirestore ? ($supplier->email ?? '') : null,
                        ]);

                        echo $this->Form->label('phone_no', 'Phone Number');
                        echo $this->Form->control('phone_no', [
                            'type' => 'number',
                            'label' => false,
                            'value' => $usingFirestore ? ($supplier->phone_no ?? '') : null,
                        ]);

                        echo $this->Form->label('website', 'Wesbite');
                        echo $this->Form->control('website', [
                            'label' => false,
                            'value' => $usingFirestore ? ($supplier->website ?? '') : null,
                        ]);

                        echo $this->Form->label('location', 'Location');
                        echo $this->Form->control('location', [
                            'label' => false,
                            'value' => $usingFirestore ? ($supplier->location ?? '') : null,
                        ]);
                    ?>
                </fieldset>
                <?= $this->Form->button(__('Update Supplier'), ['class' => 'btn btn-dark btn-lg']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>
