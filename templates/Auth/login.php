<?php
/**
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;

$debug = Configure::read('debug');
$usingFirestore = $usingFirestore ?? false;

$this->layout = 'login';
$this->assign('title', 'Login');
?>

<?= $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css') ?>
<style>
    .field-icon {
        float: right;
        margin-left: -25px;
        position: relative;
        z-index: 2;
        cursor: pointer;
    }


</style>

<div class="container login" style="max-width: 900px; margin: 0 auto;">
    <div class="row">
        <div class="column column-50 column-offset-25">
            <div class="users form content" >
                <?= $this->Form->create() ?>
                <fieldset>


                    <center><legend style="font-size: 2em; font-family: Arial;">Login</legend></center>

                    <?= $this->Flash->render() ?>
                    <!-- <center><legend>Please Enter Your Email and Password</legend></center> -->
                    <?php
                    echo $this->Form->control('email', [
                        'type' => 'email',
                        'required' => true,
                        'autofocus' => true
                    ]);
                    echo $this->Form->control('password', [
                        'type' => 'password',
                        'required' => true,
                        'templates' => [
                            'inputContainer' => '{{content}}<span toggle="#password" style="margin-top: 8.5px; margin-right: 5px;" class="fa fa-fw fa-eye field-icon toggle-password"></span>'
                        ]
                    ]);
                    ?>
                </fieldset>

                <center>
                <?= $this->Form->button('Login', [
                    'class' => 'btn-dark',
                    'style' => '
                    color: #fff;
                    background-color: #212529;
                    border-color: #212529;
                    width: 100%;
                    '
                ]) ?>&nbsp;&nbsp;



                    <?= $this->Html->link('Forgot password?', ['controller' => 'Auth', 'action' => 'forgetPassword'], [
                    'class' => 'button button-outline',
                    'style' => 'color: black; border-color: black; width:100%;'
                ]) ?> <center>

                        <?= $this->Form->end() ?>

                        <hr class="hr-between-buttons">

                        <?= $this->Html->link('Register new user', ['controller' => 'Auth', 'action' => 'register'], ['class' => 'button button-clear' , 'style' => 'color:black; font-size:15px;']) ?>
            </div>
        </div>
    </div>
</div>

<?= $this->Html->script('https://code.jquery.com/jquery-3.6.0.min.js') ?>
<script>
    $(document).ready(function() {
        $(".toggle-password").click(function() {
            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    });
</script>
