Feature: Manage a user account
  In order to manage my user account
  As a user
  I need to interact with the account page

  Background:
    Given I am on the homepage
    And I am anonymous
    And I go to "login"
    And I fill in "Username" with "damien"
    And I fill in "Password" with "damien"
    And I press "Log in"

  Scenario: I can access my profile
    When I am on "profile/"
    Then I should see "damien user profile"
    And I should see "Logged in as damien"
    When I follow "damien"
    Then I should see "damien user profile"

  Scenario: I can edit my profile:
    Given I am on "profile/"
    When I follow "Edit profile"
    Then I should be on "profile/edit"
    When I fill in the following:
      | Username         | pandore             |
      | Email            | pandore@khatovar.fr |
      | Current password | damien              |
    And I press "Update"
    Then I should see "The profile has been updated"
    And I should see "Username: pandore"
    And I should see "Email: pandore@khatovar.fr"

  Scenario: I cannot edit my profile without my password
    Given I am on "profile/"
    And I follow "Edit profile"
    When I fill in the following:
      | Username         | pandore |
      | Current password | pandore |
    And I press "Update"
    Then I should see "The entered password is invalid"

  Scenario: I can change my password
    Given I am on "profile/"
    When I follow "Change password"
    Then I should be on "profile/change-password"
    When I fill in the following:
      | Current password    | damien  |
      | New password        | pandore |
      | Repeat new password | pandore |
    And I press "Change password"
    Then I should see "The password has been changed"
    When I follow "Log out"
    And I fill in the following:
      | Username | damien  |
      | Password | pandore |
    And I press "Log in"
    Then I should be on "profile/"
    And I should see "Username: damien"

  Scenario: I cannot change my password without knowing it
    Given I am on "profile/"
    And I follow "Change password"
    When I fill in the following:
      | Current password    | pandore |
      | New password        | pandore |
      | Repeat new password | pandore |
    And I press "Change password"
    Then I should see "The entered password is invalid"

  Scenario: I cannot change my password if I don't confirm the new one
    Given I am on "profile/"
    And I follow "Change password"
    When I fill in the following:
      | Current password    | damien  |
      | New password        | pandore |
      | Repeat new password | pendora |
    And I press "Change password"
    Then I should see "The entered passwords don't match"
