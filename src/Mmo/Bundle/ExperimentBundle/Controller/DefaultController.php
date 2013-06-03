<?php

namespace Mmo\Bundle\ExperimentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {        
        return $this->render('MmoExperimentBundle:Default:index.html.twig');
  
    }
    
    public function helloAction($name = "world")
    {
        $response = new Response(sprintf('Hello %s', $name));
        return $response;
    }
    
    public function jsonAction() {
        $values = $this->getRequest()->query->all() + $this->getRequest()->request->all();

        $response = new Response(json_encode($values));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
    
    public function headersAction() {
        $headers = $this->getRequest()->headers->all();
        
        $response = new Response(json_encode($headers));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    public function displayHttpRequestBodyAction() {
        $body = $this->getRequest()->getContent();
        $response = new Response($body);
        return $response;
    }
}
