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
    And I follow "Ajouter un fichier"
    And I attach the file "tests/fixtures/images/black_cat.jpg" to "file_filePath"
    And I press "file_submit"

  Scenario: I can edit a file at root
    Given I am on "documents"
    When I click on "Renommer" in table row "black_cat.jpg"
    Then I should see "Renommer un fichier"
    When I fill in "Nom" with "not_a_dog.jpg"
    And I press "Renommer"
    Then I should be on "documents/"
    And I should see "not_a_dog.jpg" in the ".file_not_a_dog_jpg .file_name" element

  Scenario: I can edit a folder at root
    Given I am on "documents"
    When I click on "Renommer" in table row "A folder at root"
    Then I should see "Renommer un dossier"
    When I fill in "Nom" with "Still a folder"
    And I press "Renommer"
    Then I should be on "documents/"
    And I should see "Still a folder" in the ".folder_still_a_folder .folder_name" element


  Scenario: I can edit a file inside a folder
    Given I am on "documents"
    When I follow "A folder at root"
    And I follow "Ajouter un fichier"
    And I attach the file "tests/fixtures/images/white_cat.jpg" to "file_filePath"
    And I press "file_submit"
    Then I should see "A folder at root" in the "nav" element
    When I click on "Renommer" in table row "white_cat.jpg"
    Then I should see "Renommer un fichier"
    When I fill in "Nom" with "not_a_dog.jpg"
    And I press "Renommer"
    Then I should see "A folder at root" in the "nav" element
    And I should see "not_a_dog.jpg" in the ".file_not_a_dog_jpg .file_name" element

  Scenario: I can edit a folder inside a folder
    Given I am on "documents"
    And I follow "A folder at root"
    When I click on "Renommer" in table row "A folder inside a folder"
    Then I should see "Renommer un dossier"
    When I fill in "Nom" with "Still a folder inside a folder"
    And I press "Renommer"
    Then I should see "A folder at root" in the "nav" element
    And I should see "Still a folder inside a folder" in the ".folder_still_a_folder_inside_a_folder .folder_name" element
