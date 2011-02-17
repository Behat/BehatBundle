Feature: Response Steps
  In order to write web features for Symfony2
  As a Symfony2 developer
  I must be able to check response status with simple steps

  Background:
    Given basic BehatBundle environment

  Scenario: Simple Browsing
    Given a feature named "response.feature" with:
      """
      Feature: Response Steps
        Basic Response Steps

        Background:
          Given I am on /behat/tests/page/page1

        Scenario: 200 Status Code
          Given I should see "Page N1"
          When I go to /behat/tests/page/page2
          Then Response status code is 200

        Scenario: 404 Status Code
          Given I should see "Page N1"
          When I go to /behat/tests/UNKNOWN
          Then Response status code is 404

        Scenario: I should see
          Given I should see "Page N1"
          When I go to /behat/tests/page/page34
          Then I should see "Page N34"

        Scenario: I should not see
          Given I should not see "Page N34"
          When I go to /behat/tests/page/page34
          Then I should not see "Page N1"
          And I should not see "Page N34"

        Scenario: I should see element
          Given I should not see "Page N34"
          When I go to /behat/tests/page/page34
          Then I should see element "ul > li > a"
          And I should see element "ul > li > a > p"
      """
    When I run "./console behat:test:path %features_path%/response.feature -f progress --no-colors --no-time"
    Then It should fail with:
      """
      ................F....F
      
      (::) failed steps (::)
      
      01. Failed asserting that <text> does not match PCRE pattern "/Page N34/".
          In step `And I should not see "Page N34"'. # features/steps/response_steps.php:21
          From scenario `I should not see'.          # features/response.feature:22
      
      02. Failed asserting that <boolean:false> is true.
          In step `And I should see element "ul > li > a > p"'. # features/steps/response_steps.php:25
          From scenario `I should see element'.                 # features/response.feature:28
      
      5 scenarios (3 passed, 2 failed)
      22 steps (20 passed, 2 failed)
      """

  Scenario: Redirects
    Given a feature named "redirect.feature" with:
      """
      Feature: Redirects
        Redirection scenarios

        Background:
          Given I am on /behat/tests/page/page1

        Scenario:
          When I go to /behat/tests/page/page2
          Then I was not redirected

        Scenario:
          Given I go to /behat/tests/redirect
          Then I was redirected
      """
    When I run "./console behat:test:path %features_path%/redirect.feature -f progress --no-colors --no-time"
    Then It should pass with:
      """
      ......
      
      2 scenarios (2 passed)
      6 steps (6 passed)
      """      

