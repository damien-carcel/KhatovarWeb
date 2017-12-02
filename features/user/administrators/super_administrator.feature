Feature: Administrate administrators
  In order to administrate users including administrators
  As the super administrator
  I need to be able to administrate any kind of users

  Background:
    Given I am on the homepage
    And I am anonymous
    And I go to "login"
    And I fill in "Username" with "admin"
    And I fill in "Password" with "admin"
    And I press "Log in"

  Scenario: I can list all users but me
    Given I am on "profile/"
    When I follow "Administration page"
    Then I should see "Administration of the users"
    And I should see the users "aurore, damien, freya and lilith"

  Scenario: I can promote a user as administrator
    Given I am on "admin"
    When I follow "Change role" for "damien" profile
    And I select "Administrator" from "Roles"
    And I press "Change"
    Then I should see "User role has been changed"
    And user "damien" should have role "ROLE_ADMIN"

  Scenario: I can demote a user from administrator
    Given I am on "admin"
    When I follow "Change role" for "aurore" profile
    And I select "Base user" from "Roles"
    And I press "Change"
    Then I should see "User role has been changed"
    And user "aurore" should have role "ROLE_USER"

  Scenario: I can change the status of an administrator
    Given I am on "admin"
    When I follow "Deactivate" for "aurore" profile
    Then I should see "User has been deactivated"
    And user "aurore" should be disabled
    When I follow "Activate" for "aurore" profile
    Then I should see "User has been activated"
    And user "aurore" should be enabled
