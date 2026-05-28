<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Rawmaterials> $rawmaterials
 */
?>
<section class="py-5">

    <?php if (empty($usingFirestore)): ?>
    <div class="button-container">
    <?= $this->Html->link(__('Add New Material'), ['action' => 'add'], ['class' => 'btn btn-dark btn-lg']) ?>
    &emsp; &emsp; &emsp; &emsp; &emsp;
    <?= $this->Html->link('Colours', ['controller'=>'Colours', 'action' => 'index'], ['class' => 'btn btn-dark btn-lg']) ?>
    &emsp; &emsp; &emsp; &emsp; &emsp;
        <?= $this->Html->link('Material Inventory', ['controller'=>'RawmaterialInventories','action'=> 'index'], ['class' => 'btn btn-dark btn-lg']) ?>

</div>
    <?php endif; ?>

    <div class="box">
        <!-- Search form -->
            <form method="GET" action="<?= $this->Url->build(['action' => 'index']) ?>" class="mb-3">
                    <div class="input-group">
                <input type="text" class="form-control" name="name" placeholder="Search by Material Name" value="<?= isset($name) ? h($name) : '' ?>">
                <select class="form-control1" name="colour_id"> <!-- Change to 'colourID' -->
                    <option value="" disabled selected>Search by Colour</option>
                    <?php foreach ($colourName as $colorID => $colorName) : ?> <!-- Use $colorID as the value -->
                    <option value="<?= $colorID ?>" <?= ($colorID == $colourID) ? 'selected' : '' ?>><?= $colorName ?></option> <!-- Use $colorName as the displayed text -->
                    <?php endforeach; ?>
                </select>
                <i class="fa-solid fa-caret-down" style="position:absolute; top:30px; right:220px;"></i>
                <button type="submit" class="btn btn-outline-search">Search</button>
                    <button type="button" onclick="window.location.href='<?= $this->Url->build(['action' => 'index']) ?>' " class="btn btn-outline-search">Clear Filters</button>
                </div>            </div>
            </form>


        <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                <?php foreach ($rawmaterial as $rawmaterials): ?>
                <div class="col mb-5">
                    <div class="card h-100">
                    <!-- Rawmaterial image-->
                        <?= $this->Html->image($rawmaterials->photo, ['height' => '300px']); ?>
                        <!-- Rawmaterial details-->
                    <div class="card-body p-4">
                            <div class="text-center">

                                <!-- Rawmaterial name-->
                                <h5 class="fw-bolder fs-2"><?= h($rawmaterials->name) ?></h5>
                    <br>
                    </div>
                        </div>
                        <!-- Rawmaterial actions-->
                    <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                            <div class="text-center"><?= $this->Html->link(__('View Material Details'), ['action' => 'view', $rawmaterials->id], ['class' => 'btn btn-outline-dark mt-auto btn-lg']) ?></div>
                    </div>
                </div>
                </div>
                <?php endforeach; ?>
                </div>
</div>
</section>
