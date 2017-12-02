Feature: Change users roles
  In order to administrate user accounts
  As an administrator
  I need to be able to change a user roles

  Background:
    Given I am on the homepage
    And I am anonymous
    And I go to "login"
    And I fill in "Username" with "aurore"
    And I fill in "Password" with "aurore"
    And I press "Log in"

  Scenario: I can change a user role
    Given I am on "admin"
    When I follow "Change role" for "damien" profile
    And I select "Viewer" from "Roles"
    And I press "Change"
    Then I should see "User role has been changed"
    And user "damien" should have role "ROLE_VIEWER"

  Scenario: I cannot promote a user as administrator
    Given I am on "admin"
    When I follow "Change role" for "damien" profile
    Then I should see "Base user" in the "select" element
    And I should see "Viewer" in the "select" element
    But I should not see "Administrator" in the "select" element
    And I should not see "Super administrator" in the "select" element

  Scenario: I cannot demote an administrator
    When I am on "admin"
    Then I should not see "Change role" in the table line containing "freya"
    When I am on "admin/freya/role"
    Then I should see "403 Forbidden"
