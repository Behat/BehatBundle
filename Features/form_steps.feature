Feature: Form Steps
  In order to write web features for Symfony2
  As a Symfony2 developer
  I must be able to send forms with simple steps

  Background:
    Given basic BehatBundle environment

  @javascript
  Scenario: Javascript Browsing
    Given a feature named "form.feature" with:
      """
      Feature: Response Steps
        Basic Response Steps

        Background:
          Given I am on "/_behat/tests/form"

        @javascript
        Scenario: Simple Form Send
          Given I fill in "name" with "ever"
          And I fill in "age" with "23"
          And I select "programmer" from "speciality"
          When I press "Send spec info"
          Then I should see "POST recieved"
          And I should see "ever is 23 years old programmer"
      """
    When I run "./console behat %features_path%/form.feature -f progress --no-colors --no-time"
    Then It should pass with:
      """
      .......

      1 scenario (1 passed)
      7 steps (7 passed)
      """
