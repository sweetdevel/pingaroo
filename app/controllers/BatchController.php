<?php

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Tag;

use Pingaroo\Validations\BatchValidation;
use Pingaroo\Managers\FormReflectionManager;
use Pingaroo\Helpers\BatchHelper;

class BatchController extends BaseController { 
    # --- AJAX ROUTER ---
    # Routes to $this->ajax{$json->action}($json->args)
    public function ajaxAction($json) {
        $ajaxControllerManager = new Pingaroo\Managers\AjaxControllerManager($this);
        echo json_encode($ajaxControllerManager->handle($json));
        
        $this->view->disable();
    }
    
    /**
     * The start action, it shows the "search" view
     */
    public function indexAction() {        
        $batchId = $this->request->get('batchId');
        $name = $this->request->get('name');
        $httpCode = $this->request->get('httpCode');
        
        if(!is_numeric($httpCode)) {
            $httpCode = '';
        }
        
        if(is_numeric($batchId)) {
            $batch = Batch::findFirst($batchId);
            if(!empty($batch)) {
                $batch->name = $name?: $batch->name;                
            }
        } else {
            $batch = new Batch;
        }
        
        Tag::setDefaults(array(
            'batchId' => $batchId,
            'name' => $name,
            'httpCode' => $httpCode,
        ));
        
        $this->view->batch = $batch;
        $this->view->batchIds = Batch::find();
        $this->view->pings = $this->searchPings(); 
       
        #$this->view->disable();
    }
    
    /**
     * Execute the "search" based on the criteria sent from the "index"
     * Returning a paginator for the results
     */
    public function searchAction($id) {
        // ...        
    }

    /**
     * Shows the view to create a "new" product
     */
    public function newAction()
    {
        $reflection = new FormReflectionManager($this->flash);
        
        $this->view->name = $reflection->get('name');
        $this->view->urls = Url::find()?: array();
        $this->view->proxies = Proxy::find()?: array();
        $this->view->reflected_proxyId = explode(',', $reflection->get('proxyId'));
        $this->view->reflected_address = explode(',', $reflection->get('address'));
        
        #$this->view->disable();
    }

    /**
     * Shows the view to "edit" an existing product
     */
    public function editAction($id)
    {
        // ...
    }
    
    /**
     * Creates a product based on the data entered in the "new" action
     */
    public function createAction()
    {
        $proxyList = $this->request->get('proxyId');
        
        if(!parent::isValidRequest(new BatchValidation()) || empty($proxyList)) {
            if(empty($proxyList)) {
               $this->flash->notice('Please select at least 1 proxy.');      
            }
            
            return parent::redirect(
                    'batch/new',
                    ['proxyId' => implode(',', $this->request->get('proxyId'))]
            );
        }
        
        try {
            $name = trim($this->request->get('name'));
            $existingBatch = Batch::findFirst(array(
                    'conditions' => 'name = ?0',
                    'bind'       => array($name)
                    )
                );

            if($existingBatch) {
                $this->flash->notice('Batch name allready exists.');
                return parent::redirect(
                            'batch/new',
                            ['proxyId' => implode(',', $this->request->get('proxyId'))]
                       );

            } else {
                $urlId = $this->request->get('address');
                $proxyIds = $this->request->get('proxyId');
                            
                $batch = new Batch;
                $batch->urlId = $urlId;
                $batch->name = $name;
                $batch->save();
                
                # THE BIG KAHUNA :)
                if($this->config->application->debug) {
                    set_time_limit(0);
                }
                BatchHelper::create($this->config, $batch->id, $urlId, $proxyIds);
                
                $this->flash->success('Batch created succesfully.');
            }
            
        } catch (\Exception $ex) {
            $this->flash->error('An error occured when trying to create the new batch.');               
        }

        $returnUrl = isset($batch) ? 'batch?batchId=' . $batch->id : 'batch';
        return $this->redirect($returnUrl);
        
        #$this->view->disable();
    }

    /**
     * Updates a product based on the data entered in the "edit" action
     */
    public function saveAction($id)
    {
        // ..
    }

    /**
     * Deletes an existing product
     */
    public function deleteAction($id)
    {
        if(!parent::isValidId($id)) {
            return parent::redirect('batch');
        }

        $manager = new TransactionManager();
        $transaction = $manager->get();
  
        try {
            $batch = Batch::findFirst($id);
            if($batch) {
                $batch->setTransaction($transaction);
                
                # delete dependencies
                $pings = $batch->getPing();
                foreach($pings as $ping) {
                    $ping->setTransaction($transaction);
                    $ping->delete();   
                }
                
                $batch->delete();
                
                $this->flash->success('Batch deleted succesfully.');
                $transaction->commit();
            } else {
                $this->flash->notice('Batch not found, operation aborted.');
            }
        } catch(\Exception $ex) {
            $this->flash->error('Error deleting batch.');
            $transaction->rollback();
        }
        
        return $this->redirect('batch');
    }

    public function reloadAction($id) {
        if(!parent::isValidId($id)) {
            $returnArg = '';
        } else {
            $batch = Batch::findFirst($id);
            if(!$batch) {
               $this->flash->error('Batch not found');
               $returnArg = '';
            } else {                
                BatchHelper::reload($this->config, $batch);
                
                $returnArg = '?batchId=' . $id;
                $this->flash->success('Reloading pings...'); 
            }
        }
        
        return parent::redirect('batch' . $returnArg);
    }
    
    
    # ==============================================
    # =============== AJAX METHODS =================
    # ==============================================

    public function ajaxRename($args) {
        $id = $args->id;
        $name = $args->name;

        $existingBatch = Batch::findFirst(array(
                            'conditions' => 'name = ?0',
                            'bind'       => array($name)
                            )
                        );

        if(isset($existingBatch->id) && $existingBatch->id != $id) {
            $payload['success']= false;
            $payload['message']= 'Batch exists with same address.';
            $payload['payload']= array('id' => $id);
            
        } else {
            $batch = Batch::findFirst($id);
            $batch->name = $name;
            $batch->save();

            $payload['success']= true;
            $payload['message']= 'Batch updated succesfully.';
            $payload['payload']= array('id' => $id);
        }
        
        return $payload;
    }

    
    # =================================================
    # =============== PRIVATE METHODS =================
    # =================================================    
    
    private function createSearchBuilder() {
        $builder = new Builder();

        $builder->columns([            
            'ping.id as pingId',
            'ping.httpCode',
            'ping.duration',
            
            'b.id as batchId',
            'b.name',
            
            'c.id as countryId',
            'c.code as proxyCountryCode',
            
            'u.id as urlId',
            'u.address as urlAddress',
            
            'pr.id as proxyId',
            'pr.address as proxyAddress'
        ]);
        
        $builder->addFrom('ping');
        $builder->innerJoin('proxy', 'pr.id = ping.proxyId', 'pr');
        $builder->innerJoin('country', 'c.id = pr.countryId', 'c');
        $builder->innerJoin('batch', 'b.id = ping.batchId', 'b');
        $builder->innerJoin('url', 'u.id = b.urlId', 'u');
        
        return $builder;
    }
    
    private function searchPings() {
        $batchId = $this->request->get('batchId');
        $httpCode = $this->request->get('httpCode');
        
        if($batchId && $batchId != -1) {
            $builder = $this->createSearchBuilder();

            if($batchId) {
                $builder->where('b.id = ' . $batchId);
            }

            if(is_numeric($httpCode)) {
                $builder->where('ping.httpCode = ' . $httpCode);
            }
            
            return $builder->getQuery()->execute() ?: array();
        } else {    
            return array();
        }
    }
}
