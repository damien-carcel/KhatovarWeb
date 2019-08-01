@end-to-end @fixtures-users
Feature: Réinitialiser le mot de passe
  In order to access the application
  As an anonymous user
  I need to be able to reset my password

  Scenario: I can reset a password by username
    Given I am anonymously on the login page
    When I reset a password using the user username
    Then the password should be reset

  Scenario: I can reset a password by email
    Given I am anonymously on the login page
    When I reset a password using the user email
    Then the password should be reset

  Scenario: I can get back on login page from reset page
    Given I am anonymously on the login page
    And I try to reset my password
    When I get back to the previous page
    Then I should be on the login page
