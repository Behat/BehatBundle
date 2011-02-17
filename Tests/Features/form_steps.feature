Feature: Form Steps
  In order to write web features for Symfony2
  As a Symfony2 developer
  I must be able to send forms with simple steps

  Background:
    Given basic BehatBundle environment

  Scenario: Simple Browsing
    Given a feature named "form.feature" with:
      """
      Feature: Response Steps
        Basic Response Steps

        Background:
          Given I am on /behat/tests/form

        Scenario: Simple Form Send
          Given I fill in "name" with "ever"
          And I fill in "age" with "23"
          And I select "programmer" from "speciality"
          When I press "Send spec info" in user form
          Then I should see "POST recieved"
          And I should see "ever is 23 years old programmer"
      """
    When I run "./console behat:test:path %features_path%/form.feature -f progress --no-colors --no-time"
    Then It should pass with:
      """
      .......
      
      1 scenario (1 passed)
      7 steps (7 passed)
      """

