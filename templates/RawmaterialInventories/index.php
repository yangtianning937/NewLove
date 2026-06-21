<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\RawmaterialInventories> $rawmaterialInventories
 */
$usingFirestore = $usingFirestore ?? false;
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
    <div class="rawmaterialInventories index content">
        <?= $this->Html->link(__('New Raw Material Inventory'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg float-right']) ?>
        <h3><?= __('Raw Material Inventory') ?></h3>
        <div class="table">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>

                        <th><?= $usingFirestore ? h('Raw Material') : $this->Paginator->sort('Raw Material') ?></th>
                        <th><?= $usingFirestore ? h('Quantity') : $this->Paginator->sort('quantity') ?></th>
                        <th><?= $usingFirestore ? h('Low Stock Threshold') : $this->Paginator->sort('lowStockLimit') ?></th>
                        <th class="actions"><?= __('Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rawmaterialInventories as $rawmaterialInventories): ?>
                    <tr>

                        <td>
                            <?= $rawmaterialInventories->has('rawmaterial') ?
                                $this->Html->link($rawmaterialInventories->rawmaterial->name . ' - ' . ($rawmaterialInventories->rawmaterial->colour->name ?? 'No colour'),
                                    ['controller' => 'Rawmaterials', 'action' => 'view', $rawmaterialInventories->rawmaterial->id])
                                : '' ?>
                        </td>                        <td><?= $this->Number->format($rawmaterialInventories->quantity) ?></td>
                        <td><?= $this->Number->format($rawmaterialInventories->lowStockLimit) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $rawmaterialInventories->id]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $rawmaterialInventories->id], ['confirm' => __('Are you sure you want to delete "{0}" ?', $rawmaterialInventories->rawmaterial->name ?? $rawmaterialInventories->id)]) ?>
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
</section>
