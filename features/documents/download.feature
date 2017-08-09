@fixtures-with-folders
Feature: Download files and folders
  In order to use files and folders
  As an regular user
  I need to be able to share files with others

  Background:
    And I am on "login"
    And I fill in "Nom d'utilisateur" with "lilith"
    And I fill in "Mot de passe" with "lilith"
    And I press "Connexion"
    And I am on "documents"

  Scenario: I can download a file
