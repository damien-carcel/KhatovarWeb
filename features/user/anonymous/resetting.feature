Feature: Reset password
  In order to access the application
  As an anonymous user
  I need to be able to reset my password

  Background:
    Given I am on "login"
    And I am anonymous

  Scenario: I can reset a password by username
    Given I follow "Forgotten password?"
    Then I should be on "resetting/request"
    When I fill in "Username or email address" with "damien"
    And I press "Reset password"
    Then I should be on "resetting/check-email?username=damien"
    And I should see "An email has been sent. It contains a link you must click to reset your password. Note: You can only request a new password once within 2 hours. If you don't get an email check your spam folder or try again."

  Scenario: I can resetting a password by email
    Given I follow "Forgotten password?"
    Then I should be on "resetting/request"
    When I fill in "Username or email address" with "damien@khatovar.fr"
    And I press "Reset password"
    Then I should be on "resetting/check-email?username=damien.carcel%40gmail.com"
    And I should see "An email has been sent. It contains a link you must click to reset your password. Note: You can only request a new password once within 2 hours. If you don't get an email check your spam folder or try again."

  Scenario: I fail to reset password if it already is
    Given I reset "damien" password
    And I follow "Forgotten password?"
    Then I should be on "resetting/request"
    When I fill in "Username or email address" with "damien"
    And I press "Reset password"
    Then I should see "An email has been sent. It contains a link you must click to reset your password. Note: You can only request a new password once within 2 hours. If you don't get an email check your spam folder or try again."

  Scenario: I can get back on login page from reset page
    Given I follow "Forgotten password?"
    Then I should be on "resetting/request"
    When I follow "Back"
    Then I should be on "login"
