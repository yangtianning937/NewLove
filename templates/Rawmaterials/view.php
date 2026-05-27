<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rawmaterial $rawmaterial
 */
?>
<section>
<div class="row">
    <aside class="column">
        <?= $this->Html->link(__('To List of Materials'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg float-left']) ?>
            <?= $this->Html->link(__('Add New Material'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg float-right']) ?>
            </aside>
    <div class="container text-center">
            <div class="row justify-content-center">
                        <h1>Options</h1>
                        <div class="row row-cols-auto justify-content-center">
                    <div class="col">
                                <?= $this->Html->link(__('Edit This Material'), ['action' => 'edit', $rawmaterial->id], ['class' => 'btn btn-dark btn-lg']) ?>
                    </div>
                        <div class="col">
                        <?= $this->Form->postLink(__('Delete This Material'), ['action' => 'delete', $rawmaterial->id], ['confirm' => __('Are you sure you want to delete material {0} from the system?', $rawmaterial->name), 'class' => 'btn btn-dark btn-lg']) ?>
                    </div>
                </div>
                            </div>
            </div>
    </div>
    <div class="container px-4 px-lg-5 my-5">
        <div class="row gx-4 gx-lg-5 align-items-center">
                            <div class="col-md-6">
                <?php 
                echo $this->Html->image($rawmaterial->photo, array('height' => '400px', 'width' => '600px')); 
                ?>
            </div>
            <div class="col-md-6">
                                <h1 class="display-5 fw-bolder"><?= h($rawmaterial->name) ?></h1>
                <div class="fs-5 mb-5">
                    <span>Colour: <?= h($colourName) ?></span>
                </div>
                <div class="fs-5 mb-5">
                    <span>Description: <?= h($rawmaterial->description) ?></span>
                </div>
                <div class="fs-5 mb-5">
                    <span>Delivery Time: <?= h($rawmaterial->delivery_time) ?></span>
                </div>
                <div class="fs-5 mb-5">
                    <span>Cost: <?= h($rawmaterial->cost_price) ?></span>
                </div>
                
                <div class="fs-5 mb-5">
                    <span>Supplier: <?= h($supplierName) ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

