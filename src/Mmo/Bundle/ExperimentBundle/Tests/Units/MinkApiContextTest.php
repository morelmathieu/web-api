<?php 

//namespace Mmo\Bundle\ExperimentBundle\Tests\Units;
//
//use atoum\AtoumBundle\Test\Units;
//
//class MinkApiContextTest extends \PHPUnit_Framework_TestCase
//{
//    
//    /**
//     * @var \Mmo\Bundle\ExperimentBundle\Features\Context\MinkApiContext
//     */
//    private $context;
//    
//    /**
//     * @var \PHPUnit_Framework_MockObject_MockObject
//     */
//    private $client;
//    
//    public function setUp() 
//    {       
//        $this->context = new \Mmo\Bundle\ExperimentBundle\Features\Context\MinkApiContext();
//        $this->client = $this->getMockForAbstractClass('\Symfony\Component\BrowserKit\Client');
//        $this->context->setClient($this->client);
//    }
//    
//    public function testIAmOnHomepage()
//    {
//        
//        $this->client->expects($this->once())
//                ->method('request')
//                ->with($this->equalTo('/'), $this->equalTo('GET'));
//        
//        $this->context->iAmOnHomepage();
//    }
//}
