@end-to-end @fixtures-users
Feature: Manage a user account
  In order to manage my user account
  As a user
  I need to interact with the account page

  Scenario: I can access my profile
    Given I am logged as damien
    When I go to my user profile
    Then I can see my user information

  Scenario: I can edit my profile:
    Given I am logged as damien
    When I change my username and my email
    Then my user information are updated

  Scenario: I cannot edit my profile without my password
    Given I am logged as damien
    When I try to edit my profile with a wrong password
    Then I am notified that the password is invalid

  Scenario: I can change my password
    Given I am logged as damien
    When I change my password
    Then my password is changed

  Scenario: I cannot change my password without knowing it
    Given I am logged as damien
    When I try to change my password without knowing it
    Then I am notified that the password is invalid

  Scenario: I cannot change my password if I don't confirm the new one
    Given I am logged as damien
    When I change my password without confirming it
    Then I am notified that the two passwords are different
