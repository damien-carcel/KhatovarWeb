Feature: Authenticate users
  In order to authenticate myself
  As an anonymous user
  I need to be able to login then logout

  Background:
    Given I am on the homepage
    And I am anonymous

  Scenario: I can log in
    Given I go to "login"
    When I fill in "Username" with "admin"
    And I fill in "Password" with "admin"
    And I press "Log in"
    Then I should be authenticated as "admin"

  Scenario: I can log out
    Given I go to "login"
    When I fill in "Username" with "admin"
    And I fill in "Password" with "admin"
    And I press "Log in"
    Then I should be authenticated as "admin"
    When I go to "profile/"
    And I follow "Log out"
    Then I should be anonymous

  Scenario: I can see an error message when I use wrong credentials
    Given I go to "login"
    When I fill in "Username" with "foo"
    And I fill in "Password" with "bar"
    And I press "Log in"
    Then I should see "Invalid credentials."

  Scenario: I am redirected to login page when anonymously authenticated
    When I go to "profile/"
    Then I should be on "profile/"
    But the "div" element should contain "class=\"login-wall panel panel-default\""
    And I should see "Remember me"
