Feature: Edit user profiles
  In order to administrate user accounts
  As an administrator
  I need to be able to edit a user profile

  Background:
    Given I am on the homepage
    And I am anonymous
    And I go to "login"
    And I fill in "Username" with "aurore"
    And I fill in "Password" with "aurore"
    And I press "Log in"

  Scenario: I can edit a user profile
    Given I am on "admin"
    When I follow "Edit" for "damien" profile
    Then I should see "Edit user damien profile"
    When I fill in the following:
      | User name     | pandore           |
      | Email address | pandore@gmail.com |
    And I press "Update"
    Then I should see "User profile has been updated"
    And I should see the users "pandore, freya and lilith"

  Scenario: I cannot edit the profile of the super admin
    When I am on "admin/admin/edit"
    Then I should see "403 Forbidden"
