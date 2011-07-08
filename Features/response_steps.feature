Feature: Response Steps
  In order to write web features for Symfony2
  As a Symfony2 developer
  I must be able to check response status with simple steps

  Background:
    Given basic BehatBundle environment

  Scenario: Response checking
    Given a feature named "response.feature" with:
      """
      Feature: Response Steps
        Basic Response Steps

        Background:
          Given I am on "/_behat/tests/page/page1"

        Scenario: 200 Status Code
          Given I should see "Page N1"
          When I go to "/_behat/tests/page/page2"
          Then the response status code should be 200

        Scenario: 404 Status Code
          Given I should see "Page N1"
          When I go to "/_behat/tests/UNKNOWN"
          Then the response status code should be 404

        Scenario: I should see
          Given I should see "Page N1"
          When I go to "/_behat/tests/page/page34"
          Then I should see "Page N34"

        Scenario: I should not see
          Given I should not see "Page N34"
          When I go to "/_behat/tests/page/page34"
          Then I should not see "Page N1"
          And I should not see "Page N34"

        Scenario: I should see element
          Given I should not see "Page N34"
          When I go to "/_behat/tests/page/page34"
          Then I should see an "ul > li > a" element
          And I should see a "ul > li > a > p" element
      """
    When I run "./console behat %features_path%/response.feature -f progress --no-colors --no-time"
    Then It should fail with:
      """
      ................F....F

      (::) failed steps (::)

      01. The text "Page N34" appears in the text of this page, but it should not.
          In step `And I should not see "Page N34"'. # FeatureContext::assertPageNotContainsText()
          From scenario `I should not see'.          # features/response.feature:22

      02. Element matching css "ul > li > a > p" not found
          In step `And I should see a "ul > li > a > p" element'. # FeatureContext::assertElementOnPage()
          From scenario `I should see element'.                   # features/response.feature:28

      5 scenarios (3 passed, 2 failed)
      22 steps (20 passed, 2 failed)
      """
