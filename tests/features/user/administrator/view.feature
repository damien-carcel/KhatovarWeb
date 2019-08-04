@end-to-end @fixtures-users
Feature: See user profiles
  In order to administrate user accounts
  As an administrator
  I need to be able to see a user profile

  Scenario: I can see a user profile
    Given I am logged as an administrator
    When I try to access damien profile
    Then I can see the profile of damien

  Scenario: I cannot see the profile of the super admin
    Given I am logged as an administrator
    When I try to access the profile of the super administrator
    Then I am forbidden to access it
