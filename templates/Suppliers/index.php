<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Supplier> $supplier
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
    <div class="supplier index content">
        <?= $this->Html->link(__('Add New Supplier'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg float-right']) ?>
        <h3><?= __('Suppliers') ?></h3>
        <div class="table">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0" style="font-size: 14px;">
                <thead>
                    <tr>
                        <th><?= h('Supplier Name') ?></th>
                        <th><?= h('Supplier ID') ?></th>
                        <th><?= h('Supplier Email') ?></th>
                        <th><?= h('Supplier Phone Number') ?></th>
                        <th><?= h('Supplier Website') ?></th>
                        <th><?= h('Supplier Location') ?></th>
                        <th class="actions"><?= __('Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suppliers as $suppliers): ?>
                    <tr>
                        <td><?= h($suppliers->name) ?></td>
                        <td><?= $this->Number->format($suppliers->id) ?></td>
                        <td><a href="<?= h($suppliers->email) ?>" target="_blank"><?= h($suppliers->email) ?></a></td>
                        <td><?= h($suppliers->phone_no) ?></td>
                        <td><a href="<?= h($suppliers->website) ?>" target="_blank"><?= h($suppliers->website) ?></a></td>
                        <td><?= h($suppliers->location) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $suppliers->id]) ?>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $suppliers->id]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $suppliers->id], ['confirm' => __('Are you sure you want to delete supplier {0} from the system?', $suppliers->name)]) ?>
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
