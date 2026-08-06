<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Product $product
 * @var \App\Model\Entity\Collection $collection
 *
 *
 */
?>
<!-- Product section-->
<section>
    <aside class="column">
        <?= $this->Html->link(__('To List of Products'), ['action' => 'index'], ['class' => 'btn btn-dark btn-lg float-left']) ?>
    </aside>
    <?php if (empty($usingFirestore)): ?>
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <?= $this->Html->link(__('Add New Product'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg float-right']) ?>
    </div>

    <div class="container text-center">
        <div class="row justify-content-center">
            <h1>Options</h1>
            <div class="row row-cols-auto justify-content-center">
                <div class="col">
                    <?= $this->Html->link(__('Edit This Product'), ['action' => 'edit', $product->id], ['class' => 'btn btn-dark btn-lg']) ?>
                </div>
                <div class="col">
                    <?= $this->Form->postLink(__('Delete This Product'), ['action' => 'delete', $product->id], ['confirm' => __('Are you sure you want to delete product {0} from the system?', $product->name), 'class' => 'btn btn-dark btn-lg']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="container px-4 px-lg-5 my-5">
        <div class="row gx-4 gx-lg-5 align-items-center">
            <div class="col-md-6">
                <?php
                echo $this->Html->image($product->photo, array('height' => '400px', 'width' => '600px'));
                ?>
            </div>
            <div class="col-md-6">
                <h1 class="display-5 fw-bolder"><?= h($product->name) ?></h1>
                <div class="fs-5 mb-5">
                    <span>Collection: <?= h($collectionName) ?></span>
                </div>
                <div class="fs-5 mb-5">
                    <span>Colour: <?= h($productColour) ?></span>
                </div>
                <div class="fs-5 mb-5">
                    <span>Description: <?= h($product->description) ?></span>
                </div>
            </div>
            <div>
                <?php if (!empty($product->materials_products)): ?>
                    <div class="mt-4">
                        <h3>Associated Raw Materials:</h3>
                        <div class="col">
                            <?= $this->Html->link(__('Add Material to this Product'), ['controller' => 'MaterialsProducts', 'action' => 'add', $product->id], ['class' => 'btn btn-dark btn-lg']) ?>
                            <?= $this->Html->link(__('View Materials Added to this Product'), ['controller' => 'MaterialsProducts', 'action' => 'index', $product->id], ['class' => 'btn btn-dark btn-lg']) ?>

                        </div>
                        <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                            <?php foreach ($product->materials_products as $materialsProduct): ?>
                                <div class="col mb-5">
                                    <div class="card h-100">
                                        <!-- Raw Material image -->
                                        <?= $this->Html->image($materialsProduct->rawmaterial->photo, ['height' => '300px', 'alt' => $materialsProduct->rawmaterial->name]); ?>

                                        <!-- Raw Material details -->
                                        <div class="card-body p-4">
                                            <div class="text-center">
                                                <!-- Raw Material name -->
                                                <h5 class="fw-bolder fs-2"><?= h($materialsProduct->rawmaterial->name) ?></h5>
                                                <br>
                                                <p>Required Quantity: <?= h($materialsProduct->quantity) ?></p>
                                            </div>
                                        </div>

                                        <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                            <div class="text-center"><?= $this->Html->link(__('View Material Details'), ['controller'=>'Rawmaterials', 'action' => 'view', $materialsProduct->rawmaterial->id], ['class' => 'btn btn-outline-dark mt-auto btn-lg']) ?></div>
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mt-4">
                        <p>No associated raw materials for this product.</p>
                        <div class="col">
                            <?= $this->Html->link(__('Add Materials Product for this Product'), ['controller' => 'MaterialsProducts', 'action' => 'add', $product->id], ['class' => 'btn btn-dark btn-lg']) ?>
                        </div>
                    </div>
                <?php endif; ?>


            </div>
        </div>
    </div>
</section>
