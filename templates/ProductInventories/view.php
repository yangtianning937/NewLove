<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ProductInventory $productInventory
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Product Inventory'), ['action' => 'edit', $productInventory->product_id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Product Inventory'), ['action' => 'delete', $productInventory->product_id], ['confirm' => __('Are you sure you want to delete # {0}?', $productInventory->product_id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Product Inventories'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Product Inventory'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="productInventories view content">
            <h3><?= h($productInventory->product_id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Product') ?></th>
                    <td><?= $productInventory->has('product') ? $this->Html->link($productInventory->product->name, ['controller' => 'Products', 'action' => 'view', $productInventory->product->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Quantity') ?></th>
                    <td><?= $this->Number->format($productInventory->quantity) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
