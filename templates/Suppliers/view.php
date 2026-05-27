<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Supplier $supplier
 */
?>
<head>
    <style>
        table td, table th  
        {    
        max-width: 150px;
        word-wrap: break-word;
        }
    </style>
</head>
<section>
    <div class="container text-center">
        <aside class="column">
            <?= $this->Html->link(__('To List of Suppliers'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg float-left']) ?>
            <?= $this->Html->link(__('Add New Supplier'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg float-right']) ?>
        </aside>
        <div class="row justify-content-center">
            <h1>Options</h1>
            <div class="row row-cols-auto justify-content-center">
                <div class="col">
                    <?= $this->Html->link(__('Edit This Supplier'), ['action' => 'edit', $supplier->id], ['class' => 'btn btn-dark btn-lg']) ?>
                </div>
                <div class="col">
                    <?= $this->Form->postLink(__('Delete This Supplier'), ['action' => 'delete', $supplier->id], ['confirm' => __('Are you sure you want to delete supplier {0} from the system?', $supplier->name), 'class' => 'btn btn-dark btn-lg']) ?>
                </div>
            </div>
            <div class="column-responsive column-60">
                <div class="supplier view content">
                    <h2><?= h($supplier->name) ?></h2>
                    <table>
                        <tr>
                            <th><?= __('SupplierID') ?></th>
                            <td><?= h($supplier->id) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Supplier Email') ?></th>
                            <td><?= h($supplier->email) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Supplier Phone Number') ?></th>
                            <td><?= h($supplier->phone_no) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Supplier Website') ?></th>
                            <td><?= h($supplier->website) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Supplier Location') ?></th>
                            <td><?= h($supplier->location )?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
