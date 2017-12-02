Feature: Create account
  In order to access the application
  As an anonymous user
  I need to be able to create a new account and reset information

  Background:
    Given I am on "login"
    And I am anonymous
    And I follow "Nouvel utilisateur"
    And I should be on "register/"

  Scenario: I can create new account
    Given I fill in the following:
      | Nom d'utilisateur       | pandore             |
      | Adresse e-mail          | pandore@khatovar.fr |
      | Mot de passe            | pandore             |
      | Répéter le mot de passe | pandore             |
    When I press "Créer un compte"
    Then I should see "L'utilisateur a été créé avec succès"
    And I should see "Un e-mail a été envoyé à l'adresse pandore@khatovar.fr. Il contient un lien d'activation sur lequel il vous faudra cliquer afin d'activer votre compte."
    And I should be anonymous
    When I follow the activation link for the user "pandore"
    Then I should see "Félicitations pandore, votre compte est maintenant activé."
    And I should be authenticated as "pandore"

  Scenario: I can see a warning message when trying to create an account with an existing username
    Given I should be on "register/"
    When I fill in the following:
      | Nom d'utilisateur       | damien              |
      | Adresse e-mail          | pandore@khatovar.fr |
      | Mot de passe            | pandore             |
      | Répéter le mot de passe | pandore             |
    When I press "Créer un compte"
    Then I should see "Le nom d'utilisateur est déjà utilisé"
    And I should be anonymous

  Scenario: I can see a warning message when trying to create an account with an existing email
    Given I should be on "register/"
    When I fill in the following:
      | Nom d'utilisateur       | pandore            |
      | Adresse e-mail          | damien@khatovar.fr |
      | Mot de passe            | pandore            |
      | Répéter le mot de passe | pandore            |
    When I press "Créer un compte"
    Then I should see "L'adresse e-mail est déjà utilisée"
    And I should be anonymous

  Scenario: I can see a warning message when creating an account with wrong confirmation password
    Given I should be on "register/"
    When I fill in the following:
      | Nom d'utilisateur       | pandore             |
      | Adresse e-mail          | pandore@khatovar.fr |
      | Mot de passe            | pandore             |
      | Répéter le mot de passe | pendora             |
    When I press "Créer un compte"
    Then I should see "Les deux mots de passe ne sont pas identiques"
    And I should be anonymous

  Scenario: I can get back on login page from register page
    Given I follow "Retour"
    Then I should be on "login"
