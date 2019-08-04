Feature: Create files and folders
  In order to use files and folders
  As an administrator
  I need to be able to create files and folders

  Background:
    Given I am on "login"
    And I fill in "Nom d'utilisateur" with "aurore"
    And I fill in "Mot de passe" with "aurore"
    And I press "Connexion"
    And I am on "documents"

  @fixtures-users
  Scenario: I can create a file at root
    Given I follow "Ajouter un fichier"
    And I attach the file "tests/fixtures/images/black_cat.jpg" to "file_filePath"
    When I press "file_submit"
    Then I should be on "documents/"
    And I should see "black_cat.jpg" in the ".file_black_cat_jpg .file_name" element

  @fixtures-users
  Scenario: I can create a folder at root
    Given I follow "Ajouter un dossier"
    When I fill in "Nom" with "A newly created folder"
    And I press "Ajouter"
    Then I should be on "documents/"
    And I should see "A newly created folder" in the ".folder_a_newly_created_folder .folder_name" element

  @fixtures-with-folders
  Scenario: I can create a file inside an existing folder
    Given I follow "A folder at root"
    When I follow "Ajouter un fichier"
    And I attach the file "tests/fixtures/images/black_cat.jpg" to "file_filePath"
    And I press "file_submit"
    Then I should see "A folder at root" in the "nav" element
    And I should see "black_cat.jpg" in the ".file_black_cat_jpg .file_name" element

  @fixtures-with-folders
  Scenario: I can create a folder an existing folder
    Given I follow "A folder at root"
    And I follow "Ajouter un dossier"
    When I fill in "Nom" with "A newly created folder"
    And I press "Ajouter"
    Then I should see "A folder at root" in the "nav" element
    And I should see "A newly created folder" in the ".folder_a_newly_created_folder .folder_name" element
