@fixtures-with-folders
Feature: Edit files and folders
  In order to use files and folders
  As an administrator
  I need to be able to edit files and folders

  Background:
    Given I am on "login"
    And I fill in "Nom d'utilisateur" with "aurore"
    And I fill in "Mot de passe" with "aurore"
    And I press "Connexion"
    And I am on "documents"
#    And I follow "Ajouter un ficher"
#    And I attach the file "features/bootstrap/fixtures/black_cat.jpg" to "file_filePath"
#    And I press "file_submit"

  Scenario: I can edit a file

  Scenario: I can edit a folder
