Feature: Réinitialiser le mot de passe
  In order to access the application
  As an anonymous user
  I need to be able to reset my password

  Background:
    Given I am on "login"
    And I am anonymous

  Scenario: I can reset a password by username
    Given I follow "Mot de passe oublié ?"
    Then I should be on "resetting/request"
    When I fill in "Nom d'utilisateur ou adresse e-mail" with "damien"
    And I press "Réinitialiser le mot de passe"
    Then I should be on "resetting/check-email?username=damien"
    And I should see "Un e-mail a été envoyé. Il contient un lien sur lequel il vous faudra cliquer pour réinitialiser votre mot de passe."

  Scenario: I can reset a password by email
    Given I follow "Mot de passe oublié ?"
    Then I should be on "resetting/request"
    When I fill in "Nom d'utilisateur ou adresse e-mail" with "damien@khatovar.fr"
    And I press "Réinitialiser le mot de passe"
    Then I should be on "resetting/check-email?username=damien.carcel%40gmail.com"
    And I should see "Un e-mail a été envoyé. Il contient un lien sur lequel il vous faudra cliquer pour réinitialiser votre mot de passe."

  Scenario: I fail to reset password if it already is
    Given I reset "damien" password
    And I follow "Mot de passe oublié ?"
    Then I should be on "resetting/request"
    When I fill in "Nom d'utilisateur ou adresse e-mail" with "damien"
    And I press "Réinitialiser le mot de passe"
    Then I should see "Un e-mail a été envoyé. Il contient un lien sur lequel il vous faudra cliquer pour réinitialiser votre mot de passe."

  Scenario: I can get back on login page from reset page
    Given I follow "Mot de passe oublié ?"
    Then I should be on "resetting/request"
    When I follow "Retour"
    Then I should be on "login"
