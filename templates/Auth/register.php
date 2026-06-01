<style>

</style>
<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */

$this->layout = 'login';
$this->assign('title', 'Register new user');
?>
<div class="container register">
    <div class="users form content">

        <?= $this->Form->create($user ?? null) ?>

        <fieldset>
            <legend style="font-size:1.8em;">Register New User</legend>

            <?= $this->Flash->render() ?>

            <?= $this->Form->control('email', ['style' => 'width: 40%; margin-left:-8px;']); ?>


            <div class="row">
                <?= $this->Form->control('first_name', ['templateVars' => ['container_class' => 'column']]); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                <?= $this->Form->control('last_name', ['templateVars' => ['container_class' => 'column']]); ?>
            </div>

            <div class="row">
                <?php
                echo $this->Form->control('password', [
                    'value' => '',  // Ensure password is not sending back to the client side
                    'templateVars' => ['container_class' => 'column']
                ]); 

                echo '&nbsp;&nbsp;&nbsp;&nbsp;';
                // Validate password by repeating it
                echo $this->Form->control('password_confirm', [
                    'type' => 'password', 
                    'value' => '',  // Ensure password is not sending back to the client side
                    'label' => 'Retype Password',
                    'templateVars' => ['container_class' => 'column']
                ]);
                ?>
            </div>

        </fieldset>

        <?= $this->Form->button('Register', ['style' => '
        color: #fff;
        background-color: #212529;
        border-color: #212529;
        ', 'class' => 'btn-dark']) ?>


      
        <?= $this->Html->link('Back to login', ['controller' => 'Auth', 'action' => 'login'], [
    'class' => 'button button-outline float-right',
    'style' => 'color:black; border-color: black;'
]) ?>
        <?= $this->Form->end() ?>

    </div>
</div>
