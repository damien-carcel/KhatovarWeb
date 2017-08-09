@fixtures-with-folders
Feature: Share files
  In order to use files
  As a regular user
  I need to be able to share files with others

  Scenario: I can share file as a link
    Given I am on "login"
    And I fill in "Nom d'utilisateur" with "lilith"
    And I fill in "Mot de passe" with "lilith"
    And I press "Connexion"
    And I am on "documents"
