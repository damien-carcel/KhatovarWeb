Feature: Change users status
  In order to administrate user accounts
  As an administrator
  I need to be able to activate and deactivate users

  Background:
    Given I am on the homepage
    And I am anonymous
    And I go to "login"
    And I fill in "Username" with "aurore"
    And I fill in "Password" with "aurore"
    And I press "Log in"

  Scenario: I can deactivate a user
    Given I am on "admin"
    When I follow "Deactivate" for "damien" profile
    Then I should see "User has been deactivated"
    And user "damien" should be disabled

  Scenario: I can activate a user
    Given I am on "admin"
    When I follow "Activate" for "lilith" profile
    Then I should see "User has been activated"
    And user "lilith" should be enabled

  Scenario: I cannot change an administrator status
    When I am on "admin"
    Then I should not see "Deactivate" in the table line containing "freya"
    When I am on "admin/freya/status"
    Then I should see "403 Forbidden"
