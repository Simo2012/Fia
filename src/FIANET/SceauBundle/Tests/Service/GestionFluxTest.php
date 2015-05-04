<?php
namespace FIANET\SceauBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Router;

class GestionFluxTest extends WebTestCase
{
    private $client;
    private $url;
    private $siteIDFluxXML = 1;
    private $siteIDNonAutorise = 66;

    public function __construct()
    {
        $this->client = static::createClient();
        $this->url = '/webservice/fr/send_rating';
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
     * On appelle le webservice avec un nom d'utilisateur trop long : XML avec message KO
     */
    public function testNomTropLong()
    {
        $this->client->request('GET', '/'); // GET pour initialiser le container

        $container = $this->client->getContainer();
        $site = $container->get('doctrine.orm.entity_manager')->getRepository('FIANETSceauBundle:Site')
            ->find($this->siteIDFluxXML);

        $refid = uniqid('xml');
        $timestamp = '2015-04-02 23:43';
        $email = 'cricri.martini@wanadoo.fr';
        $crypt = $container->get('fianet_sceau.flux')->getCrypt($site->getClePriveeSceau(), $refid, $timestamp, $email);

        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur>
            <nom titre="2">MARTINIDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD</nom>
            <prenom>CRISTIANE</prenom><email>' . $email . '</email></utilisateur>
            <infocommande><siteid>' . $site->getId() . '</siteid><refid>' . $refid . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $timestamp . '">83.112.81.91</ip>
            </infocommande><crypt>' . $crypt . '</crypt></control>';

        $xml = $this->client->request(
            'POST',
            $this->url,
            array('SiteID' => $this->siteIDFluxXML, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('KO', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice avec un flux bien formé mais dont la date de commande est incorrecte :
     * XML avec message KO
     */
    public function testDateIncorrecte()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8"?>
            <control fianetmodule="api_prestashop_sceau" version="4.5" sceaumodule="2.8"><utilisateur><nom titre="2">
            <![CDATA[TREGUIER]]></nom><prenom><![CDATA[Aline]]></prenom>
            <email><![CDATA[missy_girl56@hotmail.fr]]></email></utilisateur>
            <infocommande><refid><![CDATA[' . uniqid('xml') . ']]></refid>
            <siteid><![CDATA[' . $this->siteIDFluxXML . ']]></siteid><montant devise="EUR"><![CDATA[33.50]]></montant>
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
     * On appelle le webservice avec un flux bien formé mais dont l'email est incorrect : XML avec message KO
     */
    public function testEmailIncorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8"?>
            <control fianetmodule="api_prestashop_sceau" version="4.5" sceaumodule="2.8"><utilisateur><nom titre="2">
            <![CDATA[TREGUIER]]></nom><prenom><![CDATA[Aline]]></prenom>
            <email><![CDATA[missy_girl56hotmail.fr]]></email></utilisateur><infocommande>
            <refid><![CDATA[' . uniqid('xml') . ']]></refid>
            <siteid><![CDATA[' . $this->siteIDFluxXML . ']]></siteid><montant devise="EUR"><![CDATA[33.50]]></montant>
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
     * On appelle le webservice avec un flux bien formé mais dont ip est incorrecte : XML avec message KO
     */
    public function testIPIncorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8"?>
            <control fianetmodule="api_prestashop_sceau" version="4.5" sceaumodule="2.8"><utilisateur><nom titre="2">
            <![CDATA[TREGUIER]]></nom><prenom><![CDATA[Aline]]></prenom>
            <email><![CDATA[cricri.martini@wanadoo.fr]]></email></utilisateur><infocommande>
            <refid><![CDATA[' . uniqid('xml') . ']]></refid>
            <siteid><![CDATA[' . $this->siteIDFluxXML . ']]></siteid><montant devise="EUR"><![CDATA[33.50]]></montant>
            <ip timestamp="2014-07-15 12:10:37"><![CDATA[.66.246.23]]></ip><langue><![CDATA[fr]]></langue>
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
     * On appelle le webservice avec un flux bien formé mais dont le montant est incorrect : XML avec message KO
     */
    public function testMontantIncorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8"?>
            <control fianetmodule="api_prestashop_sceau" version="4.5" sceaumodule="2.8"><utilisateur><nom titre="2">
            <![CDATA[TREGUIER]]></nom><prenom><![CDATA[Aline]]></prenom>
            <email><![CDATA[cricri.martini@wanadoo.fr]]></email></utilisateur><infocommande>
            <refid><![CDATA[' . uniqid('xml') . ']]></refid>
            <siteid><![CDATA[' . $this->siteIDFluxXML . ']]></siteid><montant devise="EUR"><![CDATA[0]]></montant>
            <ip timestamp="2014-07-15 12:10:37"><![CDATA[185.66.246.23]]></ip><langue><![CDATA[fr]]></langue>
            <produits><urlwebservice><![CDATA[http://www.vitalco.com/modules/fianetsceau/commentsmanager.php?token=7]]>
            </urlwebservice><produit><codeean><![CDATA[3401597785115]]></codeean><id><![CDATA[33]]></id>
            <categorie><![CDATA[239]]></categorie><libelle><![CDATA[Piment Brûleur ]]></libelle>
            <montant><![CDATA[25.50]]></montant></produit></produits></infocommande><paiement><type><![CDATA[2]]></type>
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
     * On appelle le webservice avec un flux bien formé mais dont la date d'utilisation est incorrecte :
     * XML avec message KO
     */
    public function testDateUtilisationIncorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8"?>
            <control fianetmodule="api_prestashop_sceau" version="4.5" sceaumodule="2.8"><utilisateur><nom titre="2">
            <![CDATA[TREGUIER]]></nom><prenom><![CDATA[Aline]]></prenom>
            <email><![CDATA[cricri.martini@wanadoo.fr]]></email></utilisateur><infocommande>
            <refid><![CDATA[' . uniqid('xml') . ']]></refid>
            <siteid><![CDATA[' . $this->siteIDFluxXML . ']]></siteid><montant devise="EUR"><![CDATA[20.50]]></montant>
            <ip timestamp="2014-07-15 12:10:37"><![CDATA[185.66.246.23]]></ip><langue><![CDATA[fr]]></langue>
            <dateutilisation>2014-45</dateutilisation></infocommande>
            <paiement><type><![CDATA[2]]></type></paiement><crypt>d7c5485c8721a04092acdff3172cce32</crypt></control>';

        $xml = $this->client->request(
            'POST',
            $this->url,
            array('SiteID' => $this->siteIDFluxXML, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('KO', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice correctement avec un flux minimal : XML avec message OK
     */
    public function testBaseCorrect()
    {
        $this->client->request('GET', '/'); // GET pour initialiser le container

        $container = $this->client->getContainer();
        $site = $container->get('doctrine.orm.entity_manager')->getRepository('FIANETSceauBundle:Site')
            ->find($this->siteIDFluxXML);

        $refid = uniqid('xml');
        $timestamp = '2015-04-02 23:43';
        $email = 'cricri.martini@wanadoo.fr';
        $crypt = $container->get('fianet_sceau.flux')->getCrypt($site->getClePriveeSceau(), $refid, $timestamp, $email);

        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CRISTIANE</prenom>
            <email>' . $email . '</email></utilisateur>
            <infocommande><siteid>' . $site->getId() . '</siteid><refid>' . $refid . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $timestamp . '">83.112.81.91</ip>
            </infocommande><crypt>' . $crypt . '</crypt></control>';

        $xml = $this->client->request(
            'POST',
            $this->url,
            array('SiteID' => $this->siteIDFluxXML, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('OK', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice correctement avec une date d'utilisation : XML avec message OK
     */
    public function testDateUtilisationCorrect()
    {
        $this->client->request('GET', '/'); // GET pour initialiser le container

        $container = $this->client->getContainer();
        $site = $container->get('doctrine.orm.entity_manager')->getRepository('FIANETSceauBundle:Site')
            ->find($this->siteIDFluxXML);

        $refid = uniqid('xml');
        $timestamp = '2015-04-02 23:43';
        $email = 'cricri.martini@wanadoo.fr';
        $crypt = $container->get('fianet_sceau.flux')->getCrypt($site->getClePriveeSceau(), $refid, $timestamp, $email);

        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CRISTIANE</prenom>
            <email>' . $email . '</email></utilisateur>
            <infocommande><siteid>' . $site->getId() . '</siteid><refid>' . $refid . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $timestamp . '">83.112.81.91</ip>
            <dateutilisation>2015-04-10</dateutilisation>
            </infocommande><crypt>' . $crypt . '</crypt></control>';

        $xml = $this->client->request(
            'POST',
            $this->url,
            array('SiteID' => $this->siteIDFluxXML, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('OK', $xml->filterXPath('//result')->attr('type'));
    }


    /**
     * On appelle le webservice correctement avec une langue : XML avec message OK
     */
    public function testLangueCorrect()
    {
        $this->client->request('GET', '/'); // GET pour initialiser le container

        $container = $this->client->getContainer();
        $site = $container->get('doctrine.orm.entity_manager')->getRepository('FIANETSceauBundle:Site')
            ->find($this->siteIDFluxXML);

        $refid = uniqid('xml');
        $timestamp = '2015-04-02 23:43';
        $email = 'cricri.martini@wanadoo.fr';
        $crypt = $container->get('fianet_sceau.flux')->getCrypt($site->getClePriveeSceau(), $refid, $timestamp, $email);

        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CRISTIANE</prenom>
            <email>' . $email . '</email></utilisateur>
            <infocommande><siteid>' . $site->getId() . '</siteid><refid>' . $refid . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $timestamp . '">83.112.81.91</ip>
            <langue>es</langue>
            </infocommande><crypt>' . $crypt . '</crypt></control>';

        $xml = $this->client->request(
            'POST',
            $this->url,
            array('SiteID' => $this->siteIDFluxXML, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('OK', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice correctement : XML avec message OK
     */
    public function testCorrect()
    {
        $this->client->request('GET', '/'); // GET pour initialiser le container

        $container = $this->client->getContainer();
        $site = $container->get('doctrine.orm.entity_manager')->getRepository('FIANETSceauBundle:Site')
            ->find($this->siteIDFluxXML);

        $refid = uniqid('xml');
        $timestamp = '2015-04-02 23:43';
        $email = 'cricri.martini@wanadoo.fr';
        $crypt = $container->get('fianet_sceau.flux')->getCrypt($site->getClePriveeSceau(), $refid, $timestamp, $email);

        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $email . '</email></utilisateur>
            <infocommande><siteid>' . $site->getId() . '</siteid><refid>' . $refid . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $timestamp . '">83.112.81.91</ip><produits>
            <produit><codeean></codeean><id>0335991</id><categorie>70</categorie>
            <libelle>Bracelet or 750 topaze bleue traité</libelle><montant>313.8</montant>
            <image>http://photos.maty.com/0335991/V1/400/bracelet-or-750-topaze-bleue-traitee.jpeg</image></produit>
            </produits><typeLivraison>1-2</typeLivraison></infocommande><paiement><type>5</type></paiement>
            <crypt>' . $crypt . '</crypt></control>';

        $xml = $this->client->request(
            'POST',
            $this->url,
            array('SiteID' => $this->siteIDFluxXML, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('OK', $xml->filterXPath('//result')->attr('type'));
    }


    /**
     * On appelle le webservice correctement avec 1 produit sans les balises facultatives : XML avec message OK
     */
    public function testProduitsSansBalisesFacultatives()
    {
        $this->client->request('GET', '/'); // GET pour initialiser le container

        $container = $this->client->getContainer();
        $site = $container->get('doctrine.orm.entity_manager')->getRepository('FIANETSceauBundle:Site')
            ->find($this->siteIDFluxXML);

        $refid = uniqid('xml');
        $timestamp = '2015-04-02 23:43';
        $email = 'cricri.martini@wanadoo.fr';
        $crypt = $container->get('fianet_sceau.flux')->getCrypt($site->getClePriveeSceau(), $refid, $timestamp, $email);

        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $email . '</email></utilisateur>
            <infocommande><siteid>' . $site->getId() . '</siteid><refid>' . $refid . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $timestamp . '">83.112.81.91</ip><produits>
            <produit><id>0335991</id><categorie>70</categorie>
            <libelle>Bracelet or 750 topaze bleue traité</libelle><montant>313.8</montant></produit>
            </produits><typeLivraison>1-2</typeLivraison></infocommande><paiement><type>5</type></paiement>
            <crypt>' . $crypt . '</crypt></control>';

        $xml = $this->client->request(
            'POST',
            $this->url,
            array('SiteID' => $this->siteIDFluxXML, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('OK', $xml->filterXPath('//result')->attr('type'));
    }

    /**
     * On appelle le webservice avec 1 produit dont il manque une balise obligatoire : XML avec message KO
     */
    public function testProduitsIncorrect()
    {
        $this->client->request('GET', '/'); // GET pour initialiser le container

        $container = $this->client->getContainer();
        $site = $container->get('doctrine.orm.entity_manager')->getRepository('FIANETSceauBundle:Site')
            ->find($this->siteIDFluxXML);

        $refid = uniqid('xml');
        $timestamp = '2015-04-02 23:43';
        $email = 'cricri.martini@wanadoo.fr';
        $crypt = $container->get('fianet_sceau.flux')->getCrypt($site->getClePriveeSceau(), $refid, $timestamp, $email);

        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $email . '</email></utilisateur>
            <infocommande><siteid>' . $site->getId() . '</siteid><refid>' . $refid . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $timestamp . '">83.112.81.91</ip><produits>
            <produit><id>0335991</id><categorie>70</categorie>
            <montant>313.8</montant></produit>
            </produits><typeLivraison>1-2</typeLivraison></infocommande><paiement><type>5</type></paiement>
            <crypt>' . $crypt . '</crypt></control>';

        $xml = $this->client->request(
            'POST',
            $this->url,
            array('SiteID' => $this->siteIDFluxXML, 'XMLInfo' => $xmlInfo, 'CheckSum' => md5($xmlInfo))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/xml'));

        $this->assertEquals('KO', $xml->filterXPath('//result')->attr('type'));
    }
}
