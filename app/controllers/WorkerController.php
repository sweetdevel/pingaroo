<?php

use Phalcon\Mvc\Controller;

use Pingaroo\Helpers\PingHelper;

/**
 * Description of Pinger
 *
 * @author Marius
 */
class WorkerController extends BaseController {
    # --- AJAX ROUTER ---
    # Routes to $this->ajax{$json->action}($json->args)
    public function ajaxAction($json) {
        $ajaxControllerManager = new Pingaroo\Managers\AjaxControllerManager($this);
        echo json_encode($ajaxControllerManager->handle($json));
        
        $this->view->disable();
    }
    
    
    # ==============================================
    # =============== AJAX METHODS =================
    # ==============================================

    public function ajaxPingCreate($args) {
        $batchId = $args->batchId;
        $urlId = $args->urlId;
        $proxyId = $args->proxyId;
        $updateBatch = $args->updateBatch;

        $ping = PingHelper::create($batchId, $urlId, $proxyId, $updateBatch);
        if($ping) {
            $payload['success']= true;
            $payload['message']= 'Batch updated succesfully.';
            $payload['payload']= array('id' => $ping->id);            
        } else {
            $payload['success']= false;
            $payload['message']= 'Batch failed to be created.';
            $payload['payload']= array('id' => -1);
        }

        return $payload;
    }
    
    public function ajaxPingReload($args) {
        $id = $args->id;

        $ping = PingHelper::reload($id);
        if($ping) {
            $payload['success']= true;
            $payload['message']= 'Batch reloaded succesfully.';
            $payload['payload']= array('id' => $id);            
        } else {
            $payload['success']= false;
            $payload['message']= 'Batch failed to be reloaded.';
            $payload['payload']= array('id' => -1);
        }
        
        $this->view->disable();
    }
}
