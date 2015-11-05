<?php

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Tag;

use Pingaroo\Helpers\PingHelper;

class PingController extends BaseController {
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
        $searchParams = [
            'ping.batchId'=>'batchId',
            'ping.proxyId'=>'proxyId',
            'b.urlId'=>'urlId'
        ];

        Tag::setDefaults(array(
            'urlId' => $this->request->get('urlId'),
            'proxyId' => $this->request->get('proxyId'),
            'pingId' => $this->request->get('pingId'),
            'batchId' => $this->request->get('batchId'),
        ));
                
        $this->view->pings = $this->searchPings($searchParams);
        $this->view->proxies = Proxy::find();
        $this->view->urls = Url::find();
        $this->view->batches = Batch::find();
        
        #$this->view->disable();
    }
    
    /**
     * Execute the "search" based on the criteria sent from the "index"
     * Returning a paginator for the results
     */
    public function searchAction($id) {
        if(!parent::isValidId($id)) {
            parent::redirect('ping');
        }
       
        $builder = $this->createSearchBuilder();
        $builder->where('ping.id = ' . $id);

        $ping = $builder->getQuery()->getSingleResult();
        
        if($ping) {
           $this->view->ping = $ping;            
        } else {
            $this->flash->error('Identifier not found.');
            return parent::redirect('ping');
        }

        #$this->view->disable();        
    }

    /**
     * Shows the view to create a "new" product
     */
    public function newAction()
    {
        // ...
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
        // ...
    }

    /**
     * Updates a product based on the data entered in the "edit" action
     */
    public function saveAction($id)
    {
        // ...
    }

    /**
     * Deletes an existing product
     */
    public function deleteAction($id)
    {
        // ...
    }
    
    
    # ==============================================
    # =============== AJAX METHODS =================
    # ==============================================
    
    public function ajaxDelete($args) {
        $manager = new TransactionManager();
        $transaction = $manager->get();
  
        $id = $args->id;
        
        try {
            $ping = Ping::findFirst($id);
            if($ping) {
                $ping->setTransaction($transaction);
                $ping->delete();
                
                $transaction->commit();

                $payload['success']= true;
                $payload['message']= 'Ping deleted succesfully.';
            } else {
                $payload['success']= false;
                $payload['message']= 'Ping not found.';
            }
            
            $payload['payload']= array('id' => $id);
        } catch(\Exception $ex) {
            $transaction->rollback();
            
            $payload['success']= false;
            $payload['message']= 'Error deleting ping.';
            $payload['payload']= array('id' => $id);
        }
        
        return $payload;
    }
    
    public function ajaxReload($args) {
        $manager = new TransactionManager();
        $transaction = $manager->get();
  
        $id = $args->id;
        
        try {
            $result = PingHelper::reload($id);
            if($result) {
                $payload['success']= true;
                $payload['message']= 'Ping reloaded succesfully.';
                $payload['payload']= array(
                    'httpCode' => $result['httpCode'],
                    'duration' => $result['duration'],
                    'error' => $result['error']
                );
            } else {
                $payload['success']= false;
                $payload['message']= 'Ping not found.';
            }
            
            $payload['payload'] += array('id' => $id);
        } catch(\Exception $ex) {
            $transaction->rollback();
            
            $payload['success']= false;
            $payload['message']= 'Error reloading ping.';
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
            'ping.id',
            'ping.batchId',
            'ping.proxyId',
            'ping.httpCode',
            'ping.duration',
            'ping.createdAt',
            'ping.updatedAt',
            'ping.error',
            
            'b.urlId',
            'b.name',
            
            'u.address as urlAddress',
            
            'pr.address as proxyAddress'
        ]);
        
        $builder->addFrom('ping');
        $builder->innerJoin('proxy', 'pr.id = ping.proxyId', 'pr');
        $builder->innerJoin('batch', 'b.id = ping.batchId', 'b');
        $builder->innerJoin('url', 'u.id = b.urlId', 'u');
        
        return $builder;
    }
    
    private function searchPings($searchParams) {
        if($this->request->get($searchParams[key($searchParams)])) {
            $builder = $this->createSearchBuilder();

            foreach($searchParams as $abbreviation=>$columnName) {
                $value = $this->request->get($columnName);
                if(!empty($value) && $value != '-1') {
                    $searchValues = is_array($value) ? implode(',', $value) : $value;
                    #var_dump($searchValues);

                    $builder->andWhere($abbreviation . ' IN ( ' . $searchValues . ')');
                }
            }
            
            return $builder->getQuery()->execute() ?: array();
            
        } else {
            $this->flash->notice('Please filter your search.');    
            return array();
        }
    }
}
