<?php
declare(strict_types=1);

namespace App\Service;

use stdClass;

class NewLoveFirestoreRepository
{
    private FirestoreService $firestore;

    private array $cache = [];

    public function __construct(?FirestoreService $firestore = null)
    {
        $this->firestore = $firestore ?? new FirestoreService();
    }

    public function isEnabled(): bool
    {
        return $this->firestore->isConfigured();
    }

    public function colourList(): array
    {
        return $this->nameList('colours');
    }

    public function collectionList(): array
    {
        return $this->nameList('collections');
    }

    public function products(?string $name, ?string $colourId, ?string $collectionId, ?string $description): array
    {
        $products = array_values($this->collectionObjects('products'));
        $inventories = $this->groupObjectsByField('product_inventories', 'product_id');
        $colours = $this->collectionObjects('colours');
        $collections = $this->collectionObjects('collections');

        foreach ($products as $product) {
            $productId = (string)$product->id;
            $product->product_inventories = $inventories[$productId] ?? [];
            $product->colour = isset($colours[(string)$product->colour_id]) ? $colours[(string)$product->colour_id] : null;
            $product->collection = isset($collections[(string)$product->collection_id]) ? $collections[(string)$product->collection_id] : null;
        }

        return array_values(array_filter($products, function ($product) use ($name, $colourId, $collectionId, $description) {
            if ($name !== null && $name !== '' && !$this->contains((string)$product->name, $name)) {
                return false;
            }

            if ($description !== null && $description !== '' && !$this->contains((string)$product->description, $description)) {
                return false;
            }

            if ($colourId !== null && $colourId !== '' && (string)$product->colour_id !== (string)$colourId) {
                return false;
            }

            if ($collectionId !== null && $collectionId !== '' && (string)$product->collection_id !== (string)$collectionId) {
                return false;
            }

            return true;
        }));
    }

    public function product(string $id): ?stdClass
    {
        $product = $this->documentObject('products', $id);

        if ($product === null) {
            return null;
        }

        $colours = $this->collectionObjects('colours');
        $collections = $this->collectionObjects('collections');
        $rawmaterials = $this->collectionObjects('rawmaterials');
        $links = array_values($this->collectionObjects('materials_products'));
        $materialsProducts = [];

        foreach ($links as $link) {
            if ((string)$link->product_id !== (string)$id) {
                continue;
            }

            $rawmaterialId = (string)$link->rawmaterial_id;

            if (!isset($rawmaterials[$rawmaterialId])) {
                continue;
            }

            $link->rawmaterial = $rawmaterials[$rawmaterialId];
            $materialsProducts[] = $link;
        }

        $product->colour = isset($colours[(string)$product->colour_id]) ? $colours[(string)$product->colour_id] : null;
        $product->collection = isset($collections[(string)$product->collection_id]) ? $collections[(string)$product->collection_id] : null;
        $product->materials_products = $materialsProducts;

        return $product;
    }

    public function rawmaterials(?string $name, ?string $colourId, ?string $description): array
    {
        $rawmaterials = array_values($this->collectionObjects('rawmaterials'));
        $inventories = $this->groupObjectsByField('rawmaterial_inventories', 'rawmaterial_id');
        $colours = $this->collectionObjects('colours');
        $suppliers = $this->collectionObjects('suppliers');

        foreach ($rawmaterials as $rawmaterial) {
            $rawmaterialId = (string)$rawmaterial->id;
            $inventoryList = $inventories[$rawmaterialId] ?? [];
            $rawmaterial->rawmaterial_inventory = $inventoryList[0] ?? null;
            $rawmaterial->colour = isset($colours[(string)$rawmaterial->colour_id]) ? $colours[(string)$rawmaterial->colour_id] : null;
            $rawmaterial->supplier = isset($suppliers[(string)$rawmaterial->supplier_id]) ? $suppliers[(string)$rawmaterial->supplier_id] : null;
        }

        return array_values(array_filter($rawmaterials, function ($rawmaterial) use ($name, $colourId, $description) {
            if ($name !== null && $name !== '' && !$this->contains((string)$rawmaterial->name, $name)) {
                return false;
            }

            if ($description !== null && $description !== '' && !$this->contains((string)$rawmaterial->description, $description)) {
                return false;
            }

            if ($colourId !== null && $colourId !== '' && (string)$rawmaterial->colour_id !== (string)$colourId) {
                return false;
            }

            return true;
        }));
    }

    public function rawmaterial(string $id): ?stdClass
    {
        $rawmaterial = $this->documentObject('rawmaterials', $id);

        if ($rawmaterial === null) {
            return null;
        }

        $colours = $this->collectionObjects('colours');
        $suppliers = $this->collectionObjects('suppliers');
        $inventories = $this->groupObjectsByField('rawmaterial_inventories', 'rawmaterial_id');
        $inventoryList = $inventories[(string)$id] ?? [];

        $rawmaterial->rawmaterial_inventory = $inventoryList[0] ?? null;
        $rawmaterial->colour = isset($colours[(string)$rawmaterial->colour_id]) ? $colours[(string)$rawmaterial->colour_id] : null;
        $rawmaterial->supplier = isset($suppliers[(string)$rawmaterial->supplier_id]) ? $suppliers[(string)$rawmaterial->supplier_id] : null;

        return $rawmaterial;
    }

    public function rawmaterialsLowStock(): array
    {
        $lowStock = [];

        foreach ($this->rawmaterials(null, null, null) as $rawmaterial) {
            if ($rawmaterial->rawmaterial_inventory === null) {
                continue;
            }

            $quantity = (int)$rawmaterial->rawmaterial_inventory->quantity;
            $limit = (int)$rawmaterial->rawmaterial_inventory->lowStockLimit;

            if ($quantity <= $limit) {
                $lowStock[] = $rawmaterial;
            }
        }

        return $lowStock;
    }

    private function nameList(string $collection): array
    {
        $list = [];

        foreach ($this->collectionObjects($collection) as $item) {
            if (isset($item->id, $item->name)) {
                $list[$item->id] = $item->name;
            }
        }

        return $list;
    }

    private function documentObject(string $collection, string $id): ?stdClass
    {
        $data = $this->firestore->getDocument($collection, $id);

        if ($data === null) {
            return null;
        }

        return $this->toObject($data);
    }

    private function collectionObjects(string $collection): array
    {
        if (!isset($this->cache[$collection])) {
            $items = [];

            foreach ($this->firestore->listDocuments($collection) as $data) {
                $object = $this->toObject($data);
                $items[(string)$object->id] = $object;
            }

            $this->cache[$collection] = $items;
        }

        return $this->cache[$collection];
    }

    private function groupObjectsByField(string $collection, string $field): array
    {
        $groups = [];

        foreach ($this->collectionObjects($collection) as $object) {
            if (!isset($object->{$field})) {
                continue;
            }

            $groups[(string)$object->{$field}][] = $object;
        }

        return $groups;
    }

    private function toObject(array $data): stdClass
    {
        if (!array_key_exists('id', $data) && array_key_exists('_document_id', $data)) {
            $data['id'] = $data['_document_id'];
        }

        $object = new stdClass();

        foreach ($data as $key => $value) {
            $object->{$key} = is_array($value) ? $this->toObject($value) : $value;
        }

        return $object;
    }

    private function contains(string $value, string $search): bool
    {
        return stripos($value, $search) !== false;
    }
}
