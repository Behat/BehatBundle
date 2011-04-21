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
          Given I am on /_behat/tests/page/page1

        Scenario: I am on/I go to
          Given I should see "Page N1"
          When I go to /_behat/tests/page/page2
          Then I should see "Page N2"
          And I should not see "Page N1"

        Scenario: I follow
          Given I should see "Page N1"
          When I follow "p10"
          Then I should see "Page N10"

        Scenario: Unexistent link follow
          Given I should see "Page N1"
          When I follow "p100"
          Then I should see "Page N100"
      """
    When I run "./console behat:test:path %features_path%/browser.feature -f progress --no-colors --no-time"
    Then It should fail with:
      """
      ...........F-

      (::) failed steps (::)

      01. link with locator: "p100" not found
          In step `When I follow "p100"'.         # Behat/Mink/Integration/steps/mink_steps.php:27
          From scenario `Unexistent link follow'. # features/browser.feature:18

      3 scenarios (2 passed, 1 failed)
      13 steps (11 passed, 1 skipped, 1 failed)
      """

  Scenario: Redirects
    Given a feature named "redirect.feature" with:
      """
      Feature: Redirects
        Redirection scenarios

        Background:
          Given I am on /_behat/tests/page/page1

        Scenario:
          Given I go to /_behat/tests/page/page2
          Then I should see "Page N1"

        Scenario:
          Given I go to /_behat/tests/redirect
          Then I should see "Page N1"
      """
    When I run "./console behat:test:path %features_path%/redirect.feature -f progress --no-colors --no-time"
    Then It should fail with:
      """
      ..F...

      (::) failed steps (::)

      01. Failed asserting that <text> matches PCRE pattern "/Page N1/".
          In step `Then I should see "Page N1"'. # Behat/Mink/Integration/steps/mink_steps.php:62
          From scenario ***.                     # features/redirect.feature:7

      2 scenarios (1 passed, 1 failed)
      6 steps (5 passed, 1 failed)
      """      
