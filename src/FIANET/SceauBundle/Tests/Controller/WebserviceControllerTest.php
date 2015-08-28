<?php

namespace FIANET\SceauBundle\Tests\Controller\Extranet;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WebserviceControllerTest extends WebTestCase
{
    private $client;
    private $container;
    private $urlSendRating;
    private $urlSendRatingSchema;

    public function __construct()
    {
        $this->client = static::createClient();
        $this->container = static::$kernel->getContainer();

        $this->urlSendRating = '/webservice/fr/send_rating';
        $this->urlSendRatingSchema = '/webservice/fr/send_rating/schema';
    }

    /**
     * On appelle le webservice "send_rating" avec la méthode GET : code HTTP 405 -> Method not alllowed
     */
    public function testSendRatingMauvaiseMethode()
    {
        $this->client->request('GET', $this->urlSendRating);

        $this->assertEquals(405, $this->client->getResponse()->getStatusCode());
    }

    /**
     * On appelle le webservice "send_rating" de manière correcte mais avec de mauvaises données (ici les données sont
     * manquantes) : on obtient un message "KO" au format XML
     */
    public function testSendRatingMauvaisesDonnees()
    {
        $xml = $this->client->request('POST', $this->urlSendRating);

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('KO', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice "send_rating" de manière correcte et avec de bonnes données : on obtient un message
     * "OK" au format XML
     */
    public function testSendRatingBonnesDonnees()
    {
        /* Ce test se base sur des données en base, il faut éventuellement modifier l'ID ci-dessous */
        $site = $this->container->get('doctrine')->getManager()->getRepository('FIANETSceauBundle:Site')->find(1);
        $refid = uniqid('', true);
        $date = new \DateTime();
        $timestamp = $date->format('Y-m-d h:i:s');
        $email = 'cricri.martini' . rand() . '@wanadoo.fr';
        $crypt = $this->container->get('fianet_sceau.flux')->getCrypt($site->getClePriveeSceau(), $refid, $timestamp, $email);

        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CRISTIANE</prenom>
            <email>' . $email . '</email></utilisateur>
            <infocommande><siteid>' . $site->getId() . '</siteid><refid>' . $refid . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $timestamp . '">83.112.81.91</ip>
            </infocommande><crypt>' . $crypt . '</crypt></control>';

        $xml = $this->client->request(
            'POST',
            $this->urlSendRating,
            array('SiteID' => 1, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('OK', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On teste que le schéma XML du webservice "send_rating" est accessible et au bon format.
     */
    public function testSendRatingSchema()
    {
        $this->client->request('GET', $this->urlSendRatingSchema);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));
    }
}
