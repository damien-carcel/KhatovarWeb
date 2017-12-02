Feature: Edit user profiles
  In order to administrate user accounts
  As an administrator
  I need to be able to edit a user profile

  Background:
    Given I am on the homepage
    And I am anonymous
    And I go to "login"
    And I fill in "Nom d'utilisateur" with "aurore"
    And I fill in "Mot de passe" with "aurore"
    And I press "Connexion"

  Scenario: I can edit a user profile
    Given I am on "admin"
    When I follow "Éditer" for "damien" profile
    Then I should see "Édition du profil de l'utilisateur damien"
    When I fill in the following:
      | Nom d'utilisateur | pandore           |
      | Adresse e-mail    | pandore@gmail.com |
    And I press "Mettre à jour"
    Then I should see "Le profil utilisateur a été mis à jour"
    And I should see the users "pandore, freya, hegor, lilith and chips"

  Scenario: I cannot edit the profile of the super admin
    When I am on "admin/admin/edit"
    Then I should see "403 Forbidden"
