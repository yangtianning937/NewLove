<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */

$this->assign('title', 'Change User Password - Users');
$usingFirestore = $usingFirestore ?? false;
$firstName = is_array($user ?? null) ? ($user['first_name'] ?? '') : ($user->first_name ?? '');
$lastName = is_array($user ?? null) ? ($user['last_name'] ?? '') : ($user->last_name ?? '');

?>
<div class="row">
    <div class="column-responsive">
        <div class="users form content">

            <?= $this->Form->create($usingFirestore ? null : $user) ?>

            <fieldset>

                <legend>Change Password for <u><?= h($firstName) ?> <?= h($lastName) ?></u></legend>

                <div class="row">
                    <?php
                    echo $this->Form->control('password', [
                        'label' => 'New Password',
                        'value' => '',  // Ensure password is not sending back to the client side
                        'templateVars' => ['container_class' => 'column']
                    ]);
                    // Validate password by repeating it
                    echo $this->Form->control('password_confirm', [
                        'type' => 'password',
                        'value' => '',  // Ensure password is not sending back to the client side
                        'label' => 'Retype New Password',
                        'templateVars' => ['container_class' => 'column']
                    ]);
                    ?>
                </div>

            </fieldset>

            <?= $this->Form->button('Submit') ?>
            <?= $this->Form->end() ?>

        </div>
    </div>
</div>
