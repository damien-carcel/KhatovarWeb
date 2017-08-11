@fixtures-with-folders
Feature: Display files and folders information
  In order to use files and folders
  As a regular user
  I need to be able to see files and folders information

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
    When I am on "documents"
    Then I should see "Dossier" in table row "A folder at root"
    And I should see "3 éléments" in table row "A folder at root"
    And I should see "Dossier" in table row "An other folder without parent"
    And I should see "0 élément" in table row "An other folder without parent"
    And I should see "image/jpeg" in table row "black_cat.jpg"
    And I should see "113.4 ko" in table row "black_cat.jpg"
    When I follow "A folder at root"
    Then I should see "Dossier" in table row "A folder inside a folder"
    And I should see "1 élément" in table row "A folder inside a folder"
    And I should see "Dossier" in table row "Another folder inside a folder"
    And I should see "1 élément" in table row "Another folder inside a folder"
    And I should see "image/jpeg" in table row "white_cat.jpg"
    And I should see "56.6 ko" in table row "white_cat.jpg"

