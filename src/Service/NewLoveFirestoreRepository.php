<?php
declare(strict_types=1);

namespace App\Service;

use Authentication\PasswordHasher\DefaultPasswordHasher;
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

    public function supplierList(): array
    {
        return $this->nameList('suppliers');
    }

    public function collections(): array
    {
        return $this->basicNameRows('collections');
    }

    public function collection(string $id): ?array
    {
        return $this->basicNameRow('collections', $id);
    }

    public function createCollection(array $data): array
    {
        return $this->createBasicNameDocument('collections', $data);
    }

    public function updateCollection(string $id, array $data): array
    {
        return $this->updateBasicNameDocument('collections', $id, $data);
    }

    public function deleteCollection(string $id): void
    {
        $this->firestore->deleteDocument('collections', $id);
        unset($this->cache['collections']);
    }

    public function colours(): array
    {
        return $this->basicNameRows('colours');
    }

    public function colour(string $id): ?array
    {
        return $this->basicNameRow('colours', $id);
    }

    public function createColour(array $data): array
    {
        return $this->createBasicNameDocument('colours', $data);
    }

    public function updateColour(string $id, array $data): array
    {
        return $this->updateBasicNameDocument('colours', $id, $data);
    }

    public function deleteColour(string $id): void
    {
        $this->firestore->deleteDocument('colours', $id);
        unset($this->cache['colours']);
    }

    public function suppliers(): array
    {
        $suppliers = array_map(function ($supplier) {
            return get_object_vars($supplier);
        }, array_values($this->collectionObjects('suppliers')));

        usort($suppliers, function (array $first, array $second): int {
            return (int)($first['id'] ?? 0) <=> (int)($second['id'] ?? 0);
        });

        return $suppliers;
    }

    public function supplier(string $id): ?array
    {
        $supplier = $this->documentObject('suppliers', $id);

        if ($supplier === null) {
            return null;
        }

        return get_object_vars($supplier);
    }

    public function createSupplier(array $data): array
    {
        $id = $this->nextNumericId('suppliers');
        $payload = $this->supplierPayload($data, $id);
        $saved = $this->firestore->setDocument('suppliers', (string)$id, $payload);
        unset($this->cache['suppliers']);

        return $saved;
    }

    public function updateSupplier(string $id, array $data): array
    {
        $existing = $this->supplier($id);

        if ($existing === null) {
            throw new \RuntimeException('Supplier not found.');
        }

        $payload = $this->supplierPayload(array_merge($existing, $data), (int)$existing['id']);
        $saved = $this->firestore->setDocument('suppliers', $id, $payload);
        unset($this->cache['suppliers']);

        return $saved;
    }

    public function deleteSupplier(string $id): void
    {
        $this->firestore->deleteDocument('suppliers', $id);
        unset($this->cache['suppliers']);
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

    public function emptyProduct(): stdClass
    {
        return $this->toObject([
            'name' => '',
            'description' => '',
            'photo' => '',
            'collection_id' => null,
            'colour_id' => null,
        ]);
    }

    public function productExistsWithNameAndColour(string $name, $colourId, ?string $excludeId = null): bool
    {
        foreach ($this->collectionObjects('products') as $product) {
            if ($excludeId !== null && (string)$product->id === (string)$excludeId) {
                continue;
            }

            if (
                strcasecmp((string)$product->name, $name) === 0 &&
                (string)$product->colour_id === (string)$colourId
            ) {
                return true;
            }
        }

        return false;
    }

    public function createProduct(array $data, string $photo): stdClass
    {
        $id = $this->nextNumericId('products');
        $payload = $this->productPayload($data, $photo, $id);
        $saved = $this->firestore->setDocument('products', (string)$id, $payload);
        unset($this->cache['products']);

        return $this->toObject($saved);
    }

    public function updateProduct(string $id, array $data, ?string $photo = null): stdClass
    {
        $existing = $this->documentObject('products', $id);

        if ($existing === null) {
            throw new \RuntimeException('Product not found.');
        }

        $payload = $this->productPayload(
            array_merge(get_object_vars($existing), $data),
            $photo ?? (string)$existing->photo,
            (int)$existing->id
        );
        $saved = $this->firestore->setDocument('products', (string)$id, $payload);
        unset($this->cache['products']);

        return $this->toObject($saved);
    }

    public function deleteProduct(string $id): void
    {
        $this->firestore->deleteDocument('products', $id);
        unset($this->cache['products']);
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

    public function emptyRawmaterial(): stdClass
    {
        return $this->toObject([
            'name' => '',
            'delivery_time' => '',
            'delivery_time_unit' => '',
            'delivery_time_value' => '',
            'description' => '',
            'cost_price' => '',
            'supplier_id' => null,
            'photo' => '',
            'colour_id' => null,
            'lowStockLimit' => null,
        ]);
    }

    public function rawmaterialExistsWithNameAndColour(string $name, $colourId, ?string $excludeId = null): bool
    {
        foreach ($this->collectionObjects('rawmaterials') as $rawmaterial) {
            if ($excludeId !== null && (string)$rawmaterial->id === (string)$excludeId) {
                continue;
            }

            if (
                strcasecmp((string)$rawmaterial->name, $name) === 0 &&
                (string)$rawmaterial->colour_id === (string)$colourId
            ) {
                return true;
            }
        }

        return false;
    }

    public function createRawmaterial(array $data, string $photo): stdClass
    {
        $id = $this->nextNumericId('rawmaterials');
        $payload = $this->rawmaterialPayload($data, $photo, $id);
        $saved = $this->firestore->setDocument('rawmaterials', (string)$id, $payload);
        unset($this->cache['rawmaterials']);

        return $this->toObject($saved);
    }

    public function updateRawmaterial(string $id, array $data, ?string $photo = null): stdClass
    {
        $existing = $this->documentObject('rawmaterials', $id);

        if ($existing === null) {
            throw new \RuntimeException('Raw material not found.');
        }

        $payload = $this->rawmaterialPayload(
            array_merge(get_object_vars($existing), $data),
            $photo ?? (string)$existing->photo,
            (int)$existing->id
        );
        $saved = $this->firestore->setDocument('rawmaterials', (string)$id, $payload);
        unset($this->cache['rawmaterials']);

        return $this->toObject($saved);
    }

    public function deleteRawmaterial(string $id): void
    {
        $this->firestore->deleteDocument('rawmaterials', $id);
        unset($this->cache['rawmaterials']);
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

    public function userByEmail(string $email): ?array
    {
        $email = strtolower(trim($email));

        if ($email === '') {
            return null;
        }

        foreach ($this->collectionObjects('users') as $user) {
            if (!isset($user->email)) {
                continue;
            }

            if (strtolower(trim((string)$user->email)) === $email) {
                return get_object_vars($user);
            }
        }

        return null;
    }

    public function users(): array
    {
        $users = array_map(function ($user) {
            return get_object_vars($user);
        }, array_values($this->collectionObjects('users')));

        usort($users, function (array $first, array $second): int {
            return (int)($first['id'] ?? 0) <=> (int)($second['id'] ?? 0);
        });

        return $users;
    }

    public function userById(string $id): ?array
    {
        $user = $this->documentObject('users', $id);

        if ($user === null) {
            return null;
        }

        return get_object_vars($user);
    }

    public function userByNonce(?string $nonce): ?array
    {
        $nonce = trim((string)$nonce);

        if ($nonce === '') {
            return null;
        }

        foreach ($this->collectionObjects('users') as $user) {
            if (!isset($user->nonce)) {
                continue;
            }

            if ((string)$user->nonce === $nonce) {
                return get_object_vars($user);
            }
        }

        return null;
    }

    public function userExistsWithEmail(string $email): bool
    {
        return $this->userByEmail($email) !== null;
    }

    public function createUser(array $data): array
    {
        $id = $this->nextNumericId('users');
        $now = gmdate('Y-m-d H:i:s');
        $payload = [
            'id' => $id,
            'first_name' => trim((string)($data['first_name'] ?? '')),
            'last_name' => trim((string)($data['last_name'] ?? '')),
            'email' => strtolower(trim((string)($data['email'] ?? ''))),
            'password' => (new DefaultPasswordHasher())->hash((string)($data['password'] ?? '')),
            'nonce' => $data['nonce'] ?? null,
            'nonce_expiry' => $data['nonce_expiry'] ?? null,
            'created' => $now,
            'modified' => $now,
        ];

        $saved = $this->firestore->setDocument('users', (string)$id, $payload);
        unset($this->cache['users']);

        return $saved;
    }

    public function updateUser(string $id, array $data): array
    {
        $existing = $this->userById($id);

        if ($existing === null) {
            throw new \RuntimeException('User not found.');
        }

        $password = trim((string)($data['password'] ?? ''));
        $merged = array_merge($existing, $data);

        if ($password === '') {
            $merged['password'] = $existing['password'] ?? '';
        }

        $payload = $this->userPayload($merged);

        if ($password !== '') {
            $payload['password'] = (new DefaultPasswordHasher())->hash($password);
        }

        $payload['modified'] = gmdate('Y-m-d H:i:s');
        $saved = $this->firestore->setDocument('users', $id, $payload);
        unset($this->cache['users']);

        return $saved;
    }

    public function deleteUser(string $id): void
    {
        $this->firestore->deleteDocument('users', $id);
        unset($this->cache['users']);
    }

    public function updateUserResetToken(string $id, string $nonce, string $nonceExpiry): array
    {
        $existing = $this->userById($id);

        if ($existing === null) {
            throw new \RuntimeException('User not found.');
        }

        $payload = $this->userPayload($existing);
        $payload['nonce'] = $nonce;
        $payload['nonce_expiry'] = $nonceExpiry;
        $payload['modified'] = gmdate('Y-m-d H:i:s');

        $saved = $this->firestore->setDocument('users', $id, $payload);
        unset($this->cache['users']);

        return $saved;
    }

    public function updateUserPassword(string $id, string $password, bool $clearResetToken = false): array
    {
        $existing = $this->userById($id);

        if ($existing === null) {
            throw new \RuntimeException('User not found.');
        }

        $payload = $this->userPayload($existing);
        $payload['password'] = (new DefaultPasswordHasher())->hash($password);
        $payload['modified'] = gmdate('Y-m-d H:i:s');

        if ($clearResetToken) {
            $payload['nonce'] = null;
            $payload['nonce_expiry'] = null;
        }

        $saved = $this->firestore->setDocument('users', $id, $payload);
        unset($this->cache['users']);

        return $saved;
    }

    private function userPayload(array $data): array
    {
        $id = $data['id'] ?? $data['_document_id'] ?? '';

        return [
            'id' => is_numeric($id) ? (int)$id : (string)$id,
            'first_name' => trim((string)($data['first_name'] ?? '')),
            'last_name' => trim((string)($data['last_name'] ?? '')),
            'email' => strtolower(trim((string)($data['email'] ?? ''))),
            'password' => (string)($data['password'] ?? ''),
            'nonce' => $data['nonce'] ?? null,
            'nonce_expiry' => $data['nonce_expiry'] ?? null,
            'created' => $data['created'] ?? gmdate('Y-m-d H:i:s'),
            'modified' => $data['modified'] ?? gmdate('Y-m-d H:i:s'),
        ];
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

    private function basicNameRows(string $collection): array
    {
        $items = array_map(function ($item) {
            return get_object_vars($item);
        }, array_values($this->collectionObjects($collection)));

        usort($items, function (array $first, array $second): int {
            return (int)($first['id'] ?? 0) <=> (int)($second['id'] ?? 0);
        });

        return $items;
    }

    private function basicNameRow(string $collection, string $id): ?array
    {
        $item = $this->documentObject($collection, $id);

        if ($item === null) {
            return null;
        }

        return get_object_vars($item);
    }

    private function createBasicNameDocument(string $collection, array $data): array
    {
        $id = $this->nextNumericId($collection);
        $payload = $this->basicNamePayload($data, $id);
        $saved = $this->firestore->setDocument($collection, (string)$id, $payload);
        unset($this->cache[$collection]);

        return $saved;
    }

    private function updateBasicNameDocument(string $collection, string $id, array $data): array
    {
        $existing = $this->basicNameRow($collection, $id);

        if ($existing === null) {
            throw new \RuntimeException('Document not found.');
        }

        $payload = $this->basicNamePayload(array_merge($existing, $data), (int)$existing['id']);
        $saved = $this->firestore->setDocument($collection, $id, $payload);
        unset($this->cache[$collection]);

        return $saved;
    }

    private function basicNamePayload(array $data, int $id): array
    {
        return [
            'id' => $id,
            'name' => trim((string)($data['name'] ?? '')),
        ];
    }

    private function nextNumericId(string $collection): int
    {
        $maxId = 0;

        foreach ($this->collectionObjects($collection) as $item) {
            $maxId = max($maxId, (int)$item->id);
        }

        return $maxId + 1;
    }

    private function productPayload(array $data, string $photo, int $id): array
    {
        return [
            'id' => $id,
            'name' => trim((string)($data['name'] ?? '')),
            'description' => trim((string)($data['description'] ?? '')),
            'photo' => $photo,
            'collection_id' => $this->nullableInteger($data['collection_id'] ?? null),
            'colour_id' => $this->nullableInteger($data['colour_id'] ?? null),
        ];
    }

    private function rawmaterialPayload(array $data, string $photo, int $id): array
    {
        return [
            'id' => $id,
            'name' => trim((string)($data['name'] ?? '')),
            'delivery_time' => $this->deliveryTime($data),
            'description' => trim((string)($data['description'] ?? '')),
            'cost_price' => trim((string)($data['cost_price'] ?? '')),
            'supplier_id' => $this->nullableInteger($data['supplier_id'] ?? null),
            'photo' => $photo,
            'colour_id' => $this->nullableInteger($data['colour_id'] ?? null),
            'lowStockLimit' => $this->nullableInteger($data['lowStockLimit'] ?? null),
        ];
    }

    private function supplierPayload(array $data, int $id): array
    {
        return [
            'id' => $id,
            'name' => trim((string)($data['name'] ?? '')),
            'email' => trim((string)($data['email'] ?? '')),
            'phone_no' => trim((string)($data['phone_no'] ?? '')),
            'website' => trim((string)($data['website'] ?? '')),
            'location' => trim((string)($data['location'] ?? '')),
        ];
    }

    private function deliveryTime(array $data): string
    {
        $value = trim((string)($data['delivery_time_value'] ?? ''));
        $unit = trim((string)($data['delivery_time_unit'] ?? ''));

        if ($value !== '' && $unit !== '') {
            return $value . ' ' . $unit;
        }

        return trim((string)($data['delivery_time'] ?? ''));
    }

    private function nullableInteger($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int)$value;
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
