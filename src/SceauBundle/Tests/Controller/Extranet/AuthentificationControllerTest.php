<?php

namespace SceauBundle\Tests\Controller\Extranet;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthentificationControllerTest extends WebTestCase
{
    /**
     * Redirection vers la page de connexion quand on arrive sur l'extranet sans être connecté.
     */
    public function testAffichagePageLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/extranet/fr');

        $this->assertTrue($client->getResponse()->isRedirect());
    }

    /**
     * Soumission du formulaire de connexion sans modification des valeurs par défaut : on obtient un message d'erreur.
     */
    public function testSoumissionFormLoginSansModif()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/extranet/fr/login');
        $form = $crawler->selectButton('Valider')->form();
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('#erreur'));
    }

    /**
     * Soumission du formulaire de connexion avec un login vide : on obtient un message d'erreur.
     */
    public function testSoumissionFormSansLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/extranet/fr/login');
        $form = $crawler->selectButton('Valider')->form();
        $form['_username'] = '';
        $form['_password'] = 'admin';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('#erreur'));
    }

    /**
     * Soumission du formulaire de connexion avec un mot de passe vide : on obtient un message d'erreur.
     */
    public function testSoumissionFormSansMdp()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/extranet/fr/login');
        $form = $crawler->selectButton('Valider')->form();
        $form['_username'] = 'admin';
        $form['_password'] = '';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('#erreur'));
    }

    /**
     * Soumission du formulaire de connexion avec un bon login et mot de passe : on arrive sur la page d'accueil de
     * l'extranet.
     */
    public function testSoumissionFormOK()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/extranet/fr/login');
        $form = $crawler->selectButton('Valider')->form();
        $form['_username'] = 'admin';
        $form['_password'] = 'admin';
        $client->submit($form);
        $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/extranet/fr/', $client->getRequest()->getRequestUri());
    }

    /**
     * 1) Affichage de la page de mot de passe oublié en cliquant sur le lien "Mot de passe oublié" de la page de
     * connexion.
     *
     * 2) Retour en arrière en cliquant sur le bouton "Retour".
     */
    public function testAffichageMdpOublie()
    {
        /* 1 */
        $client = static::createClient();
        $crawler = $client->request('GET', '/extranet/fr/login');
        $link = $crawler->selectLink('Mot de passe oublié ?')->link();
        $crawler = $client->click($link);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/extranet/fr/mdp_oublie', $client->getRequest()->getRequestUri());

        /* 2 */
        $form = $crawler->selectButton('retour')->form();
        $client->submit($form);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/extranet/fr/login', $client->getRequest()->getRequestUri());
    }

    /**
     * Soumission du formulaire de mot de passe oublié avec site ID vide : on obtient un message d'erreur.
     */
    public function testSoumissionMdpOublieVide()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/extranet/fr/mdp_oublie');
        $form = $crawler->selectButton('Valider')->form();
        $crawler = $client->submit($form);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('#erreur'));
    }

    /**
     * 1) Soumission du formulaire de mot de passe oublié avec site ID qui n'existe pas : on obtient un message
     * d'erreur.
     *
     * 2) Soumission du formulaire de mot de passe oublié avec site ID non numérique : on obtient un message
     * d'erreur.
     */
    public function testSoumissionMdpOublieSiteIdIncorect()
    {
        /* 1 */
        $client = static::createClient();
        $crawler = $client->request('GET', '/extranet/fr/mdp_oublie');
        $form = $crawler->selectButton('Valider')->form();
        $form['site_id[site_id]'] = 99999999;
        $crawler = $client->submit($form);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('#erreur'));

        /* 2 */
        $form = $crawler->selectButton('Valider')->form();
        $form['site_id[site_id]'] = 'abcd';
        $crawler = $client->submit($form);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('#erreur'));
    }

    /**
     * 1) Soumission du formulaire de mot de passe oublié avec site ID qui existe : on est redirigé vers la page de
     * confirmation.
     *
     * 2) Clic sur le bouton "Connexion" de la page de confirmation : retour au formulaire de connexion.
     */
    public function testSoumissionMdpOublieSiteIdOK()
    {
        /* 1 */
        $client = static::createClient();
        $crawler = $client->request('GET', '/extranet/fr/mdp_oublie');
        $form = $crawler->selectButton('Valider')->form();
        $site = $client->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('SceauBundle:Site')->findOneByNom('Cdiscount');
        $form['site_id[site_id]'] = $site->getId();
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/extranet/fr/mdp_oublie_confirmation', $client->getRequest()->getRequestUri());

        /* 2 */
        $form = $crawler->selectButton('Se connecter')->form();
        $client->submit($form);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('/extranet/fr/login', $client->getRequest()->getRequestUri());
    }
}
