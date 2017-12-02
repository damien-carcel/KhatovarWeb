Feature: See user profiles
  In order to administrate user accounts
  As an administrator
  I need to be able to see a user profile

  Background:
    Given I am on the homepage
    And I am anonymous
    And I go to "login"
    And I fill in "Username" with "aurore"
    And I fill in "Password" with "aurore"
    And I press "Log in"

  Scenario: I can see a user profile
    Given I am on "admin"
    When I follow "View" for "damien" profile
    Then I should see "damien user profile"
    And I should see "Username: damien"
    And I should see "Email: damien@khatovar.fr"

  Scenario: I cannot see the profile of the super admin
    When I am on "admin/admin/show"
    Then I should see "403 Forbidden"
