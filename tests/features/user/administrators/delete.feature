@end-to-end @fixtures-users
Feature: Delete a user account
  In order to administrate user accounts
  As an administrator
  I need to be able to delete a user account

  Background:
    Given I am anonymously on the homepage
    And I go to "login"
    And I fill in "Nom d'utilisateur" with "aurore"
    And I fill in "Mot de passe" with "aurore"
    And I press "Connexion"

  Scenario: I can delete a user
    Given I am on "admin"
    When I press "Supprimer" for "damien" profile
    Then I should see "L'utilisateur a bien été effacé"
    And I should see the users "freya, hegor, lilith and chips"

  Scenario: I can delete a user
    Given I am on "admin"
    When I stop following redirections
    And I press "Supprimer" for "damien" profile
    Then I should get a confirmation email with subject "Suppression de compte"
    And I start following redirections
