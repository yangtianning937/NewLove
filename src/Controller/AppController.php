<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\NewLoveFirestoreRepository;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;

class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');

        // Further components...
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\EventInterface $event The beforeRender event.
     * @return \Cake\Http\Response|null
     */
    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        $firestoreRepository = new NewLoveFirestoreRepository();
        if ($firestoreRepository->isEnabled()) {
            $rawmaterials_lowstock = $firestoreRepository->rawmaterialsLowStock();
            $this->set(compact('rawmaterials_lowstock'));

            return;
        }

        // Load Rawmaterials model
        $RawmaterialsTable = $this->fetchTable("Rawmaterials");

        // Get all records from raw materials table, and include their inventories
        $rawmaterials = $RawmaterialsTable->find()
            ->contain("RawmaterialInventories");

        // Filter out inventories with quantity less than low stock limit
        $rawmaterials_lowstock = [];
        foreach ($rawmaterials as $rawmaterial) {
            if (!is_null($rawmaterial->rawmaterial_inventory) &&
                $rawmaterial->rawmaterial_inventory->quantity <=
                $rawmaterial->rawmaterial_inventory->lowStockLimit
            ) {
                $rawmaterials_lowstock[] = $rawmaterial;
            }
        }

        // Send the results to view template (home.php)
        $this->set(compact('rawmaterials_lowstock'));
    }
}
