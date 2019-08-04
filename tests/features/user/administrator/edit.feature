@end-to-end @fixtures-users
Feature: Edit user profiles
  In order to administrate user accounts
  As an administrator
  I need to be able to edit a user profile

  Scenario: I can edit a user profile
    Given I am logged as an administrator
    When I rename the user damien as pandore
    Then I should be notified that the user profile was updated

  Scenario: I cannot edit the profile of the super admin
    Given I am logged as an administrator
    When I try to edit the super administrator profile
    Then I am forbidden to access it
