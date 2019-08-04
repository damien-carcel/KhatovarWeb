@end-to-end @fixtures-users
Feature: Administrate administrators
  In order to administrate users including administrators
  As the super administrator
  I need to be able to administrate any kind of users

  Background:
    Given I am logged as the super administrator

  Scenario: I can list all users but me
    When I go on the administration page
    Then I should see all the regular users

  Scenario: I can promote a user as administrator
    Given I am on the administration page
    When I change the role of damien for "Administrateur"
    Then the user damien should be an admin

  Scenario: I can demote a user from administrator
    Given I am on the administration page
    When I change the role of aurore for "Utilisateur basique"
    Then the user aurore should be regular user

  Scenario: I can change the status of an administrator
    Given I am on the administration page
    When I deactivate the user aurore
    Then the user aurore should be deactivated
