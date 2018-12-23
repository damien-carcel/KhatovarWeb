@fixtures-with-folders
Feature: Delete files and folders
  In order to use files and folders
  As an administrator
  I need to be able to delete files and folders

  Background:
    Given I am on "login"
    And I fill in "Nom d'utilisateur" with "aurore"
    And I fill in "Mot de passe" with "aurore"
    And I press "Connexion"
    And I am on "documents"
    And I follow "Ajouter un fichier"
    And I attach the file "tests/fixtures/images/black_cat.jpg" to "file_filePath"
    And I press "file_submit"
    And I follow "A folder at root"
    And I follow "Ajouter un fichier"
    And I attach the file "tests/fixtures/images/siamese_cat.jpg" to "file_filePath"
    And I press "file_submit"

  Scenario: I can delete a file from root
    Given I am on "documents"
    When I click on "Supprimer" in table row "black_cat.jpg"
    Then I should be on "documents/"
    And I should not see "black_cat.jpg"

  Scenario: I can delete a folder from root
    Given I am on "documents"
    When I click on "Supprimer" in table row "An other folder without parent"
    Then I should be on "documents/"
    And I should not see "An other folder without parent"

  Scenario: I can delete a file from an existing folder
    Given I am on "documents"
    And I follow "A folder at root"
    When I click on "Supprimer" in table row "siamese_cat.jpg"
    And I am on "documents"
    And I follow "A folder at root"
    Then I should not see "siamese_cat.jpg"

  Scenario: I can delete a folder from existing folder
    Given I am on "documents"
    And I follow "A folder at root"
    When I click on "Supprimer" in table row "Another folder inside a folder"
    And I am on "documents"
    And I follow "A folder at root"
    Then I should not see "Another folder inside a folder"

  Scenario: I can delete a folder and all its content
    Given I am on "documents"
    When I click on "Supprimer" in table row "A folder at root"
    Then I should be on "documents/"
    And I should not see "A folder at root"
