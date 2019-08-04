@fixtures-with-folders
Feature: Manipulate files and folders when it is allowed
  In order to use files and folders
  As a user
  I should be able to do only what I am allowed to

  Background:
    Given I am on "login"
    And I fill in "Nom d'utilisateur" with "freya"
    And I fill in "Mot de passe" with "freya"
    And I press "Connexion"
    And I am on "documents"
    And I follow "Ajouter un fichier"
    And I attach the file "tests/fixtures/images/black_cat.jpg" to "file_filePath"
    And I press "file_submit"
    And I follow "Déconnexion"

  Scenario: I cannot create a file if I am not an uploader

  Scenario: I cannot move a file if I am not an uploader

  Scenario: I cannot rename a file if I am not an uploader

  Scenario: I cannot delete a file if I am not an uploader

  Scenario: I cannot create a folder if I am not an administrator

  Scenario: I cannot move a folder if I am not an administrator

  Scenario: I cannot rename a folder if I am not an administrator

  Scenario: I cannot delete a folder if I am not an administrator
