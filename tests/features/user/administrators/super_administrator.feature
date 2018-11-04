@end-to-end
Feature: Administrate administrators
  In order to administrate users including administrators
  As the super administrator
  I need to be able to administrate any kind of users

  Background:
    Given I am on the homepage
    And I am anonymous
    And I go to "login"
    And I fill in "Nom d'utilisateur" with "admin"
    And I fill in "Mot de passe" with "admin"
    And I press "Connexion"

  Scenario: I can list all users but me
    Given I am on "profile/"
    When I follow "Page d'administration"
    Then I should see "Administration des utilisateurs"
    And I should see the users "aurore, damien, freya, hegor, lilith and chips"

  Scenario: I can promote a user as administrator
    Given I am on "admin"
    When I follow "Changer le rôle" for "damien" profile
    And I select "Administrateur" from "Rôles"
    And I press "Modifier"
    Then I should see "Le rôle de l'utilisateur a été modifié"
    And user "damien" should have role "ROLE_ADMIN"

  Scenario: I can demote a user from administrator
    Given I am on "admin"
    When I follow "Changer le rôle" for "aurore" profile
    And I select "Utilisateur basique" from "Rôles"
    And I press "Modifier"
    Then I should see "Le rôle de l'utilisateur a été modifié"
    And user "aurore" should have role "ROLE_USER"

  Scenario: I can change the status of an administrator
    Given I am on "admin"
    When I follow "Désactiver" for "aurore" profile
    Then I should see "L'utilisateur a été désactivé"
    And user "aurore" should be disabled
    When I follow "Activer" for "aurore" profile
    Then I should see "L'utilisateur a été activé"
    And user "aurore" should be enabled
