<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Colour $colour
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Product Colour'), ['action' => 'edit', $colour->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Product Colour'), ['action' => 'delete', $colour->id], ['confirm' => __('Are you sure you want to delete # {0}?', $colour->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Product Colour'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Product Colour'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="productColour view content">
            <h3><?= h($colour->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Colour name') ?></th>
                    <td><?= h($colour->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Colour ID') ?></th>
                    <td><?= $this->Number->format($colour->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
