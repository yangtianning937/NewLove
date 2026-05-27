<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ProductInventory> $productInventories
 */
echo $this->Html->css('/vendor/datatables/dataTables.bootstrap4.min.css', ['block' => true]);
echo $this->Html->script("/vendor/datatables/jquery.dataTables.min.js", ['block' => true]);
echo $this->Html->script("/vendor/datatables/dataTables.bootstrap4.min.js", ['block' => true]);
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
    <div class="productInventories index content">
        <?= $this->Html->link(__('New Product Inventory'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg float-right']) ?>
        <h3><?= __('Product Inventories') ?></h3>
        <div class="table">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th><?= $this->Paginator->sort('product_id') ?></th>
                        <th><?= $this->Paginator->sort('quantity') ?></th>
                        <th class="actions"><?= __('Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productInventories as $productInventory): ?>
                    <tr>
                        <td>
                            <?= $productInventory->has('product') ? $this->Html->link($productInventory->product->name . ' - ' . $productInventory->product->colour->name, ['controller' => 'Products', 'action' => 'view', $productInventory->product->id]) : '' ?>
                        </td>
                        <td><?= $this->Number->format($productInventory->quantity) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $productInventory->product_id]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $productInventory->product_id], ['confirm' => __('Are you sure you want to delete "{0}" ?', $productInventory->product->name)]) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <script>
            $(document).ready(function(){
                $('#dataTable').DataTable();
            })
        </script>
    </div>
<section>
