@end-to-end @fixtures-users
Feature: Change users status
  In order to administrate user accounts
  As an administrator
  I need to be able to activate and deactivate users

  Scenario: I can deactivate a user
    Given I am logged as an administrator
    And I am on the administration page
    When I follow "Désactiver" for "damien" profile
    Then I should see "L'utilisateur a été désactivé"
    And user "damien" should be disabled

  Scenario: I can activate a user
    Given I am logged as an administrator
    And I am on the administration page
    When I follow "Activer" for "chips" profile
    Then I should see "L'utilisateur a été activé"
    And user "chips" should be enabled

  Scenario: I cannot change an administrator status
    Given I am logged as an administrator
    When I am on the administration page
    Then I should not see "Désactiver" in the table line containing "hegor"
    And I am on "admin/hegor/status"
    And I should see "403 Forbidden"
