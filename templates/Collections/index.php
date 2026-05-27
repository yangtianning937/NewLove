<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Collections> $collections
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
    <div class="productCollection index content">
        <?= $this->Html->link(__('Add New Product Collection'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg float-right']) ?>
        <h3><?= __('Product Collections') ?></h3>
        <div class="table">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>

                        <th><?= h('Collection Name') ?></th>
                        <th class="actions"><?= __('Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($collections as $collections): ?>
                    <tr>

                        <td><?= h($collections->name) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $collections->id]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $collections->id], ['confirm' => __('Are you sure you want to delete collection: {0} from the system?', $collections->name)]) ?>
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
