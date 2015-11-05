<?php

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

use Pingaroo\Validations\CountryValidation;
use Pingaroo\Managers\FormReflectionManager;

class CountryController extends BaseController {

    /**
     * The start action, it shows the "search" view
     */
    public function indexAction() {
        $countries = Country::find();
        $this->view->countries = $countries;
        
        #$this->view->disable();
    }
    
    /**
     * Execute the "search" based on the criteria sent from the "index"
     * Returning a paginator for the results
     */
    public function searchAction($id) {
        if(!parent::isValidId($id)) {
            parent::redirect('country');
        }
       
        $country = Country::findFirst($id);
        if($country) {
           $this->view->country = $country;            
        } else {
            $this->flash->error('Identifier not found.');
            return parent::redirect('country');
        }

        #$this->view->disable();        
    }

    /**
     * Shows the view to create a "new" product
     */
    public function newAction()
    {
        $reflection = new FormReflectionManager($this->flash);

        $country = new Country;
        $country->id = $reflection->get('id');
        $country->code = $reflection->get('code');
        $country->name = $reflection->get('name');
    
        $this->view->country = $country;
    
        #$this->view->disable();        
    }

    /**
     * Shows the view to "edit" an existing product
     */
    public function editAction($id)
    {
        if(!parent::isValidId($id)) {
            return parent::redirect('country');
        }
        
        $country = Country::findFirst($id);
        if($country) {
           $reflection = new FormReflectionManager($this->flash);
           $country->code = $reflection->get('code') ?: $country->code; 
           $country->name = $reflection->get('name') ?: $country->name; 
           
           $this->view->country = $country;
        } else {
            $this->flash->error('Identifier not found.');
            return parent::redirect('country');
        }

        #$this->view->disable();
    }
    
    /**
     * Creates a product based on the data entered in the "new" action
     */
    public function createAction()
    {
        if(!parent::isValidRequest(new CountryValidation())) {
            return parent::redirect('country/new');
        }
        
        try {
            $code = trim($this->request->get('code'));
            $existingCountry = Country::findFirst(array(
                                    'conditions' => 'code = ?0',
                                    'bind'       => array($code)
                                    )
                                );
            
            if($existingCountry) {
                $this->flash->notice('Country with code <b>' . $code . '</b> address allready exists.');
                return parent::redirect('country/new');
                                            
            } else {
                $country = new Country;
                $country->code = $code;
                $country->name = trim($this->request->get('name'));
                $country->save();

                $this->flash->success('Country created succesfully.');
            }

        } catch (Exception $ex) {
            $this->flash->error('An error occured when trying to create the new Country.');               
        }

        return $this->redirect('country');
 
        #$this->view->disable();
    }

    /**
     * Updates a product based on the data entered in the "edit" action
     */
    public function saveAction($id)
    {
        if(!parent::isValidId($id) || !parent::saveAction(new CountryValidation())) {
            return parent::redirect('country/edit/' . $id);
        }
        
        try {
            $code = trim($this->request->get('code'));
            $existingCountry = Country::findFirst(array(
                                    'conditions' => 'code = ?0',
                                    'bind'       => array($code)
                                    )
                                );

            if(isset($existingCountry->id) && $existingCountry->id != $id) {
                $this->flash->notice('Country exists with same code.');
                return parent::redirect('country/edit/' . $id);
            
            } else {
                $country = Country::findFirst($id);
                $country->code = $code;
                $country->name = trim($this->request->get('name'));
                $country->save();

                $this->flash->success('Country updated succesfully.');                
            }
            
        } catch(\Exception $ex) {
           $this->flash->error('An error occured when trying to update the country.');            
        }
        
        return $this->redirect('country');
    }

    /**
     * Deletes an existing product
     */
    public function deleteAction($id)
    {
        if(!parent::isValidId($id)) {
            return parent::redirect('country');
        }

        $manager = new TransactionManager();
        $transaction = $manager->get();
  
        try {
            $country = Country::findFirst($id);
            if($country) {
                $country->setTransaction($transaction);
                
                $proxies = $country->getProxy();
                foreach($proxies as $proxy) {
                    $proxy->setTransaction($transaction);
                                        
                    $pings = $proxy->getPing();
                    foreach($pings as $ping) {
                        $proxy->setTransaction($transaction);
                        $ping->delete();
                    }
                  
                    $proxy->delete();
                }
                $country->delete();
                
                $this->flash->success('Country deleted succesfully.');
                $transaction->commit();
                
            } else {
                $this->flash->notice('Country not found, operation aborted.');
            }
        } catch(\Exception $ex) {
            $this->flash->error('Error deleting country.');
            $transaction->rollback();
        }
        
        return $this->redirect('country');
    }

}
