@fixtures-with-folders
Feature: Move files and folders
  In order to use files and folders
  As an administrator
  I need to be able to move files and folders

  Background:
    Given I am on "login"
    And I fill in "Nom d'utilisateur" with "aurore"
    And I fill in "Mot de passe" with "aurore"
    And I press "Connexion"
    And I am on "documents"
    And I follow "Ajouter un fichier"
    And I attach the file "features/Context/fixtures/black_cat.jpg" to "file_filePath"
    And I press "file_submit"
    And I follow "A folder at root"
    And I follow "Ajouter un fichier"
    And I attach the file "features/Context/fixtures/siamese_cat.jpg" to "file_filePath"
    And I press "file_submit"

  Scenario: I can move a file from root
    Given I am on "documents"
    When I click on "Déplacer" in table row "black_cat.jpg"
    Then I should see "Déplacer un fichier"
    When I select "- A folder at root" from "Choisissez un dossier"
    And I press "Déplacer"
    Then I should see "A folder at root" in the "nav" element
    And I should see "black_cat.jpg"

  Scenario: I can move a folder from root
    Given I am on "documents"
    When I click on "Déplacer" in table row "An other folder without parent"
    Then I should see "Déplacer un dossier"
    When I select "- A folder at root" from "Choisissez un dossier"
    And I press "Déplacer"
    Then I should see "A folder at root" in the "nav" element
    And I should see "An other folder without parent"

  Scenario: I can move a file from an existing folder to another
    Given I am on "documents"
    When I follow "A folder at root"
    And I click on "Déplacer" in table row "siamese_cat.jpg"
    Then I should see "Déplacer un fichier"
    When I select "- - Another folder inside a folder" from "Choisissez un dossier"
    And I press "Déplacer"
    Then I should see "Another folder inside a folder" in the "nav" element
    And I should see "siamese_cat.jpg"

  Scenario: I can move a folder from an existing folder to another
    Given I am on "documents"
    When I follow "A folder at root"
    And I click on "Déplacer" in table row "A folder inside a folder"
    Then I should see "Déplacer un dossier"
    When I select "- - Another folder inside a folder" from "Choisissez un dossier"
    And I press "Déplacer"
    Then I should see "Another folder inside a folder" in the "nav" element
    And I should see "A folder inside a folder"

  Scenario: I can move a file from an existing folder to the root
    Given I am on "documents"
    When I follow "A folder at root"
    And I click on "Déplacer" in table row "siamese_cat.jpg"
    Then I should see "Déplacer un fichier"
    When I select "Racine" from "Choisissez un dossier"
    And I press "Déplacer"
    Then I should be on "documents/"
    And I should see "siamese_cat.jpg"

  Scenario: I can move a folder from an existing folder to the root
    Given I am on "documents"
    When I follow "A folder at root"
    And I click on "Déplacer" in table row "A folder inside a folder"
    Then I should see "Déplacer un dossier"
    When I select "Racine" from "Choisissez un dossier"
    And I press "Déplacer"
    Then I should be on "documents/"
    And I should see "A folder inside a folder"
