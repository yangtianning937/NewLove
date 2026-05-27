<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Colours $colours
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Product Colour'), ['action' => 'edit', $colours->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Product Colour'), ['action' => 'delete', $colours->id], ['confirm' => __('Are you sure you want to delete # {0}?', $colours->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Product Colour'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Product Colour'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="productColour view content">
            <h3><?= h($colours->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Colour name') ?></th>
                    <td><?= h($colours->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Colour ID') ?></th>
                    <td><?= $this->Number->format($colours->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
