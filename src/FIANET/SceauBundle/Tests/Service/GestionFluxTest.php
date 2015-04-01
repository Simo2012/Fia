<?php
namespace FIANET\SceauBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Router;

class GestionFluxTest extends WebTestCase
{
    private $client;
    private $url;
    private $siteIDFluxXML = 3;
    private $siteIDNonAutorise = 1;

    public function __construct()
    {
        $this->client = static::createClient();
        $this->url = '/webservice/send_rating';
    }

    /**
     * On appelle le webservice avec la méthode GET : code HTTP 405 -> Method not alllowed
     */
    public function testMauvaiseMethode()
    {
        $this->client->request('GET', $this->url);

        $this->assertEquals(405, $this->client->getResponse()->getStatusCode());
    }

    /**
     * On appelle le webservice sans aucun paramètre : XML avec message KO
     */
    public function testSansParametres()
    {
        $xml = $this->client->request('POST', $this->url);

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('KO', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice sans le paramètre SiteID : XML avec message KO
     */
    public function testSansSiteID()
    {
        $xml = $this->client->request('POST', $this->url, array(
            'XMLInfo' => '<control></control>', 'CheckSum' => md5('<control></control>')
        ));

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('KO', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice sans le paramètre XMLInfo : XML avec message KO
     */
    public function testSansXMLInfo()
    {
        $xml = $this->client->request('POST', $this->url, array(
            'SiteID' => $this->siteIDFluxXML, 'CheckSum' => md5(uniqid('xml'))
        ));

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('KO', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice sans le paramètre CheckSum : XML avec message KO
     */
    public function testSansCheckSum()
    {
        $xml = $this->client->request('POST', $this->url, array(
            'SiteID' => $this->siteIDFluxXML, 'XMLInfo' => uniqid('xml')));

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('KO', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice avec un site qui n'a pas le bon type d'administration : XML avec message KO
     */
    public function testSiteMauvaiseAdministration()
    {
        $xmlInfo = uniqid('xml');
        $xml = $this->client->request('POST', $this->url, array(
            'SiteID' => $this->siteIDNonAutorise, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo)));

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('KO', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice avec un site inexistant : XML avec message KO
     */
    public function testSiteInexistant()
    {
        $xmlInfo = uniqid('xml');
        $xml = $this->client->request('POST', $this->url, array(
            'SiteID' => 999999999, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo)));

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('KO', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice avec un checksum invalide : XML avec message KO
     */
    public function testChecksumInvalide()
    {
        $xmlInfo = uniqid('xml');
        $xml = $this->client->request('POST', $this->url, array(
            'SiteID' => $this->siteIDFluxXML, 'XMLInfo' => $xmlInfo, 'CheckSum' => 'incorrect'));

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('KO', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice avec un flux déjà envoyé : XML avec message KO
     */
    public function testFluxDejaEnvoye()
    {
        $xmlInfo = '<control>reçu</control>';

        /* 1er envoi */
        $client = static::createClient();
        $client->request('POST', $this->url, array(
            'SiteID' => $this->siteIDFluxXML, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo)));

        /* 2ème envoi */
        $xml = $this->client->request('POST', $this->url, array(
            'SiteID' => $this->siteIDFluxXML, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo)));

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('KO', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice avec un flux bien formé mais dont la date de commande est incorrecte :
     * XML avec message KO
     */
    public function testDateIncorecte()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8"?>
            <control fianetmodule="api_prestashop_sceau" version="4.5" sceaumodule="2.8"><utilisateur><nom titre="2">
            <![CDATA[TREGUIER]]></nom><prenom><![CDATA[Aline]]></prenom>
            <email><![CDATA[missy_girl56@hotmail.fr]]></email></utilisateur>
            <infocommande><refid><![CDATA[' . uniqid('xml') . ']]></refid>
            <siteid><![CDATA[14533]]></siteid><montant devise="EUR"><![CDATA[33.50]]></montant>
            <ip timestamp="2014-07-15T12:10:37"><![CDATA[82.66.246.23]]></ip><langue><![CDATA[fr]]></langue>
            <produits><urlwebservice><![CDATA[http://www.vitalco.com/modules/fianetsceau/commentsmanager.php?token=7]]>
            </urlwebservice><produit><codeean><![CDATA[3401597785115]]></codeean><id><![CDATA[33]]></id>
            <categorie><![CDATA[239]]></categorie><libelle><![CDATA[Piment Brûleur ]]></libelle>
            <montant><![CDATA[28.5]]></montant></produit></produits></infocommande><paiement><type><![CDATA[2]]></type>
            </paiement><crypt>d7c5485c8721a04092acdff3172cce32</crypt></control>';

        $xml = $this->client->request(
            'POST',
            $this->url,
            array('SiteID' => $this->siteIDFluxXML, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('KO', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice avec un flux bien formé mais dont l'email est incorrecte : XML avec message KO
     */
    public function testEmailIncorect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8"?>
            <control fianetmodule="api_prestashop_sceau" version="4.5" sceaumodule="2.8"><utilisateur><nom titre="2">
            <![CDATA[TREGUIER]]></nom><prenom><![CDATA[Aline]]></prenom>
            <email><![CDATA[missy_girl56hotmail.fr]]></email></utilisateur><infocommande>
            <refid><![CDATA[' . uniqid('xml') . ']]></refid>
            <siteid><![CDATA[14533]]></siteid><montant devise="EUR"><![CDATA[33.50]]></montant>
            <ip timestamp="2014-07-15 12:10:37"><![CDATA[82.66.246.23]]></ip><langue><![CDATA[fr]]></langue>
            <produits><urlwebservice><![CDATA[http://www.vitalco.com/modules/fianetsceau/commentsmanager.php?token=7]]>
            </urlwebservice><produit><codeean><![CDATA[3401597785115]]></codeean><id><![CDATA[33]]></id>
            <categorie><![CDATA[239]]></categorie><libelle><![CDATA[Piment Brûleur ]]></libelle>
            <montant><![CDATA[28.5]]></montant></produit></produits></infocommande><paiement><type><![CDATA[2]]></type>
            </paiement><crypt>d7c5485c8721a04092acdff3172cce32</crypt></control>';

        $xml = $this->client->request(
            'POST',
            $this->url,
            array('SiteID' => $this->siteIDFluxXML, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('KO', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice correctement : XML avec message OK
     */
    public function testCorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8"?>
            <control fianetmodule="api_prestashop_sceau" version="4.5" sceaumodule="2.8"><utilisateur><nom titre="2">
            <![CDATA[TREGUIER]]></nom><prenom><![CDATA[Aline]]></prenom>
            <email><![CDATA[missy_girl56@hotmail.fr]]></email></utilisateur>
            <infocommande><refid><![CDATA[' . uniqid('xml') . ']]></refid>
            <siteid><![CDATA[14533]]></siteid><montant devise="EUR"><![CDATA[33.50]]></montant>
            <ip timestamp="2014-07-15 12:10:37"><![CDATA[82.66.246.23]]></ip><langue><![CDATA[fr]]></langue>
            <produits><urlwebservice><![CDATA[http://www.vitalco.com/modules/fianetsceau/commentsmanager.php?token=7]]>
            </urlwebservice><produit><codeean><![CDATA[3401597785115]]></codeean><id><![CDATA[33]]></id>
            <categorie><![CDATA[239]]></categorie><libelle><![CDATA[Piment Brûleur ]]></libelle>
            <montant><![CDATA[28.5]]></montant></produit></produits></infocommande><paiement><type><![CDATA[2]]></type>
            </paiement><crypt>d7c5485c8721a04092acdff3172cce32</crypt></control>';

        $xml = $this->client->request(
            'POST',
            $this->url,
            array('SiteID' => $this->siteIDFluxXML, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('OK', $xml->filterXPath('//result')->attr('type'));
    }
}
