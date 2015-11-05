<?php

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

use Pingaroo\Validations\UrlValidation;
use Pingaroo\Managers\FormReflectionManager;

class UrlController extends BaseController {

    /**
     * The start action, it shows the "search" view
     */
    public function indexAction() {
        $urls = Url::find();
        $this->view->urls = $urls;
        
        #$this->view->disable();
    }
    
    /**
     * Execute the "search" based on the criteria sent from the "index"
     * Returning a paginator for the results
     */
    public function searchAction($id) {
        if(!parent::isValidId($id)) {
            parent::redirect('url');
        }
       
        $url = Url::findFirst($id);
        if($url) {
           $this->view->url = $url;            
        } else {
            $this->flash->error('Identifier not found.');
            return parent::redirect('url');
        }

        #$this->view->disable();        
    }

    /**
     * Shows the view to create a "new" product
     */
    public function newAction()
    {
        $reflection = new FormReflectionManager($this->flash);

        $url = new Url;
        $url->id = $reflection->get('id');
        $url->address = $reflection->get('address');
    
        $this->view->url = $url;
    
        #$this->view->disable();        
    }

    /**
     * Shows the view to "edit" an existing product
     */
    public function editAction($id)
    {
        if(!parent::isValidId($id)) {
            return parent::redirect('url');
        }
        
        $url = Url::findFirst($id);
        if($url) {
           $reflection = new FormReflectionManager($this->flash);
  
           $url->address = $reflection->get('address') ?: $url->address; 
           $this->view->url = $url;
        } else {
            $this->flash->error('Identifier not found.');
            return parent::redirect('url');
        }

        #$this->view->disable();
    }
    
    /**
     * Creates a product based on the data entered in the "new" action
     */
    public function createAction()
    {
        if(!parent::isValidRequest(new UrlValidation())) {
            return parent::redirect('url/new');
        }
            
        try {
            $address = trim($this->request->get('address'));

            $existingUrl = Url::findFirst(array(
                                'conditions' => 'address = ?0',
                                'bind'       => array($address)
                                )
                            );
            
            if($existingUrl) {
                $this->flash->notice('Url address allready exists.');
                return parent::redirect('url/new');
                                            
            } else {
                $url = new Url;
                $url->address = $address;
                $url->save();

                $this->flash->success('Url created succesfully.');
            }
            
        } catch (Exception $ex) {
            $this->flash->error('An error occured when trying to create the new url.');               
        }

        return parent::redirect('url');
 
        #$this->view->disable();
    }

    /**
     * Updates a product based on the data entered in the "edit" action
     */
    public function saveAction($id)
    {
        if(!parent::isValidId($id) || !parent::isValidRequest(new UrlValidation())) {
            return parent::redirect('url/edit/' . $id);
        }
        
        try {
            $address = $this->request->get('address');
            $existingUrl = Url::findFirst(array(
                                'conditions' => 'address = ?0',
                                'bind'       => array($address)
                                )
                            );

            if(isset($existingUrl->id) && $existingUrl->id != $id) {
                $this->flash->notice('Url exists with same address.');
                return parent::redirect('url/edit/' . $id);
            
            } else {
                $url = Url::findFirst($id);
                $url->address = $address;
                $url->save();

                $this->flash->success('Url updated succesfully.');                
            }
           
        } catch(\Exception $ex) {
           $this->flash->error('An error occured when trying to update the url.');            
        }
        
        return $this->redirect('url');
    }

    /**
     * Deletes an existing product
     */
    public function deleteAction($id)
    {
        if(!parent::isValidId($id)) {
            return parent::redirect('url');
        }

        $manager = new TransactionManager();
        $transaction = $manager->get();
  
        try {
            $url = Url::findFirst($id);
            if($url) {
                $url->setTransaction($transaction);
 
                $batches = $url->getBatch();
                foreach($batches as $batch) {
                    $batch->setTransaction($transaction);
                    
                    $pings = $batch->getPing();
                    foreach($pings as $ping) {
                        $ping->setTransaction($transaction);
                        $ping->delete();
                    }
                    
                    $batch->delete();
                }
                $url->delete();
                
                $this->flash->success('Url deleted succesfully.');
                $transaction->commit();
                
            } else {
                $this->flash->notice('Url not found, operation aborted.');
            }
        } catch(\Exception $ex) {
            $this->flash->error('Error deleting url.');
            $transaction->rollback($ex->getMessage());
        }
        
        return $this->redirect('url');
    }
}
