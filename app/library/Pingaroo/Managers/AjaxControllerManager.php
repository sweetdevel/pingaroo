<?php

namespace Pingaroo\Managers;

/**
 * Description of AjaxControllerManager
 *
 * @author Marius
 */
class AjaxControllerManager {
    private $controller;

    public function __construct(&$controller) {
        $this->controller = $controller;
    }
 
    public static function getEmptyPayload() {
        return array(
                    'success'=>false,
                    'code'=>'Code not implemented yet',
                    'message'=>null,
                    'payload'=>null
                );
    }
    
    public static function generateRequestURL($config, $resource, $jsonRequest) {
        $workerUrl = "http://{$_SERVER['HTTP_HOST']}/";
        $workerUrl .= $config->application->baseUri
                    . $resource . '/ajax/' . $jsonRequest;
        
        return $workerUrl;
    }
    
    public function handle($json) {
        $payload = self::getEmptyPayload();
        
        try {
            $request = json_decode($json);
            
            $action = strtoupper($request->action[0]) . substr($request->action, 1);        
            if(strtolower($action) == 'action') {
                $payload['message'] = 'Reserved';

            } else {
                $controllerPayload = $this->controller->{'ajax'.$action}($request->args);
                # !!! THIS CREATES A STANDARD
                # All keys from the controller that do not match the ones
                # from the default payload will be ignonred 
                # and not forwarded in the response
                # !!!
                foreach($payload as $key=>$entry) {
                    if(isset($controllerPayload[$key])) {
                        $payload[$key] = $controllerPayload[$key];                     
                    }
                }
            }
        } catch (\Exception $ex) {
            $payload = array(
                'message' => $ex->getMessage(), 
                'payload' => null
            );
        }
        
        return $payload;
    }
}
