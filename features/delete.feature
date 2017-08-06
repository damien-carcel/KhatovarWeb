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

  Scenario: I can delete a file from root

  Scenario: I can delete a folder from root

  Scenario: I can delete a file from an existing folder

  Scenario: I can delete a folder from existing folder

  Scenario: I can delete a folder and all its content
