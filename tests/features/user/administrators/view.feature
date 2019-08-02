@end-to-end @fixtures-users
Feature: See user profiles
  In order to administrate user accounts
  As an administrator
  I need to be able to see a user profile

  Scenario: I can see a user profile
    Given I am logged as an administrator
    And I am on the administration page
    When I follow "Visualiser" for "damien" profile
    Then I should see "Profil de l'utilisateur damien"
    And I should see "Nom d'utilisateur: damien"
    And I should see "Adresse e-mail: damien@khatovar.fr"

  Scenario: I cannot see the profile of the super admin
    Given I am logged as an administrator
    When I am on "admin/admin/show"
    Then I should see "403 Forbidden"
