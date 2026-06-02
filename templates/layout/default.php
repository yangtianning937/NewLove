<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */
use Cake\ORM\TableRegistry;
use App\Model\Table\ProductInventoriesTable;
use Cake\Database\Expression\QueryExpression;
$cakeDescription = 'CakePHP: the rapid development php framework';
$this->disableAutoLayout();
$isFirestoreReadOnly = !empty($usingFirestore);
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        NEW LOVE
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <?= $this->Html->css(['normalize.min', 'milligram.min', 'cake', 'home']) ?>


    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->Html->css('style.css') ?>
    <?= $this->Html->css('home.css') ?>
    <?= $this->Html->script('jquery-3.6.4.min.js'); ?>
    <?= $this->Html->script('scripts.js'); ?>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>New Love</title>
    <link rel="icon" type="image/x-icon" href="/webroot/favicon1.ico">
    <link href="https://fonts.googleapis.com/css2?family=Autour+One&family=Poppins:wght@400;700&family=Rozha+One&display=swap" rel="stylesheet">
    <!-- <link rel="stylesheet" href="webroot/css/home.css"> -->
    <link rel="stylesheet" href="webroot/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


</head>
<body>
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container px-4 px-lg-5" style="border: none;">
                <a class="navbar-brand" href="<?= $this->Url->build(['controller'=>'#','action'=> '#']) ?>">NEW LOVE</a>

                <div class="dropdown">
                    <!-- Button that triggers dropdown -->
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            style="background-color: rgba(255, 87, 51, 0);
                    color: black;
                    border: none;
                    font-size: 13px;">Products
                    </button>



                    <!-- Dropdown content -->
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="<?= $this->Url->build(['controller'=>'Products','action'=> 'index']) ?>">Products</a>
                        <a class="dropdown-item" href="<?= $this->Url->build(['controller'=>'Products','action'=> 'add']) ?>">Add new Products</a>
                        <a class="dropdown-item" href="<?= $this->Url->build(['controller'=>'Collections', 'action' => 'index']) ?>">Product Collection</a>
                        <a class="dropdown-item" href="<?= $this->Url->build(['controller'=>'Colours', 'action' => 'index']) ?>">Colours</a>
                        <?php if (!$isFirestoreReadOnly): ?>
                        <a class="dropdown-item" href="<?= $this->Url->build(['controller'=>'ProductInventories','action'=> 'index']) ?>">Product Inventory</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dropdown">
                    <!-- Button that triggers dropdown -->
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            style="background-color: rgba(255, 87, 51, 0);
                    color: black;
                    border: none;
                    font-size: 13px;">Materials
                    </button>



                    <!-- Dropdown content -->
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="<?= $this->Url->build(['controller'=>'Rawmaterials','action'=> 'index']) ?>">Materials</a>
                        <a class="dropdown-item" href="<?= $this->Url->build(['controller'=>'Rawmaterials','action'=> 'add']) ?>">Add new Materials</a>
                        <a class="dropdown-item" href="<?= $this->Url->build(['controller'=>'Colours', 'action' => 'index']) ?>">Colours</a>
                        <?php if (!$isFirestoreReadOnly): ?>
                        <a class="dropdown-item" href="<?= $this->Url->build(['controller'=>'RawmaterialInventories','action'=> 'index']) ?>">Material Inventory</a>
                        <?php endif; ?>
                    </div>
                </div>


                <a class="navbar-brand" href="<?= $this->Url->build(['controller'=>'Suppliers','action'=> 'index']) ?>">Suppliers</a>
                <li class="dropdown">
                    <i class="fa-solid fa-bell fa-2xl" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"></i>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <?php
                            foreach ($rawmaterials_lowstock as $rawmaterial) {
                                $target = $isFirestoreReadOnly
                                    ? ['controller' => 'Rawmaterials', 'action' => 'view', $rawmaterial->id]
                                    : ['controller' => 'RawmaterialInventories', 'action' => 'index'];
                                echo '<li><a class="dropdown-item" href="' . $this->Url->build($target) . '">Raw material: ' . h($rawmaterial->name) . ' is low on stock</a></li>';
                            }
                        ?>
                    </ul>
                </li>

                <?php
                    if ($this->Identity->isLoggedIn()) {
                        echo $this->Html->link(__('Logout'), ['controller' => 'Auth', 'action' => 'logout'], ['class' => 'btn btn-dark btn-lg']);
                    }
                    else
                    echo $this->Html->link('Log in', ['controller' => 'Auth', 'action' => 'login'], ['class' => 'btn btn-dark btn-lg']);
                ?>
            </div>
        </nav>
    <!-- Section-->
    <main class="main">
        <div class="container">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>

    <!-- Bootstrap core JavaScript-->


    <!-- Core plugin JavaScript-->
    <?= $this->Html->script('jquery.easing.min.js'); ?>

    <?= $this->fetch('script') ?>


</body>
<!-- Footer-->
<footer class="py-5 bg-dark">
        <a href="#top">Back to top</a>
    </footer>
</html>
