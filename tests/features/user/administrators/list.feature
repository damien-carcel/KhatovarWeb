@end-to-end @fixtures-users
Feature: Manage user accounts
  In order to administrate user accounts
  As an administrator
  I need to interact with the administration page

  Background:
    Given I am anonymously on the homepage
    And I go to "login"
    And I fill in "Nom d'utilisateur" with "aurore"
    And I fill in "Mot de passe" with "aurore"
    And I press "Connexion"

  Scenario: I can see all users on the admin page
    Given I am on "profile/"
    When I follow "Page d'administration"
    Then I should see "Administration des utilisateurs"
    And I should see the users "damien, freya, hegor, lilith and chips"

  Scenario: A regular user should not be able to access the admin page
    Given I am on "admin"
    And I follow "Déconnexion"
    And I fill in "Nom d'utilisateur" with "damien"
    And I fill in "Mot de passe" with "damien"
    When I press "Connexion"
    Then I should be on "admin/"
    And I should see "403 Forbidden"
