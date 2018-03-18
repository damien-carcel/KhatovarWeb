@fixtures-with-folders
Feature: Navigate through the application
  In order to use files and folders
  As a regular user
  I need to be able go in and out folders

  Background:
    Given I am on "login"
    And I fill in "Nom d'utilisateur" with "lilith"
    And I fill in "Mot de passe" with "lilith"
    And I press "Connexion"
    And I am on "documents"

  Scenario: I can go back from a folder

  Scenario: I can go several folder back at once

  Scenario: I can go from a folder directly to the root
