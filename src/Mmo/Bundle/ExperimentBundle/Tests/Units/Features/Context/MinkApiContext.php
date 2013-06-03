<?php

namespace Mmo\Bundle\ExperimentBundle\Tests\Units\Features\Context;

use atoum\AtoumBundle\Test\Units;

class MinkApiContext extends Units\Test
{
    public function beforeTestMethod($method) {
        $this->client = new \mock\Symfony\Component\BrowserKit\Client;
        $this->context = new \Mmo\Bundle\ExperimentBundle\Features\Context\MinkApiContext();
        $this->context->setClient($this->client);
    }
    
    public function testIAmOnHomepage()
    {
        $this->client->getMockController()->request = function() {};
        
        $this->context->iAmOnHomepage();
        
        $this
            ->mock($this->client)
            ->call('request')
            ->withArguments('/', 'GET')
            ->once(); 
    }
    
    public function testVisit()
    {
        $this->client->getMockController()->request = function() {};
        
        $this->context->visit('/hello');
        
        $this
            ->mock($this->client)
            ->call('request')
            ->withArguments('/hello', 'GET')
            ->once();
    }
    
    public function testReload()
    {
        $this->client->getMockController()->reload = function() {};
        
        $this->context->reload();
        
        $this
            ->mock($this->client)
            ->call('reload')
            ->once();
    }
    
    public function testBack()
    {
        $this->client->getMockController()->back = function() {};
        
        $this->context->back();
        
        $this
            ->mock($this->client)
            ->call('back')
            ->once();
    }

    public function testForward()
    {
        $this->client->getMockController()->forward = function() {};
        
        $this->context->forward();
        
        $this
            ->mock($this->client)
            ->call('forward')
            ->once();
    }
    
    public function testPressButton()
    {
        
    }
    
}

