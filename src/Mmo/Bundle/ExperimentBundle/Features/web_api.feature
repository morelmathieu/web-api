Feature: Using Web Api Context with Symfony2Extension
  In order to avoid some cumbersome configurations
  As features tester
  I need to be able to use Web Api definitions with Symfony2Extension

  Scenario: Say hello to "mathieu"
    When I send a GET request to "/hello/mathieu"
    Then the response code should be 200
    And response should contain "mathieu"

  Scenario: Say hello to someone else than "mathieu"
    When I send a POST request to "/hello/thomas"
    Then the response code should be 200
    And response should not contain "mathieu"

  Scenario: Send values and display as json
    When I send a GET request to "/json" with values:
      | first_name | Mathieu |
      | last_name  | MOREL   |
    Then the response code should be 200
    And response should contain json:
      """
        {
            "first_name": "Mathieu",
            "last_name":  "MOREL"
        }
      """

  Scenario: Send form data and display as json
    When I send a POST request to "/json" with form data:
        """
            first_name=Mathieu&last_name=MOREL
        """
    Then the response code should be 200
    And response should contain json:
      """
        {
            "first_name": "Mathieu",
            "last_name":  "MOREL"
        }
      """

  Scenario: Add Header "FROM"
    Given I set header "from" with value "user@example.com"
    When I send a GET request to "/headers"
    Then the response should contain "user@example.com"

  Scenario: Add multiple headers
    Given I set header "from" with value "first_user@example.com"
    And I set header "from" with value "second_user@example.com"
    When I send a GET request to "/headers"
    Then the response should contain "first_user@example.com"
    And the response should contain "second_user@example.com"

  Scenario: Add Header "FROM" with values
    Given I set header "from" with value "user@example.com"
    When I send a GET request to "/headers" with values:
        | some_value | 123 |
    Then the response should contain "user@example.com"

  Scenario: Add Header "FROM" with form data
    Given I set header "from" with value "user@example.com"
    When I send a GET request to "/headers" with form data:
        """ 
            some_value = 123 
        """
    Then the response should contain "user@example.com"

  Scenario: Display http body request
    When I send a GET request to "/display_http_request_body" with body:
       """
            rosebud
       """
    Then the response should contain "rosebud"

  Scenario: Add Header "FROM" with http body request
    Given I set header "from" with value "user@example.com"
    When I send a GET request to "/headers" with body:
        """ 
            whatever
        """
    Then the response should contain "user@example.com"

  Scenario: Authentication
    Given I am authenticating as "user" with "pass" password
    When I send a GET request to "/headers"
    Then the response should contain "Basic dXNlcjpwYXNz"

  Scenario: Print response
    When I send a GET request to "/hello/world"
    Then print response