<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Supplier;
use App\Service\NewLoveFirestoreRepository;
use Cake\Http\Exception\NotFoundException;

/**
 * Suppliers Controller
 *
 * @property \App\Model\Table\SuppliersTable $Suppliers
 * @method \App\Model\Entity\Supplier[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SuppliersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        if ($this->usesFirestore()) {
            $suppliers = array_map([$this, 'firestoreSupplierEntity'], (new NewLoveFirestoreRepository())->suppliers());

            $this->set(compact('suppliers'));

            return;
        }

        $suppliers = $this->paginate($this->Suppliers);

        $this->set(compact('suppliers'));
    }

    /**
     * View method
     *
     * @param string|null $id Supplier id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if ($this->usesFirestore()) {
            $supplier = $this->firestoreSupplierOrFail((string)$id);

            $this->set(compact('supplier'));

            return;
        }

        $supplier = $this->Suppliers->get($id, [
            'contain' => ['Rawmaterials'],
        ]);

        $this->set(compact('supplier'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if ($this->usesFirestore()) {
            $supplier = $this->firestoreSupplierEntity([], true);

            if ($this->request->is('post')) {
                $data = $this->request->getData();
                $supplier = $this->firestoreSupplierEntity($data, true);
                $errors = $this->validateFirestoreSupplier($data);

                if ($errors === []) {
                    try {
                        (new NewLoveFirestoreRepository())->createSupplier($data);
                        $this->Flash->success(__('The supplier has been saved.'));

                        return $this->redirect(['action' => 'index']);
                    } catch (\RuntimeException $exception) {
                        $this->Flash->error(__('The supplier could not be saved. Please, try again.'));
                    }
                } else {
                    $this->Flash->error(implode(' ', $errors));
                }
            }

            $this->set(compact('supplier'));

            return;
        }

        $supplier = $this->Suppliers->newEmptyEntity();
        if ($this->request->is('post')) {
            $supplier = $this->Suppliers->patchEntity($supplier, $this->request->getData());
            if ($this->Suppliers->save($supplier)) {
                $this->Flash->success(__('The supplier has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The supplier could not be saved. Please, try again.'));
        }
        $this->set(compact('supplier'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Supplier id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if ($this->usesFirestore()) {
            $repository = new NewLoveFirestoreRepository();
            $supplierData = $repository->supplier((string)$id);

            if ($supplierData === null) {
                throw new NotFoundException(__('Supplier not found'));
            }

            $supplier = $this->firestoreSupplierEntity($supplierData);

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();
                $supplier = $this->firestoreSupplierEntity(array_merge($supplierData, $data));
                $errors = $this->validateFirestoreSupplier($data);

                if ($errors === []) {
                    try {
                        $repository->updateSupplier((string)$id, $data);
                        $this->Flash->success(__('The supplier has been saved.'));

                        return $this->redirect(['action' => 'index']);
                    } catch (\RuntimeException $exception) {
                        $this->Flash->error(__('The supplier could not be saved. Please, try again.'));
                    }
                } else {
                    $this->Flash->error(implode(' ', $errors));
                }
            }

            $this->set(compact('supplier'));

            return;
        }

        $supplier = $this->Suppliers->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $supplier = $this->Suppliers->patchEntity($supplier, $this->request->getData());
            if ($this->Suppliers->save($supplier)) {
                $this->Flash->success(__('The supplier has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The supplier could not be saved. Please, try again.'));
        }
        $this->set(compact('supplier'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Supplier id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        if ($this->usesFirestore()) {
            try {
                (new NewLoveFirestoreRepository())->deleteSupplier((string)$id);
                $this->Flash->success(__('The supplier has been deleted.'));
            } catch (\RuntimeException $exception) {
                $this->Flash->error(__('The supplier could not be deleted. Please, try again.'));
            }

            return $this->redirect(['action' => 'index']);
        }

        $supplier = $this->Suppliers->get($id);
        if ($this->Suppliers->delete($supplier)) {
            $this->Flash->success(__('The supplier has been deleted.'));
        } else {
            $this->Flash->error(__('The supplier could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function usesFirestore(): bool
    {
        return (new NewLoveFirestoreRepository())->isEnabled();
    }

    private function firestoreSupplierOrFail(string $id): Supplier
    {
        $supplier = (new NewLoveFirestoreRepository())->supplier($id);

        if ($supplier === null) {
            throw new NotFoundException(__('Supplier not found'));
        }

        return $this->firestoreSupplierEntity($supplier);
    }

    private function firestoreSupplierEntity(array $data = [], bool $isNew = false): Supplier
    {
        unset($data['_document_id']);

        return new Supplier($data, [
            'useSetters' => false,
            'markClean' => true,
            'markNew' => $isNew,
        ]);
    }

    private function validateFirestoreSupplier(array $data): array
    {
        $errors = [];
        $name = trim((string)($data['name'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $phoneNo = trim((string)($data['phone_no'] ?? ''));
        $website = trim((string)($data['website'] ?? ''));
        $location = trim((string)($data['location'] ?? ''));

        if ($name === '') {
            $errors[] = 'The name cannot be empty.';
        } elseif (strlen($name) > 56) {
            $errors[] = 'The name cannot exceed 56 characters.';
        }

        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Please enter a valid email address.';
        }

        if ($phoneNo !== '') {
            if (!ctype_digit($phoneNo)) {
                $errors[] = 'Phone number should be numeric.';
            } elseif (strlen($phoneNo) < 6 || strlen($phoneNo) > 20) {
                $errors[] = 'Phone number should be between 6 and 20 digits long.';
            }
        }

        if ($website !== '' && !$this->isValidWebsite($website)) {
            $errors[] = 'Please enter a valid website.';
        }

        if (strlen($website) > 255) {
            $errors[] = 'The website cannot exceed 255 characters.';
        }

        if (strlen($location) > 255) {
            $errors[] = 'The location cannot exceed 255 characters.';
        }

        return $errors;
    }

    private function isValidWebsite(string $website): bool
    {
        $pattern = '/^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([\/\w .-]*)*\/?$/';

        return (bool)preg_match($pattern, $website);
    }
}
