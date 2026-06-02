<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$usingFirestore = $usingFirestore ?? false;
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Users'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="users form content">
            <?= $this->Form->create($usingFirestore ? null : $user) ?>
            <fieldset>
                <legend><?= __('Add User') ?></legend>
                <?php
                    echo $this->Form->control('first_name', ['value' => $usingFirestore ? ($user->first_name ?? '') : null]);
                    echo $this->Form->control('last_name', ['value' => $usingFirestore ? ($user->last_name ?? '') : null]);
                    echo $this->Form->control('email', ['value' => $usingFirestore ? ($user->email ?? '') : null]);
                    echo $this->Form->control('password', ['value' => '']);
                    echo $this->Form->control('nonce', ['value' => $usingFirestore ? ($user->nonce ?? '') : null]);
                    echo $this->Form->control('nonce_expiry', ['value' => $usingFirestore ? ($user->nonce_expiry ?? '') : null]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
