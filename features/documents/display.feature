@fixtures-with-folders
Feature: Navigate through the application
  In order to use files and folders
  As a regular user
  I need to be able go in and out folders

  Background:
    Given I am on "login"
    And I fill in "Nom d'utilisateur" with "freya"
    And I fill in "Mot de passe" with "freya"
    And I press "Connexion"
    And I am on "documents"
    And I follow "Ajouter un fichier"
    And I attach the file "features/bootstrap/fixtures/black_cat.jpg" to "file_filePath"
    And I press "file_submit"
    And I follow "A folder at root"
    And I follow "Ajouter un fichier"
    And I attach the file "features/bootstrap/fixtures/white_cat.jpg" to "file_filePath"
    And I press "file_submit"
    And I follow "Another folder inside a folder"
    And I follow "Ajouter un fichier"
    And I attach the file "features/bootstrap/fixtures/siamese_cat.jpg" to "file_filePath"
    And I press "file_submit"

  Scenario: I can see files and folders information
