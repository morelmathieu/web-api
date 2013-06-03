<?php

namespace Mmo\Bundle\ExperimentBundle\Features\Context;

use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
   require_once 'PHPUnit/Autoload.php';
   require_once 'PHPUnit/Framework/Assert/Functions.php';


/**
 * Feature context.
 */
class WebApiContext extends BehatContext //MinkContext if you want to test web
                  implements KernelAwareInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;
    
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;
    
    /**
     * @var array
     */
    private $headers = array();
    
    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /** @BeforeScenario */
    public function setupClient($event) {
        $this->client = $this->kernel->getContainer()->get("test.client");
    }
        
    /**
     * Adds Basic Authentication header to next request.
     *
     * @param string $username
     * @param string $password
     *
     * @Given /^I am authenticating as "([^"]*)" with "([^"]*)" password$/
     */
    public function iAmAuthenticatingAs($username, $password)
    {
        $this->authorization = base64_encode($username.':'.$password);
        $this->headers['Authorization'] = 'Basic '.$this->authorization;
    }
    
    /**
     * Sets a HTTP Header.
     *
     * @param string $name  header name
     * @param string $value header value
     *
     * @Given /^I set header "([^"]*)" with value "([^"]*)"$/
     */
    public function iSetHeaderWithValue($name, $value)
    {
        $this->addHeader($name, $value);
    }
    
    /**
     * @When /^I send a ([A-Z]+) request to "([^"]*)"$/
     */
    public function iSendARequestTo($method, $uri)
    {
        $this->client->request($method, $uri, array(), array(), $this->formatHeadersAsHttp());
    }
    
    /**
     * Sends HTTP request to specific URL with field values from Table.
     *
     * @param string    $method request method
     * @param string    $url    relative url
     * @param TableNode $post   table of post values
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" with values:$/
     */
    public function iSendARequestWithValues($method, $url, TableNode $post)
    {
        $fields = $post->getRowsHash();
        
        $this->client->request($method, $url, $fields, array(), $this->formatHeadersAsHttp());

    }
    
    /**
     * Sends HTTP request to specific URL with raw body from PyString.
     *
     * @param string       $method request method
     * @param string       $url    relative url
     * @param PyStringNode $string request body
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" with body:$/
     */
    public function iSendARequestWithBody($method, $url, PyStringNode $string)
    {
        $this->client->request($method, $url, array(), array(), $this->formatHeadersAsHttp(), $string);
    }
    
    /**
     * Sends HTTP request to specific URL with form data from PyString.
     *
     * @param string       $method request method
     * @param string       $url    relative url
     * @param PyStringNode $string request body
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" with form data:$/
     */
    public function iSendARequestWithFormData($method, $url, PyStringNode $string)
    {
        $string = trim($string);

        parse_str(implode('&', explode("\n", $string)), $fields);

        $this->client->request($method, $url, $fields, array(), $this->formatHeadersAsHttp());
        
    }
    
    /**
     * Checks that response body contains specific text.
     *
     * @param string $text
     *
     * @Then /^(?:the )?response should contain "([^"]*)"$/
     */
    public function theResponseShouldContain($text)
    {
        assertRegExp('/'.preg_quote($text).'/', $this->client->getResponse()->getContent());
    }

    /**
     * Checks that response body doesn't contains specific text.
     *
     * @param string $text
     *
     * @Then /^(?:the )?response should not contain "([^"]*)"$/
     */
    public function theResponseShouldNotContain($text)
    {
        assertNotRegExp('/'.preg_quote($text).'/', $this->client->getResponse()->getContent());
    }
    
    /**
     * @Then /^the response code should be (\d+)$/
     */
    public function theResponseCodeShouldBe($code)
    {
        assertSame(intval($code), $this->client->getResponse()->getStatusCode());        
    }

    /**
     * Checks that response body contains JSON from PyString.
     *
     * @param PyStringNode $jsonString
     *
     * @Then /^(?:the )?response should contain json:$/
 */
    public function theResponseShouldContainJson(PyStringNode $jsonString)
    {
        $etalon = json_decode($jsonString->getRaw(), true);
        $actual = json_decode($this->client->getResponse()->getContent(), true);

        if (null === $etalon) {
            throw new \RuntimeException(
                "Can not convert etalon to json:\n".$jsonString->getRaw()
            );
        }

        assertCount(count($etalon), $actual);
        foreach ($actual as $key => $needle) {
            assertArrayHasKey($key, $etalon);
            assertEquals($etalon[$key], $actual[$key]);
        }
    }
    
    /**
     * Prints last response body.
     *
     * @Then print response
     */
    public function printResponse()
    {
        $request  = $this->client->getRequest();
        $response = $this->client->getResponse();

        $this->printDebug(sprintf("%s %s => %d:\n%s",
            $request->getMethod(),
            $request->getUri(),
            $response->getStatusCode(),
            $response->getContent()
        ));
    }

    
    /**
     * Adds header
     *
     * @param string $header
     */
    protected function addHeader($name, $value)
    {       
        if (!array_key_exists($name, $this->headers)) {
            $this->headers[$name] = array();
        }
        $this->headers[$name][] = $value;
    }
    
    protected function formatHeadersAsHttp() {
        
        $results = array();   
        foreach ($this->headers as $name => $values) {
            $httpName = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
            $results[$httpName] = $values;
        }

        return $results;
    }

}
