@end-to-end @fixtures-users
Feature: Manage user accounts
  In order to administrate user accounts
  As an administrator
  I need to interact with the administration page

  Scenario: I can see all users on the admin page
    Given I am logged as an administrator
    When I go on the administration page
    Then I should see the list of all the other users except the super admin

  Scenario: A regular user should not be able to access the admin page
    Given I am logged as a regular user
    When I try to access the administration page
    And I am forbidden to access the page
