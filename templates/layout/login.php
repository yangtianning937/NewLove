<?php
/**
 * Login layout
 *
 * This layout comes with no navigation bar and Flash renderer placeholder. Usually used for login page or similar.
 *
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;

$appLocale = Configure::read('App.defaultLocale');
?>
<!DOCTYPE html>
<html lang="<?= $appLocale ?>">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $this->fetch('title') ?> - New Love
    </title>
    <?= $this->Html->meta('icon') ?>

    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">

    <?= $this->Html->css(['normalize.min', 'milligram.min', 'cake']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>

    <style>
         body {
    /* Set the background image and define its properties */
    background-image: url('../img/v745-kul-39.jpg');
    background-size: cover; 
    background-position: center; 
    background-repeat: no-repeat; 
}
        header {
            text-align: center; 
        }

        header img {
            display: inline-block; 
            margin-bottom: 30px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<main class="main">
    <header>
    <?php
                // Assuming the image is in the 'img' directory
                echo $this->Html->image('New Love Logo FINAL Transparent v2.png', [
                    'alt' => 'CakePHP',
                    'width' => 520,   
                    'height' => 90   
                ]);
            ?>

        </header>

    <?= $this->fetch('content') ?>
</main>
<footer>
</footer>
</body>
</html>
