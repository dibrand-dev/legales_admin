<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SignedContractRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SignedContractCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SignedContractCrudController extends CrudController
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
        CRUD::setModel(\App\Models\SignedContract::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/signed-contract');
        CRUD::setEntityNameStrings('Contrato Firmado', 'Contratos Firmados');
    }

    

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        
        if (request()->has('campaign_id')) {
            CRUD::addClause('where', function ($query) {
                // Check if 'filter_param' is present in the query string
                
                // Filter the query based on the value of 'campaign_id'
                $query->where('campaign_id', request()->input('campaign_id'));
                
            });
        }
        
        
        CRUD::setFromDb(); // set columns from db columns.
        CRUD::column('signature')->label('Firma')->type('image')->prefix('/storage/');
        CRUD::column('person_id')
        ->label('Persona')
        ->model('App\Models\Person')
        ->name('person_id')
        ->type('select')
        ->entity('person')
        ->attribute('fullname');

        CRUD::column('campaign_id')
        ->label('Campaña')
        ->model('App\Models\Campaign')
        ->name('campaign_id')
        ->type('select')
        ->entity('campaign')
        ->attribute('name');

        CRUD::column('code')
        ->label('Codigo')
        ->model('App\Models\Campaign')
        ->name('code')
        ->type('select')
        ->entity('campaign')
        ->attribute('code');

        CRUD::addButtonFromModelFunction('line', 'getPdf', 'getPdf', 'beginning');
        
        

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
        CRUD::setValidation(SignedContractRequest::class);
        //CRUD::setFromDb(); // set fields from db columns.
        CRUD::addFields([
            ['name' => 'signed_date', 'label' => 'Fecha Firma']
        ]);
        CRUD::field('signature')
            ->label('Firma')
            ->type('upload')
            ->hint('El tamano debe ser de 500px x 500px')
            ->withFiles([
                'disk' => 'public',
                'path' => 'uploads',
        ]);

        CRUD::field('person_id')
            ->type('select')
            ->model('App\Models\Person')
            ->attribute('fullname')
            ->entity('person')
            ->label('Persona');

        CRUD::field('campaign_id')
            ->type('select')
            ->model('App\Models\Campaign')
            ->attribute('name')
            ->entity('campaign')
            ->label('Campaña');
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
