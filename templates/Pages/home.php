<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.10.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\TableRegistry;
$cakeDescription = 'CakePHP: the rapid development php framework';



?>

<!--Container-->
<div class="container2">
  <br>
    <h1>Categories</h1>
    <br>
    <div class="rowa">
      <div class="card1">
      <?php
        echo $this->Html->image('box.png', array('width' => '300px', 'height' => '320px'));
      ?>
    <br>
<a class="btn" href="<?= $this->Url->build(['controller'=>'Products','action'=> 'index']) ?>">View Products</a>

        <!-- <img src="img/box.png" alt="" width="300px" height="320px"><br>
        <a href="#" class="btn">Products</a><br> -->
      </div>

      <div class="card1">
      <?php
        echo $this->Html->image('processing.png', array('width' => '300px', 'height' => '320px'));
      ?>
      <br>
      <a class="btn" href="<?= $this->Url->build(['controller'=>'Rawmaterials','action'=> 'index']) ?>">View Materials</a>
        <!-- <img src="img/processing.png" alt="" width="300px" height="320px"><br>
        <a href="#" class="btn">Materials</a><br> -->
      </div>

      <div class="card1">
      <?php
        echo $this->Html->image('b.png', array('width' => '300px', 'height' => '320px'));
      ?>
      <br>
      <a class="btn" href="<?= $this->Url->build(['controller'=>'Suppliers','action'=> 'index']) ?>">View Suppliers</a>
        <!-- <img src="img/b.png" alt="" width="300px" height="320px"><br>
        <a href="#" class="btn">Supplier</a><br> -->
      </div>


    </div>


  </div>

  <script>
  function toggleDropdown() {
    var dropdown = document.getElementById("myDropdown");
    if (dropdown.style.display === "block") {
        dropdown.style.display = "none";
    } else {
        dropdown.style.display = "block";
    }
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
    if (!event.target.matches('.icon-button')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.style.display === "block") {
                openDropdown.style.display = "none";
            }
        }
    }
}

</script>

