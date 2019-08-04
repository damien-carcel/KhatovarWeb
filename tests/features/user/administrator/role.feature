@end-to-end @fixtures-users
Feature: Change users roles
  In order to administrate user accounts
  As an administrator
  I need to be able to change a user role

  Scenario: I can change a user role
    Given I am logged as an administrator
    When I change the role of damien for "Lecture seule"
    Then the user damien should be a viewer

  Scenario: I cannot promote a user as administrator
    Given I am logged as an administrator
    When I try to change damien role
    Then It cannot be promoted to administrator

  Scenario: I cannot demote an administrator
    When I am logged as an administrator
    Then I cannot demote another administrator
