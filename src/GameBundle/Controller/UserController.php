<?php

namespace GameBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\Request;

class UserController extends FOSRestController
{
    /**
     * @Post("/user/")
     */
    public function userAction()
    {
        $request = Request::createFromGlobals();
        $body = json_decode($request->getContent());
        $service = $this->get('user.service');
        $view = null;
        $jsonValidation = array();
        
        if (!isset($body->email))
            $jsonValidation[] = 'email is required on json body';
        
        if (!isset($body->password))
            $jsonValidation[] = 'password is required on json body';
        
        if ($jsonValidation)
        {
            $view = $this->view($jsonValidation, 400);
        }
        else
        {
            $result = $service->login($body->email, $body->password);
            
            if ($result['success'])
            {
                $view = $this->view([
                    'userHash' => $result['userHash']
                ], 200);
            }
            else
            {
                $view = $this->view([
                    'error' => $result['message']
                ], 400);
            }
        }
        
        return $this->handleView($view);
    }
}
