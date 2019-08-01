@end-to-end @fixtures-users
Feature: Delete a user account
  In order to administrate user accounts
  As an administrator
  I need to be able to delete a user account

  # TODO: Use a mail catcher to check that the removed user was notified by mail about the destruction of his/her account
  Scenario: I can delete a user
    Given I am logged as an administrator
    And I am on the administration page
    When I remove the user damien
    Then I should be noticed that user damien was removed
    And I should see the users "freya, hegor, lilith and chips"
