@end-to-end @fixtures-users
Feature: Authenticate users
  In order to authenticate myself
  As an anonymous user
  I need to be able to login then logout

  Scenario: I can log in
    Given I am anonymously on the homepage
    When I log in as admin
    Then I should be authenticated as admin

  Scenario: I can log out
    Given I am anonymously on the homepage
    When I log in then out
    Then I should be anonymous

  Scenario: I can see an error message when I use wrong credentials
    Given I am anonymously on the homepage
    When I log in as foobar
    Then I should be noticed that the credentials are invalid

  Scenario: I am redirected to login page when anonymously authenticated
    Given I am anonymously on the homepage
    When I try to go on my profile
    Then I am proposed with the login screen instead
