<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\NewLoveFirestoreRepository;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use App\Model\Table\ColoursTable;
use App\Model\Table\SuppliersTable;

/**
 * Rawmaterial Controller
 *
 * @property \App\Model\Table\RawmaterialsTable $Rawmaterials
 * @method \App\Model\Entity\Rawmaterial[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RawmaterialsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $allowedActions = ['index', 'view'];

        if ($this->firestoreRepository()->isEnabled()) {
            $allowedActions = ['index', 'view', 'add', 'edit', 'delete'];
        }

        $this->Authentication->allowUnauthenticated($allowedActions);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $rawmaterialsName = $this->request->getQuery('name');
        $colourID = $this->request->getQuery('colour_id');
        $rawmaterialsDesc = $this->request->getQuery('description');

        $firestoreRepository = $this->firestoreRepository();

        if ($firestoreRepository->isEnabled()) {
            $rawmaterial = $firestoreRepository->rawmaterials($rawmaterialsName, $colourID, $rawmaterialsDesc);
            $colourName = $firestoreRepository->colourList();
            $usingFirestore = true;

            $this->set(compact('rawmaterial', 'colourName', 'usingFirestore'));
            $this->set(compact('rawmaterialsName', 'colourID', 'rawmaterialsDesc'));

            return;
        }

        $rawmaterialsTable = TableRegistry::getTableLocator()->get('Rawmaterials');

        // Build query conditions
        $conditions = [];

        if (!empty($rawmaterialsName)) {
            $conditions['name LIKE'] = '%' . $rawmaterialsName . '%';
        }

        if (!empty($rawmaterialsDesc)) {
            $conditions['description LIKE'] = '%' . $rawmaterialsDesc . '%';
        }

        // Start the query and apply conditions
        $query = $rawmaterialsTable->find('all')
            ->where($conditions)
            ->contain(['RawmaterialInventories']);  // Include RawmaterialInventories data

        // Filtering by colourID if selected
        if (!empty($colourID)) {
            $query->where(['colour_id' => $colourID]);
        }

        $rawmaterial = $query->toList();

        $ColoursTable = new ColoursTable();
        $colourName = $ColoursTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->toArray();

        $this->set(compact('rawmaterial', 'colourName'));
        $this->set(compact('rawmaterialsName', 'colourID', 'rawmaterialsDesc'));
    }


    /**
     * View method
     *
     * @param string|null $id Rawmaterial id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $firestoreRepository = $this->firestoreRepository();

        if ($firestoreRepository->isEnabled()) {
            $rawmaterial = $firestoreRepository->rawmaterial((string)$id);

            if ($rawmaterial === null) {
                throw new NotFoundException(__('Raw material not found'));
            }

            $supplierName = !empty($rawmaterial->supplier) ? $rawmaterial->supplier->name : 'null';
            $colourName = !empty($rawmaterial->colour) ? $rawmaterial->colour->name : 'null';
            $usingFirestore = true;

            $this->set(compact('rawmaterial', 'supplierName', 'colourName', 'usingFirestore'));

            return;
        }

        $rawmaterial = $this->Rawmaterials->get($id, [
            'contain' => [],
        ]);

        $rawmaterial = $this->Rawmaterials->get($id, [
            'contain' => ['Suppliers', 'Colours'], // Load the associated collection data for Supplier and Colour
        ]);

        // Access the supplierName and colourName directly from the $rawmaterialData object
        if ($rawmaterial->supplier !== null) {
            $supplierName = $rawmaterial->supplier->name;
        } else {
            $supplierName = "null";
        }
        if ($rawmaterial->colour !== null) {
            $colourName = $rawmaterial->colour->name;
        } else {
            $colourName = "null";
        }

        $this->set(compact('rawmaterial', 'supplierName', 'colourName'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $firestoreRepository = $this->firestoreRepository();

        if ($firestoreRepository->isEnabled()) {
            return $this->addWithFirestore($firestoreRepository);
        }

        $rawmaterial = $this->Rawmaterials->newEmptyEntity();

        $ColoursTable = new ColoursTable();
        $colourName = $ColoursTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->toArray();

        $suppliersTable = new SuppliersTable();
        $supplierName = $suppliersTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->toArray();

        $this->set(compact('rawmaterial', 'colourName', 'supplierName'));

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $file = $data['photo'] ?? null;

            if (!$file || empty($file->getClientFilename())) {
                $this->Flash->error(__('Please upload an image for the raw material.'));
                return;
            }

            // Merge delivery_time_unit and delivery_time_value into delivery_time
            if (isset($data['delivery_time_unit']) && isset($data['delivery_time_value'])) {
                $data['delivery_time'] = $data['delivery_time_value'] . ' ' . $data['delivery_time_unit'];
                unset($data['delivery_time_unit'], $data['delivery_time_value']);
            }

            // Check for existing raw material with same name
            $existingRawmaterials = $this->Rawmaterials->find()
                ->where(['name' => $data['name']])
                ->toArray();

            $duplicateExists = false;
            foreach ($existingRawmaterials as $existingRawmaterial) {
                if ($existingRawmaterial->colour_id == $data['colour_id']) {
                    $duplicateExists = true;
                    break;
                }
            }

            if ($duplicateExists) {
                $this->Flash->error(__('A rawmaterial with the same name and colour already exists. Please choose a different colour.'));
                return;
            }

            $filename = $file->getClientFilename();
            $file->moveTo(WWW_ROOT . 'img' . DS . $filename);

            $rawmaterial = $this->Rawmaterials->patchEntity($rawmaterial, $data);
            $rawmaterial->photo = $filename;

            if ($this->Rawmaterials->save($rawmaterial)) {
                $this->Flash->success(__('The rawmaterial has been saved.'));
                return $this->redirect(['action' => 'view', $rawmaterial->id]);
            } else {
                $this->Flash->error(__('The rawmaterial could not be saved. Please, try again.'));
            }
        }
    }




    /**
     * Edit method
     *
     * @param string|null $id Rawmaterial id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */

    public function edit($id = null)
    {
        $firestoreRepository = $this->firestoreRepository();

        if ($firestoreRepository->isEnabled()) {
            return $this->editWithFirestore($firestoreRepository, (string)$id);
        }

        $rawmaterial = $this->Rawmaterials->get($id, [
            'contain' => [],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            // Handling Form Submission
            $data = $this->request->getData();

            // Merge delivery_time_unit and delivery_time_value into delivery_time
            if (isset($data['delivery_time_unit']) && isset($data['delivery_time_value'])) {
                $data['delivery_time'] = $data['delivery_time_value'] . ' ' . $data['delivery_time_unit'];
                unset($data['delivery_time_unit'], $data['delivery_time_value']);
            }

            // Check for duplicate name and colour_id
            $existingRawmaterial = $this->Rawmaterials->find()
                ->where([
                    'name' => $data['name'],
                    'colour_id' => $data['colour_id'],
                    'id !=' => $id // exclude the current raw material being edited
                ])
                ->first();

            if ($existingRawmaterial) {
                $this->Flash->error(__('A raw material with the same name and color already exists.'));
            } else {
                // Handling the photo upload
                if (!empty($data['photo']) && $data['photo']->getError() == UPLOAD_ERR_OK) {
                    $filename = $data['photo']->getClientFilename();
                    $data['photo']->moveTo(WWW_ROOT . 'img' . DS . $filename);
                    $data['photo'] = $filename;
                } else {
                    unset($data['photo']);
                }

                // Patching and Saving the Entity
                $rawmaterial = $this->Rawmaterials->patchEntity($rawmaterial, $data);

                if ($this->Rawmaterials->save($rawmaterial)) {
                    $this->Flash->success(__('The raw material has been saved.'));
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('The raw material could not be saved. Please, try again.'));
            }
        } else {
            // Split delivery_time into delivery_time_unit and delivery_time_value for display
            if (isset($rawmaterial->delivery_time)) {
                list($value, $unit) = explode(' ', $rawmaterial->delivery_time, 2);
                $rawmaterial->delivery_time_value = $value;
                $rawmaterial->delivery_time_unit = $unit;
            }
        }

        // Retrieving Data for Dropdowns
        $rawmaterialColourTable = new ColoursTable();
        $colourName = $rawmaterialColourTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->toArray();

        $supplierTable = new SuppliersTable();
        $supplierName = $supplierTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->toArray();

        // Sending data to the View
        $this->set(compact('rawmaterial', 'colourName', 'supplierName'));
    }





    /**
     * Delete method
     *
     * @param string|null $id Rawmaterial id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $firestoreRepository = $this->firestoreRepository();

        if ($firestoreRepository->isEnabled()) {
            $firestoreRepository->deleteRawmaterial((string)$id);
            $this->Flash->success(__('The rawmaterial has been deleted.'));

            return $this->redirect(['action' => 'index']);
        }

        $rawmaterial = $this->Rawmaterials->get($id);
        if ($this->Rawmaterials->delete($rawmaterial)) {
            $this->Flash->success(__('The rawmaterial has been deleted.'));
        } else {
            $this->Flash->error(__('The rawmaterial could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function firestoreRepository(): NewLoveFirestoreRepository
    {
        return new NewLoveFirestoreRepository();
    }

    private function addWithFirestore(NewLoveFirestoreRepository $firestoreRepository)
    {
        $rawmaterial = $this->rawmaterialFormObject($firestoreRepository->emptyRawmaterial());
        $colourName = $firestoreRepository->colourList();
        $supplierName = $firestoreRepository->supplierList();
        $usingFirestore = true;

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $file = $data['photo'] ?? null;
            $filename = $this->uploadedFileName($file);

            if (trim((string)($data['name'] ?? '')) === '') {
                $this->Flash->error(__('Please enter a raw material name.'));
            } elseif (empty($data['colour_id'])) {
                $this->Flash->error(__('Please choose a colour.'));
            } elseif ($filename === null) {
                $this->Flash->error(__('Please upload an image for the raw material.'));
            } elseif ($firestoreRepository->rawmaterialExistsWithNameAndColour((string)$data['name'], $data['colour_id'])) {
                $this->Flash->error(__('A rawmaterial with the same name and colour already exists. Please choose a different colour.'));
            } else {
                $file->moveTo(WWW_ROOT . 'img' . DS . $filename);
                $savedRawmaterial = $firestoreRepository->createRawmaterial($data, $filename);
                $this->Flash->success(__('The rawmaterial has been saved.'));

                return $this->redirect(['action' => 'view', $savedRawmaterial->id]);
            }

            $rawmaterial = $this->rawmaterialFormObject((object)$data);
        }

        $this->set(compact('rawmaterial', 'colourName', 'supplierName', 'usingFirestore'));
    }

    private function editWithFirestore(NewLoveFirestoreRepository $firestoreRepository, string $id)
    {
        $existingRawmaterial = $firestoreRepository->rawmaterial($id);

        if ($existingRawmaterial === null) {
            throw new NotFoundException(__('Raw material not found'));
        }

        $rawmaterial = $this->rawmaterialFormObject($existingRawmaterial);
        $colourName = $firestoreRepository->colourList();
        $supplierName = $firestoreRepository->supplierList();
        $usingFirestore = true;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            if (trim((string)($data['name'] ?? '')) === '') {
                $this->Flash->error(__('Please enter a raw material name.'));
            } elseif (empty($data['colour_id'])) {
                $this->Flash->error(__('Please choose a colour.'));
            } elseif ($firestoreRepository->rawmaterialExistsWithNameAndColour((string)$data['name'], $data['colour_id'], $id)) {
                $this->Flash->error(__('A raw material with the same name and color already exists.'));
            } else {
                $file = $data['photo'] ?? null;
                $filename = $this->uploadedFileName($file);

                if ($filename !== null) {
                    $file->moveTo(WWW_ROOT . 'img' . DS . $filename);
                }

                $savedRawmaterial = $firestoreRepository->updateRawmaterial($id, $data, $filename);
                $this->Flash->success(__('The raw material has been saved.'));

                return $this->redirect(['action' => 'view', $savedRawmaterial->id]);
            }

            $rawmaterial = $this->rawmaterialFormObject((object)array_merge(get_object_vars($existingRawmaterial), $data));
        }

        $this->set(compact('rawmaterial', 'colourName', 'supplierName', 'usingFirestore'));
    }

    private function uploadedFileName($file): ?string
    {
        if (!is_object($file) || !method_exists($file, 'getClientFilename')) {
            return null;
        }

        $filename = trim((string)$file->getClientFilename());

        return $filename !== '' ? $filename : null;
    }

    private function rawmaterialFormObject(object $rawmaterial): object
    {
        $data = get_object_vars($rawmaterial);
        $deliveryTime = (string)($data['delivery_time'] ?? '');

        if ($deliveryTime !== '' && empty($data['delivery_time_value']) && empty($data['delivery_time_unit'])) {
            $parts = explode(' ', $deliveryTime, 2);
            $data['delivery_time_value'] = $parts[0] ?? '';
            $data['delivery_time_unit'] = $parts[1] ?? '';
        }

        return (object)$data;
    }
}
