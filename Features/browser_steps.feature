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
          Given I am on "/_behat/tests/page/page1"

        Scenario: I am on/I go to
          Given I should see "Page N1"
          When I go to "/_behat/tests/page/page2"
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
    When I run "./console behat %features_path%/browser.feature -f progress --no-colors --no-time"
    Then It should fail with:
      """
      ...........F-

      (::) failed steps (::)

      01. Link with id|title|alt|text "p100" not found
          In step `When I follow "p100"'.         # FeatureContext::clickLink()
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
          Given I am on "/_behat/tests/page/page1"

        Scenario:
          Given I go to "/_behat/tests/page/page2"
          Then I should see "Page N1"

        Scenario:
          Given I go to "/_behat/tests/redirect"
          Then I should see "Page N1"
      """
    When I run "./console behat %features_path%/redirect.feature -f progress --no-colors --no-time"
    Then It should fail with:
      """
      ..F...

      (::) failed steps (::)

      01. The text "Page N1" was not found anywhere in the text of the current page
          In step `Then I should see "Page N1"'. # FeatureContext::assertPageContainsText()
          From scenario ***.                     # features/redirect.feature:7

      2 scenarios (1 passed, 1 failed)
      6 steps (5 passed, 1 failed)
      """
