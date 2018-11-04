@end-to-end
Feature: Change users roles
  In order to administrate user accounts
  As an administrator
  I need to be able to change a user roles

  Background:
    Given I am on the homepage
    And I am anonymous
    And I go to "login"
    And I fill in "Nom d'utilisateur" with "aurore"
    And I fill in "Mot de passe" with "aurore"
    And I press "Connexion"

  Scenario: I can change a user role
    Given I am on "admin"
    When I follow "Changer le rôle" for "damien" profile
    And I select "Lecture seule" from "Rôles"
    And I press "Modifier"
    Then I should see "Le rôle de l'utilisateur a été modifié"
    And user "damien" should have role "ROLE_VIEWER"

  Scenario: I cannot promote a user as administrator
    Given I am on "admin"
    When I follow "Changer le rôle" for "damien" profile
    Then I should see "Utilisateur basique" in the "select" element
    And I should see "Lecture seule" in the "select" element
    But I should not see "Administrateur" in the "select" element
    And I should not see "Super administrateur" in the "select" element

  Scenario: I cannot demote an administrator
    When I am on "admin"
    Then I should not see "Changer le rôle" in the table line containing "hegor"
    When I am on "admin/hegor/role"
    Then I should see "403 Forbidden"
