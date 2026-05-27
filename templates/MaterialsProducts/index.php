<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\MaterialsProduct> $materialsProducts
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
    <div class="materialsProducts index content">
        <aside class="column">
            <?php if ($productId): ?>
                <?= $this->Html->link(__('Back to Product Details'), ['controller' => 'Products', 'action' => 'view', $productId], ['class' => 'btn btn-dark btn-lg']) ?>
            <?php endif; ?>

        </aside>

        <h3><?= __('Materials Assigned to Product') ?></h3>
        <div class="table">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th><?= h('Product') ?></th>
                        <th><?= h('Material') ?></th>
                        <th><?= h('Quantity Used') ?></th>
                        <th class="actions"><?= __('Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($materialsProducts as $materialsProduct): ?>
                    <tr>
                        <td><?= $materialsProduct->has('product') ? $this->Html->link($materialsProduct->product->name, ['controller' => 'Products', 'action' => 'view', $materialsProduct->product->id]) : '' ?></td>
                        <td><?= $materialsProduct->has('rawmaterial') ? $this->Html->link($materialsProduct->rawmaterial->name, ['controller' => 'Rawmaterials', 'action' => 'view', $materialsProduct->rawmaterial->id]) : '' ?></td>
                        <td><?= $this->Number->format($materialsProduct->quantity) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $materialsProduct->product_id, $materialsProduct->rawmaterial_id]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $materialsProduct->product_id, $materialsProduct->rawmaterial_id], ['confirm' => __('Are you sure you want to remove this material from the product?')]) ?>
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
