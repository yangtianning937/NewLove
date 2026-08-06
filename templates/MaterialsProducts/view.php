<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MaterialsProduct $materialsProduct
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Materials Product'), ['action' => 'edit', $materialsProduct->product_id, $materialsProduct->rawmaterial_id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Materials Product'), ['action' => 'delete', $materialsProduct->product_id, $materialsProduct->rawmaterial_id], ['confirm' => __('Are you sure you want to delete this item?'), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Materials Products'), ['action' => 'index', $materialsProduct->product_id], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Materials Product'), ['action' => 'add', $materialsProduct->product_id], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="materialsProducts view content">
            <h3><?= h(($materialsProduct->product->name ?? 'Product') . ' - ' . ($materialsProduct->rawmaterial->name ?? 'Raw Material')) ?></h3>
            <table>
                <tr>
                    <th><?= __('Product') ?></th>
                    <td><?= $materialsProduct->has('product') ? $this->Html->link($materialsProduct->product->name, ['controller' => 'Products', 'action' => 'view', $materialsProduct->product->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Rawmaterial') ?></th>
                    <td><?= $materialsProduct->has('rawmaterial') ? $this->Html->link($materialsProduct->rawmaterial->name, ['controller' => 'Rawmaterials', 'action' => 'view', $materialsProduct->rawmaterial->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Quantity') ?></th>
                    <td><?= $this->Number->format($materialsProduct->quantity) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
