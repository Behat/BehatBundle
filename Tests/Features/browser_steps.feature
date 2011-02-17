Feature: Browser Steps
  In order to write web features for Symfony2
  As a Symfony2 developer
  I must be able to write basic browser interactions in semantic steps

  Background:
    Given basic BehatBundle environment

  Scenario: Simple Browsing
    Given a feature named "browser.feature" with:
      """
      Feature: Browser Steps
        Basic Browser Steps

        Background:
          Given I am on /behat/tests/page/page1

        Scenario: I am on/I go to
          Given I should see "Page N1"
          When I go to /behat/tests/page/page2
          Then I should see "Page N2"
          And I should not see "Page N1"

        Scenario: I follow
          Given I should see "Page N1"
          When I follow "p10"
          Then I should see "Page N10"

        Scenario: I click
          Given I should see "Page N1"
          When I click "p0" link
          Then I should see "Page N0"
          And I should not see "Page N1"

        Scenario: Unexistent click
          Given I should see "Page N1"
          When I click "p100" link
          Then I should see "Page N100"

        Scenario: Backward
          Given I click the "p22" link
          And I should see "Page N22"
          When I go back
          Then I should see "Page N1"

        Scenario: Forward
          Given I click the "p22" link
          And I should see "Page N22"
          When I go back
          And I go forward
          Then I should see "Page N22"
      """
    When I run "./console behat:test:path %features_path%/browser.feature -f progress --no-colors --no-time"
    Then It should fail with:
      """
      ................F-...........
      
      (::) failed steps (::)
      
      01. The current node list is empty.
          In step `When I click "p100" link'. # features/steps/browser_steps.php:22
          From scenario `Unexistent click'.   # features/browser.feature:24
      
      6 scenarios (5 passed, 1 failed)
      29 steps (27 passed, 1 skipped, 1 failed)
      """

  Scenario: Redirects
    Given a feature named "redirect.feature" with:
      """
      Feature: Redirects
        Redirection scenarios

        Background:
          Given I am on /behat/tests/page/page1

        Scenario:
          Given I go to /behat/tests/page/page2
          When I follow redirect
          Then I should see "Page 1"

        Scenario:
          Given I go to /behat/tests/redirect
          When I follow redirect
          Then I should see "Page N1"
      """
    When I run "./console behat:test:path %features_path%/redirect.feature -f progress --no-colors --no-time"
    Then It should fail with:
      """
      ..F-....
      
      (::) failed steps (::)
      
      01. The request was not redirected.
          In step `When I follow redirect'. # features/steps/browser_steps.php:38
          From scenario ***.                # features/redirect.feature:7
      
      2 scenarios (1 passed, 1 failed)
      8 steps (6 passed, 1 skipped, 1 failed)
      """      

  Scenario: POST|PUT|DELETE sends
    Given a feature named "send.feature" with:
      """
      Feature: POST|PUT|DELETE
        Post scenarios

        Background:
          Given I am on /behat/tests/page/page1

        Scenario:
          Given I send PUT to /behat/tests/submit with:
            | name    | age | speciality |
            | everzet | 22  | programmer |
          Then I should see "PUT recieved"
          And I should see "everzet is 22 years old programmer"

        Scenario:
          Given I send DELETE to /behat/tests/submit with:
            | name   | age | speciality |
            | antono | 30  | rubyist    |
          Then I should see "DELETE recieved"
          And I should see "antono is 30 years old rubyist"
      """
    When I run "./console behat:test:path %features_path%/send.feature -f progress --no-colors --no-time"
    Then It should pass with:
      """
      ........
      
      2 scenarios (2 passed)
      8 steps (8 passed)
      """

