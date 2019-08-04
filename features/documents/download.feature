@fixtures-with-folders
Feature: Download files and folders
  In order to use files and folders
  As an regular user
  I need to be able to share files with others

  Background:
    Given I am on "login"
    And I fill in "Nom d'utilisateur" with "aurore"
    And I fill in "Mot de passe" with "aurore"
    And I press "Connexion"
    And I am on "documents"
    And I follow "Ajouter un fichier"
    And I attach the file "tests/fixtures/images/black_cat.jpg" to "file_filePath"
    And I press "file_submit"
    And I follow "Déconnexion"

  Scenario: I can download a file
    Given I am on "login"
    And I fill in "Nom d'utilisateur" with "lilith"
    And I fill in "Mot de passe" with "lilith"
    And I press "Connexion"
    When I am on "documents"
    And I follow "black_cat.jpg"
    Then the current page should be the file "black_cat.jpg"
