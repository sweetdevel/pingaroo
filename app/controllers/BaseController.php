<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

use Pingaroo\Managers\FormReflectionManager;

class BaseController extends Controller {
    /*
    # --- AJAX ROUTER ---
    # Routes to $this->ajax{$json->action}($json->args)
    public function ajaxAction($json) {
        $ajaxControllerManager = new AjaxControllerManager($this);
        print_r($ajaxControllerManager->handle($json));
        
        $this->view->disable();
    }
    */
    
    public function isValidId($id) {
        $isNumeric = is_numeric($id);
        
        if(!is_numeric($id)) {
            $this->flash->error('The identifier is not numeric.');
        }
        
        return $isNumeric;
    }
    
    /**
     * Validation wrapper.
     * 
     * Returns true if the validation passes.
     * Returns false if validation fails.
     * 
     * @param array $arRequest
     * @return boolean
     */
    public function isValidRequest($validator) {
        $validation = new $validator();

        $messages = $validation->validate($this->request->get());
        if(count($messages)) {
            foreach($messages as $message) {
                $this->flash->error($message->getMessage());
            }
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Returns an Redirect to the given url.
     * 
     * @param string $url
     * @return \Phalcon\Http\Response
     */
    protected function redirect($url, $extraParams = array()) {
        $response = new Response();
        $response->redirect($url);

        $formReflectionManager = new FormReflectionManager($this->flash);                
        $formReflectionManager->add($extraParams + $this->request->get());
        
        return $response;        
    }
}
