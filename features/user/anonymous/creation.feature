Feature: Create account
  In order to access the application
  As an anonymous user
  I need to be able to create a new account and reset information

  Background:
    Given I am on "login"
    And I am anonymous
    And I follow "New user"
    And I should be on "register/"

  Scenario: I can create new account
    Given I fill in the following:
      | Username        | pandore             |
      | Email           | pandore@khatovar.fr |
      | Password        | pandore             |
      | Repeat password | pandore             |
    When I press "Register"
    Then I should see "The user has been created successfully"
    And I should see "An email has been sent to pandore@khatovar.fr. It contains an activation link you must click to activate your account."
    And I should be anonymous
    When I follow the activation link for the user "pandore"
    Then I should see "Congrats pandore, your account is now activated."
    And I should be authenticated as "pandore"

  Scenario: I can see a warning message when trying to create an account with an existing username
    Given I should be on "register/"
    When I fill in the following:
      | Username        | damien              |
      | Email           | pandore@khatovar.fr |
      | Password        | pandore             |
      | Repeat password | pandore             |
    When I press "Register"
    Then I should see "The username is already used"
    And I should be anonymous

  Scenario: I can see a warning message when trying to create an account with an existing email
    Given I should be on "register/"
    When I fill in the following:
      | Username        | pandore            |
      | Email           | damien@khatovar.fr |
      | Password        | pandore            |
      | Repeat password | pandore            |
    When I press "Register"
    Then I should see "The email is already used"
    And I should be anonymous

  Scenario: I can see a warning message when creating an account with wrong confirmation password
    Given I should be on "register/"
    When I fill in the following:
      | Username        | pandore             |
      | Email           | pandore@khatovar.fr |
      | Password        | pandore             |
      | Repeat password | pendora             |
    When I press "Register"
    Then I should see "The entered passwords don't match"
    And I should be anonymous

  Scenario: I can get back on login page from register page
    Given I follow "Back"
    Then I should be on "login"
