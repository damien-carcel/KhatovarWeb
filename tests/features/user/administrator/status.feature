@end-to-end @fixtures-users
Feature: Change users status
  In order to administrate user accounts
  As an administrator
  I need to be able to activate and deactivate users

  Background:
    Given I am logged as an administrator

  Scenario: I can deactivate a user
    When I deactivate the user damien
    Then the user damien should be deactivated

  Scenario: I can activate a user
    When I activate the user chips
    Then the user chips should be activated

  Scenario: I cannot change an administrator status
    When I am on the administration page
    Then I cannot change the status of another administrator
