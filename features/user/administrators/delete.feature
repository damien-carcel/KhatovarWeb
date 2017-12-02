Feature: Delete a user account
  In order to administrate user accounts
  As an administrator
  I need to be able to delete a user account

  Background:
    Given I am on the homepage
    And I am anonymous
    And I go to "login"
    And I fill in "Username" with "aurore"
    And I fill in "Password" with "aurore"
    And I press "Log in"

  Scenario: I can delete a user
    Given I am on "admin"
    When I press "Delete" for "damien" profile
    Then I should see "The user has been deleted"
    And I should see the users "freya and lilith"

  Scenario: I can delete a user
    Given I am on "admin"
    When I stop following redirections
    And I press "Delete" for "damien" profile
    Then I should get a confirmation email with subject "Account deletion"
    And I start following redirections
