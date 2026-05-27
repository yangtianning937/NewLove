<!-- src/Template/Products/index.ctp -->
<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Rawmaterial> $rawmaterial
 */
?>
<section class="py-5">
    <div class="button-container">
        <?= $this->Html->link(__('Add New Product'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg']) ?>
        &emsp; &emsp; &emsp; &emsp; &emsp;
        <?= $this->Html->link('Product Collections', ['controller'=>'Collections', 'action' => 'index'], ['class' => 'btn btn-dark btn-lg']) ?>
        &emsp; &emsp; &emsp; &emsp; &emsp;
        <?= $this->Html->link('Colours', ['controller'=>'Colours', 'action' => 'index'], ['class' => 'btn btn-dark btn-lg']) ?>
        &emsp; &emsp; &emsp; &emsp; &emsp;
        <?= $this->Html->link('Product Inventory', ['controller'=>'ProductInventories','action'=> 'index'], ['class' => 'btn btn-dark btn-lg']) ?>
    </div>

    <div class="box">
        <form method="GET" action="<?= $this->Url->build(['action' => 'index']) ?>" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="name" placeholder="Search by Product Name" value="<?= isset($name) ? h($name) : '' ?>">
                <!-- Dropdown for selecting color -->
                <select class="form-control1" name="colour_id"> <!-- Change to 'colourID' -->
                    <option value="" disabled selected>Search by Colour</option>
                    <?php foreach ($colourName as $colorID => $colorName) : ?> <!-- Use $colorID as the value -->
                        <option value="<?= $colorID ?>" <?= ($colorID == $colourID) ? 'selected' : '' ?>><?= $colorName ?></option> <!-- Use $colorName as the displayed text -->
                    <?php endforeach; ?>
                </select>
                <i class="fa-solid fa-caret-down" style="position:absolute; top:30px; right:560px;"></i>
                <select class="form-control1" name="collection_id"> <!-- Add 'collectionID' -->
                    <option value="" disabled selected>Search by Collection</option>
                    <?php foreach ($collectionName as $collID => $collName) : ?> <!-- Use $collID as the value -->
                        <option value="<?= $collID ?>" <?= ($collID == $collectionID) ? 'selected' : '' ?>><?= $collName ?></option> <!-- Use $collName as the displayed text -->
                    <?php endforeach; ?>
                </select>
                <i class="fa-solid fa-caret-down" style="position:absolute; top:30px; right:220px;"></i>
                <button type="submit" class="btn btn-outline-search">Search</button>
                <button type="button" onclick="window.location.href='<?= $this->Url->build(['action' => 'index']) ?>' " class="btn btn-outline-search">Clear Filters</button>
            </div>
        </form>
    </div>

    <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
        <?php foreach ($product as $productItem): ?>
            <div class="col mb-5">
                <div class="card h-100">
                    <!-- Product image-->
                    <?= $this->Html->image($productItem->photo, ['height' => '300px']); ?>
                    <!-- Product details-->
                    <div class="card-body p-4">
                        <div class="text-center">
                            <!--Quantity-->
                            <?php
                            $totalQuantity = 0;
                            if (isset($productItem->product_inventories) && is_iterable($productItem->product_inventories)) {
                                foreach ($productItem->product_inventories as $productInventories) {
                                    $totalQuantity += $productInventories->quantity;
                                }
                            }
                            ?>
                            <p>Quantity: <?= h($totalQuantity) ?></p>
                            <!-- Product name-->
                            <h5 class="fw-bolder fs-2"><?= h($productItem->name) ?></h5>
                            <br>
                        </div>
                    </div>
                    <!-- Product actions-->
                    <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                        <div class="text-center">
                            <?= $this->Html->link(__('View Product Details'), ['action' => 'view', $productItem->id], ['class' => 'btn btn-outline-dark mt-auto btn-lg']) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


</section>
