@end-to-end @fixtures-users
Feature: See user profiles
  In order to administrate user accounts
  As an administrator
  I need to be able to see a user profile

  Background:
    Given I am on the homepage
    And I am anonymous
    And I go to "login"
    And I fill in "Nom d'utilisateur" with "aurore"
    And I fill in "Mot de passe" with "aurore"
    And I press "Connexion"

  Scenario: I can see a user profile
    Given I am on "admin"
    When I follow "Visualiser" for "damien" profile
    Then I should see "Profil de l'utilisateur damien"
    And I should see "Nom d'utilisateur: damien"
    And I should see "Adresse e-mail: damien@khatovar.fr"

  Scenario: I cannot see the profile of the super admin
    When I am on "admin/admin/show"
    Then I should see "403 Forbidden"
