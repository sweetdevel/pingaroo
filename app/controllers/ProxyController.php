<?php

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

use Pingaroo\Managers\FormReflectionManager;
use Pingaroo\Validations\ProxyValidation;

class ProxyController extends BaseController {

    /**
     * The start action, it shows the "search" view
     */
    public function indexAction() {
        $proxies = Proxy::find();
        $this->view->proxies = $proxies;
        
        #$this->view->disable();
    }
    
    /**
     * Execute the "search" based on the criteria sent from the "index"
     * Returning a paginator for the results
     */
    public function searchAction($id) {
        if(!parent::isValidId($id)) {
            parent::redirect('proxy');
        }
       
        $proxy = Proxy::findFirst($id);
        if($proxy) {
           $this->view->proxy = $proxy;            
        } else {
            $this->flash->error('Identifier not found.');
            return parent::redirect('proxy');
        }

        #$this->view->disable();        
    }

    /**
     * Shows the view to create a "new" product
     */
    public function newAction()
    {
        $reflection = new FormReflectionManager($this->flash);

        $proxy = new Proxy;
        $proxy->id = $reflection->get('id');
        $proxy->countryId = $reflection->get('countryId');
        $proxy->address = $reflection->get('address');
    
        $countries = Country::find();
        
        $this->view->proxy = $proxy;
        $this->view->countries = $countries;
        
        #$this->view->disable();        
    }

    /**
     * Shows the view to "edit" an existing product
     */
    public function editAction($id)
    {
        if(!parent::isValidId($id)) {
            return parent::redirect('proxy');
        }
        
        $proxy = Proxy::findFirst($id);
        if($proxy) {
           $reflection = new FormReflectionManager($this->flash);
           
           $proxy->countryId = $reflection->get('countryId') ?: $proxy->countryId; 
           $proxy->address = $reflection->get('address') ?: $proxy->address; 
           
           $this->view->proxy = $proxy;
           $this->view->countries = Country::find();
        } else {
            $this->flash->error('Identifier not found.');
            return parent::redirect('proxy');
        }

        #$this->view->disable();
    }
    
    /**
     * Creates a product based on the data entered in the "new" action
     */
    public function createAction()
    {
        if(!parent::isValidRequest(new ProxyValidation())) {
            return parent::redirect('proxy/new');
        }
        
        try {
            $address = trim($this->request->get('address'));
            $existingProxy = Proxy::findFirst(array(
                                'conditions' => 'address = ?0',
                                'bind'       => array($address)
                                )
                            );
            
            if($existingProxy) {
                $this->flash->notice('Proxy address allready exists.');
                return parent::redirect('proxy/new');
                
            } else {
                $proxy = new Proxy;
                $proxy->countryId = $this->request->get('countryId');
                $proxy->address = $address;
                $proxy->save();

                $this->flash->success('Proxy created succesfully.');
            }
                        
        } catch (Exception $ex) {
            $this->flash->error('An error occured when trying to create the new proxy.');               
        }

        return $this->redirect('proxy');
 
        #$this->view->disable();
    }

    /**
     * Updates a product based on the data entered in the "edit" action
     */
    public function saveAction($id)
    {
        if(!parent::isValidId($id) || !parent::isValidRequest(new ProxyValidation())) {
            return parent::redirect('proxy/edit/' . $id);
        }
        
        try {
            $address = trim($this->request->get('address'));
            $existingProxy = Proxy::findFirst(array(
                                    'conditions' => 'address = ?0',
                                    'bind'       => array($address)
                                    )
                                );

            if(isset($existingProxy->id) && $existingProxy->id != $id) {
                $this->flash->notice('Proxy exists with same address.');
                return parent::redirect('proxy/edit/' . $id);
            
            } else {
                $proxy = Proxy::findFirst($id);
                $proxy->countryId = $this->request->get('countryId');
                $proxy->address = $address;
                $proxy->save();

                $this->flash->success('Proxy updated succesfully.');                
            }            

        } catch(\Exception $ex) {
           $this->flash->error('An error occured when trying to update the proxy.');            
        }
        
        return $this->redirect('proxy');
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
            $proxy = Proxy::findFirst($id);
            if($proxy) {
                $proxy->setTransaction($transaction);
 
                $pings = $proxy->getPing();
                foreach($pings as $ping) {
                    $ping->setTransaction($transaction);
                    $ping->delete();
                }
                $proxy->delete();
                
                $this->flash->success('Proxy deleted succesfully.');
                $transaction->commit();
                
            } else {
                $this->flash->notice('Proxy not found, operation aborted.');
            }
        } catch(\Exception $ex) {
            $this->flash->error('Error deleting proxy.');
            $transaction->rollback();
        }
        
        return $this->redirect('proxy');
    }

}
