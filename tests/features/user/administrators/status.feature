@end-to-end @fixtures-users
Feature: Change users status
  In order to administrate user accounts
  As an administrator
  I need to be able to activate and deactivate users

  Background:
    Given I am anonymously on the homepage
    And I go to "login"
    And I fill in "Nom d'utilisateur" with "aurore"
    And I fill in "Mot de passe" with "aurore"
    And I press "Connexion"

  Scenario: I can deactivate a user
    Given I am on "admin"
    When I follow "Désactiver" for "damien" profile
    Then I should see "L'utilisateur a été désactivé"
    And user "damien" should be disabled

  Scenario: I can activate a user
    Given I am on "admin"
    When I follow "Activer" for "chips" profile
    Then I should see "L'utilisateur a été activé"
    And user "chips" should be enabled

  Scenario: I cannot change an administrator status
    When I am on "admin"
    Then I should not see "Désactiver" in the table line containing "hegor"
    When I am on "admin/hegor/status"
    Then I should see "403 Forbidden"
