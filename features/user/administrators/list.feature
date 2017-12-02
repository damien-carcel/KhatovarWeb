Feature: Manage user accounts
  In order to administrate user accounts
  As an administrator
  I need to interact with the administration page

  Background:
    Given I am on the homepage
    And I am anonymous
    And I go to "login"
    And I fill in "Username" with "aurore"
    And I fill in "Password" with "aurore"
    And I press "Log in"

  Scenario: I can see all users on the admin page
    Given I am on "profile/"
    When I follow "Administration page"
    Then I should see "Administration of the users"
    And I should see the users "damien, freya and lilith"

  Scenario: A regular user should not be able to access the admin page
    Given I am on "admin"
    And I follow "Log out"
    And I fill in "Username" with "damien"
    And I fill in "Password" with "damien"
    When I press "Log in"
    Then I should be on "admin/"
    And I should see "403 Forbidden"
