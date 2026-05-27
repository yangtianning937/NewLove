<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RawmaterialInventories $rawmaterialInventories
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit ProdRawmaterialuct Inventory'), ['action' => 'edit', $rawmaterialInventories->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Rawmaterial Inventory'), ['action' => 'delete', $rawmaterialInventories->id], ['confirm' => __('Are you sure you want to delete # {0}?', $rawmaterialInventories->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Rawmaterial Inventory'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Rawmaterial Inventory'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="rawmaterialInventories view content">
            <h3><?= h($rawmaterialInventories->id) ?></h3>
            <table>

                <tr>
                    <th><?= __('rawmaterialID') ?></th>
                    <td><?= $this->Number->format($rawmaterialInventories->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Quantity') ?></th>
                    <td><?= $this->Number->format($rawmaterialInventories->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('lowStockLimit') ?></th>
                    <td><?= $this->Number->format($rawmaterialInventories->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
