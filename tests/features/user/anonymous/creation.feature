@end-to-end @fixtures-users
Feature: Create account
  In order to access the application
  As an anonymous user
  I need to be able to create a new account and reset information

  Scenario: I can create new account
    Given I want to register a new account
    When I create a new account as pandore
    Then a new user "pandore" is created
    And pandore account can be activated

  Scenario: I can see a warning message when trying to create an account with an existing username
    Given I want to register a new account
    When I try to create a new account as pandore with an already existing username
    Then I should be notified that the username is already used

  Scenario: I can see a warning message when trying to create an account with an existing email
    Given I want to register a new account
    When I try to create a new account as pandore with an already existing email
    Then I should be notified that the email is already used

  Scenario: I can see a warning message when creating an account with wrong confirmation password
    Given I want to register a new account
    When I try to create a new account as pandore without confirming my password
    Then I should be notified that the confirmation password is different from the original one

  Scenario: I can get back on login page from register page
    Given I want to register a new account
    When I get back to the previous page
    Then I should be on the login page
