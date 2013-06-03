<?php

namespace Mmo\Bundle\ExperimentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MinkController extends Controller
{
    
    public function displayHttpRequestBodyAction() {
        $body = $this->getRequest()->getContent();
        $response = new Response($body);
        return $response;
    }
}
