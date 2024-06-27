<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CampaignRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CampaignCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CampaignCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Campaign::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/campaign');
        CRUD::setEntityNameStrings('campaña', 'campañas');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // set columns from db columns.
        CRUD::column('name')->label('nombre');
        CRUD::column('start_date')->label('Inicio');
        CRUD::column('end_date')->label('Fin');
        CRUD::column('code')->label('Código');
        CRUD::column('signed_contracts_count')->label('Contratos Firmados');
        


        CRUD::column('brand_id')
        ->label('Marca')
        ->model('App\Models\Brand')
        ->name('brand_id')
        ->type('select')
        ->entity('brand')
        ->attribute('name');

        CRUD::addButtonFromModelFunction('line', 'allContracts', 'allContracts', 'beginning');
        
        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(CampaignRequest::class);
        //CRUD::setFromDb(); // set fields from db columns.

        //CRUD::field('brand');
        
        CRUD::addFields([
            ['name' => 'name', 'label' => 'Nombre'],
            ['name' => 'start_date', 'label' => 'Inicio'],
            ['name' => 'end_date', 'label' => 'Fin'],
            ['name' => 'code', 'label' => 'Código'],
            ['name' => 'contractText', 'label' => 'Contrato (html)'],
            //['name' => 'brand_id', 'label' => 'Marca'],
        ]);
        
        CRUD::field('brand_id')->type('select')->model('App\Models\Brand')->attribute('name')->entity('brand'); // notice the name is the foreign key attribute
        
        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        
    }
}
