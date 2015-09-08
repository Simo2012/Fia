<?php
namespace SceauBundle\Tests\Service;

use SceauBundle\Entity\AdministrationType;
use SceauBundle\Entity\Flux;
use SceauBundle\Service\GestionFlux;
use SceauBundle\Entity\FluxStatut;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GestionFluxTest extends KernelTestCase
{
    private $container;
    private $gestionFlux;
    private $translator;
    private $validator;
    private $site;
    private $fluxOk;
    private $fluxStatutATraiter;
    private $fluxStatutEnCoursDeTraitement;

    const SITE_ID = 42;
    const QUESTIONNAIRE_TYPE_ID = 10;

    public function __construct()
    {
        static::bootKernel();

        $this->container = static::$kernel->getContainer();
        $this->validator = $this->container->get('validator');
        $this->translator = $this->container->get('translator');

        $this->fluxStatutEnCoursDeTraitement = $this->getMock('\SceauBundle\Entity\FluxStatut');
        $this->fluxStatutEnCoursDeTraitement->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(FluxStatut::FLUX_EN_COURS_DE_TRAITEMENT));

        $this->fluxStatutATraiter = $this->getMock('\SceauBundle\Entity\FluxStatut');
        $this->fluxStatutATraiter->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(FluxStatut::FLUX_A_TRAITER));

        /* Initialisation d'un site qui peut envoyer des flux */
        $administrationType = $this->getMock('\SceauBundle\Entity\AdministrationType');
        $administrationType->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(AdministrationType::FLUX_XML));

        $this->site = $this->getMockBuilder('\SceauBundle\Entity\Site')
            ->setMethods(['getId', 'getAdministrationType', 'getClePriveeSceau', 'getQuestionnairePersonnalisations'])
            ->getMock();
        $this->site->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::SITE_ID));
        $this->site->expects($this->any())
            ->method('getAdministrationType')
            ->will($this->returnValue($administrationType));
        $this->site->expects($this->any())
            ->method('getClePriveeSceau')
            ->will($this->returnValue('123456789azerty'));
        $questionnaireType = $this->getMock('\SceauBundle\Entity\QuestionnaireType');
        $questionnaireType->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::QUESTIONNAIRE_TYPE_ID));
        $questionnairePersonnalisation = $this->getMock('\SceauBundle\Entity\QuestionnairePersonnalisation');
        $questionnairePersonnalisation->expects($this->any())
            ->method('getQuestionnaireType')
            ->will($this->returnValue($questionnaireType));
        $this->site->expects($this->any())
            ->method('getQuestionnairePersonnalisations')
            ->will($this->returnValue([0 => $questionnairePersonnalisation]));

        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['find', 'aTraiter'])
            ->getMock();
        $repository->expects($this->any())
            ->method('find')
            ->will($this->returnValue($this->site));
        $repository->expects($this->any())
            ->method('aTraiter')
            ->will($this->returnValue($this->fluxStatutATraiter));

        $em = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repository));

        $this->gestionFlux = new GestionFlux($em, $this->validator, $this->translator);

        /* Initialisation données correctes pour les flux */
        $this->fluxOk['refid'] = uniqid('', true);
        $date = new \DateTime();
        $this->fluxOk['timestamp'] = $date->format('Y-m-d h:i:s');
        $this->fluxOk['email'] = 'jp.martini@wanadoo.fr';
        $this->fluxOk['crypt'] = $this->container->get('sceau.flux')
            ->getCrypt(
                $this->site->getClePriveeSceau(),
                $this->fluxOk['refid'],
                $this->fluxOk['timestamp'],
                $this->fluxOk['email']
            );
    }

    /**
     * Permet d'appeler la méthode privée validerReception() de la classe GestionFlux.
     *
     * @param string $xmlInfo XML à passer à la méthode de validation
     *
     * @return Flux Instance de Flux
     */
    private function validerReception($xmlInfo)
    {
        $this->reflector = new ReflectionClass($this->gestionFlux);
        $validerReception = $this->reflector->getMethod('validerReception');
        $validerReception->setAccessible(true);

        return $validerReception
            ->invokeArgs($this->gestionFlux, [$this->site->getId(), $xmlInfo, md5($xmlInfo), '127.0.0.1']);

    }

    /******************* Tests pour la validation du format *******************/

    /**
     * On appelle le service avec que des données vides => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage Le paramètre SiteID est obligatoire.
     */
    public function testSansParametres()
    {
        $this->gestionFlux->inserer(null, null, null, '127.0.0.1');
    }

    /**
     * On appelle le service sans l'identifiant du site => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage Le paramètre SiteID est obligatoire.
     */
    public function testSansSiteID()
    {
        $this->gestionFlux->inserer(null, '<control></control>', md5('<control></control>'), '127.0.0.1');
    }

    /**
     * On appelle le service sans le XML => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage Le paramètre XMLInfo est obligatoire.
     */
    public function testSansXMLInfo()
    {
        $this->gestionFlux->inserer($this->site->getId(), null, md5(uniqid('', true)), '127.0.0.1');
    }

    /**
     * On appelle le service sans le checksum => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage Le paramètre CheckSum est obligatoire.
     */
    public function testSansCheckSum()
    {
        $this->gestionFlux->inserer($this->site->getId(), '<control></control>', null, '127.0.0.1');
    }

    /**
     * On appelle le service avec un site qui n'a pas le bon type d'administration => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage Ce site n'est pas autorisé à poster des flux.
     */
    public function testSiteMauvaiseAdministration()
    {
        $administrationType = $this->getMock('\SceauBundle\Entity\AdministrationType');
        $administrationType->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(AdministrationType::VIA_CERTISSIM));

        $site = $this->getMock('\SceauBundle\Entity\Site');
        $site->expects($this->once())
            ->method('getAdministrationType')
            ->will($this->returnValue($administrationType));

        $siteRepository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $siteRepository->expects($this->once())
            ->method('find')
            ->will($this->returnValue($site));

        $em = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $em->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($siteRepository));

        $gestionFlux = new GestionFlux($em, $this->validator, $this->translator);
        $xmlInfo = uniqid('', true);
        $gestionFlux->inserer($this->site->getId(), $xmlInfo, md5($xmlInfo), '127.0.0.1');
    }

    /**
     * On appelle le service avec un site inexistant => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage Site inexistant ou ne disposant pas des droits d'accès.
     */
    public function testSiteInexistant()
    {
        $siteRepository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $siteRepository->expects($this->once())
            ->method('find')
            ->will($this->returnValue(null));

        $em = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $em->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($siteRepository));

        $gestionFlux = new GestionFlux($em, $this->validator, $this->translator);
        $xmlInfo = uniqid('', true);
        $gestionFlux->inserer($this->site->getId(), $xmlInfo, md5($xmlInfo), '127.0.0.1');

    }

    /**
     * On appelle le service avec un checksum invalide => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage Le checksum est incorrect.
     */
    public function testChecksumInvalide()
    {
        $this->gestionFlux->inserer($this->site->getId(), uniqid('', true), 'incorrect', '127.0.0.1');
    }

    /**
     * On appelle le service avec un flux dont le nom de l'utilisateur trop long => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessageRegExp |(.*)Element 'nom': \[facet 'maxLength'\](.*)|
     */
    public function testNomTropLong()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur>
            <nom titre="2">MARTINIDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD</nom>
            <prenom>CRISTIANE</prenom><email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            </infocommande><crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $this->gestionFlux->inserer($this->site->getId(), $xmlInfo, md5($xmlInfo), '127.0.0.1');
    }

    /**
     * On appelle le service avec un flux dont la date de commande est incorrecte => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessageRegExp |(.*)Element 'ip', attribute 'timestamp': \[facet 'pattern'\](.*)|
     *
     */
    public function testDateIncorrecte()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur>
            <nom titre="2">MARTINI</nom>
            <prenom>CRISTIANE</prenom><email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="2015-21-04">83.112.81.91</ip>
            </infocommande><crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $this->gestionFlux->inserer($this->site->getId(), $xmlInfo, md5($xmlInfo), '127.0.0.1');
    }

    /**
     * On appelle le service avec un flux dont l'email est incorrect => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessageRegExp |(.*)Element 'email': \[facet 'pattern'\](.*)|
     */
    public function testEmailIncorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur>
            <nom titre="2">MARTINI</nom>
            <prenom>CRISTIANE</prenom><email>martin.crisgmail.com</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            </infocommande><crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $this->gestionFlux->inserer($this->site->getId(), $xmlInfo, md5($xmlInfo), '127.0.0.1');
    }

    /**
     * On appelle le service avec un flux dont l'IP est incorrecte => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessageRegExp |(.*)Element 'ip': \[facet 'pattern'\](.*)|
     */
    public function testIPIncorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur>
            <nom titre="2">MARTINI</nom>
            <prenom>CRISTIANE</prenom><email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">112.81.91</ip>
            </infocommande><crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $this->gestionFlux->inserer($this->site->getId(), $xmlInfo, md5($xmlInfo), '127.0.0.1');
    }

    /**
     * On appelle le service avec un flux dont le montant est incorrect => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessageRegExp |(.*)Element 'montant': '(.*)' is not a valid value(.*)|
     */
    public function testMontantIncorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur>
            <nom titre="2">MARTINI</nom>
            <prenom>CRISTIANE</prenom><email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313e.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            </infocommande><crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $this->gestionFlux->inserer($this->site->getId(), $xmlInfo, md5($xmlInfo), '127.0.0.1');
    }

    /**
     * On appelle le service avec un flux dont la date d'utilisation est incorrecte => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessageRegExp |(.*)Element 'dateutilisation': \[facet 'pattern'\](.*)|
     */
    public function testDateUtilisationIncorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur>
            <nom titre="2">MARTINI</nom>
            <prenom>CRISTIANE</prenom><email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            <dateutilisation>2014-45</dateutilisation>
            </infocommande><crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $this->gestionFlux->inserer($this->site->getId(), $xmlInfo, md5($xmlInfo), '127.0.0.1');
    }

    /**
     * On appelle le service correctement avec un flux minimal => on récupère une instance de Flux.
     */
    public function testBaseCorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur>
            <nom titre="1">MARTINI</nom>
            <prenom>JEAN</prenom><email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            </infocommande><crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $flux = $this->validerReception($xmlInfo);

        $this->assertTrue($flux instanceof Flux);
    }

    /**
     * On appelle le service correctement avec un flux complexe : on récupère une instance de Flux.
     */
    public function testComplexeCorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            <produits><produit><codeean>34578787878</codeean><id>0335991</id><categorie>70</categorie>
            <libelle>Bracelet or 750 topaze bleue traité</libelle><montant>313.8</montant>
            <image>http://photos.maty.com/0335991/V1/400/bracelet-or-750-topaze-bleue-traitee.jpeg</image></produit>
            </produits><typeLivraison>1-2</typeLivraison></infocommande><paiement><type>5</type></paiement>
            <crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $flux = $this->validerReception($xmlInfo);

        $this->assertTrue($flux instanceof Flux);
    }

    /**
     * On appelle le service correctement avec une date d'utilisation => on récupère une instance de Flux.
     */
    public function testDateUtilisationCorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur>
            <nom titre="2">MARTINI</nom>
            <prenom>CRISTIANE</prenom><email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            <dateutilisation>2015-04-10</dateutilisation>
            </infocommande><crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $flux = $this->validerReception($xmlInfo);

        $this->assertTrue($flux instanceof Flux);
    }

    /**
     * On appelle le service correctement avec une langue => on récupère une instance de Flux.
     */
    public function testLangueCorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur>
            <nom titre="2">MARTINI</nom>
            <prenom>CRISTIANE</prenom><email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            <langue>es</langue>
            </infocommande><crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $flux = $this->validerReception($xmlInfo);

        $this->assertTrue($flux instanceof Flux);
    }

    /**
     * On appelle le service correctement avec 1 produit sans les balises facultatives => on récupère une instance
     * de Flux.
     */
    public function testProduitsSansBalisesFacultatives()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            <produits><produit><id>0335991</id><categorie>70</categorie>
            <libelle>Bracelet or 750 topaze bleue traité</libelle><montant>313.8</montant></produit>
            </produits><typeLivraison>1-2</typeLivraison></infocommande><paiement><type>5</type></paiement>
            <crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $flux = $this->validerReception($xmlInfo);

        $this->assertTrue($flux instanceof Flux);
    }

    /**
     * On appelle le service avec 1 produit dont il manque une balise obligatoire => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessageRegExp |(.*)Element 'produit': Missing child element(.*)|
     */
    public function testProduitsIncorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            <produits><produit><categorie>70</categorie>
            <libelle>Bracelet or 750 topaze bleue traité</libelle><montant>313.8</montant></produit>
            </produits><typeLivraison>1-2</typeLivraison></infocommande><paiement><type>5</type></paiement>
            <crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $this->gestionFlux->inserer($this->site->getId(), $xmlInfo, md5($xmlInfo), '127.0.0.1');
    }

    /**
     * On appelle le service avec une balise questionnaire non numérique => exception
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessageRegExp |(.*)is not a valid value of the atomic type 'xs:unsignedShort'(.*)|
     */
    public function testQuestionnaireIncorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><questionnaire>un-quest</questionnaire><utilisateur>
            <nom titre="1">MARTINI</nom>
            <prenom>JEAN</prenom><email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            </infocommande><crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $this->gestionFlux->inserer($this->site->getId(), $xmlInfo, md5($xmlInfo), '127.0.0.1');
    }

    /**
     * On appelle le service avec une balise questionnaire dont le format est correct => on récupère une instance
     * de Flux.
     */
    public function testQuestionnaireCorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><questionnaire>10</questionnaire><utilisateur>
            <nom titre="1">CORENTIN</nom>
            <prenom>PIERRE</prenom><email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            </infocommande><crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $flux = $this->validerReception($xmlInfo);

        $this->assertTrue($flux instanceof Flux);
    }

    /******************* Tests pour la validation du contenu *******************/

    /**
     * Permet de créer une instance de Flux pour les tests.
     *
     * @param string $xmlInfo XML à valider
     * @param string $checksum MD5 du XML
     * @param string $ip IP de l'utilisateur
     *
     * @return Flux Instance de Flux
     */
    private function creerInstanceFlux($xmlInfo, $checksum, $ip)
    {
        $flux = new Flux();
        $flux->setXml($xmlInfo);
        $flux->setChecksum($checksum);
        $flux->setDateInsertion(new \DateTime());
        $flux->setIp($ip);
        $flux->setFluxStatut($this->fluxStatutEnCoursDeTraitement);
        $flux->setSite($this->site);

        return $flux;
    }

    /**
     * On valide un flux dont le crypt est incorrect => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage La valeur de la balise crypt est incorrecte.
     */
    public function testCryptIncorrect()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            <produits><produit><id>0335991</id><categorie>70</categorie>
            <libelle>Bracelet or 750 topaze bleue traité</libelle><montant>313.8</montant></produit>
            </produits><typeLivraison>1-2</typeLivraison></infocommande><paiement><type>5</type></paiement>
            <crypt>mauvais_crypt</crypt></control>';

        $this->gestionFlux->validerContenu($this->creerInstanceFlux($xmlInfo, md5($xmlInfo), '127.0.0.1'));
    }

    /**
     * On valide un flux dont l'identifiant du site est différent de l'identifiant de site donné lors de l'étape
     * précedente (réception du flux) => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage La valeur de la balise siteid est incorrecte.
     */
    public function testSiteIdDifferent()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>99997845545</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            <produits><produit><id>0335991</id><categorie>70</categorie>
            <libelle>Bracelet or 750 topaze bleue traité</libelle><montant>313.8</montant></produit>
            </produits><typeLivraison>1-2</typeLivraison></infocommande><paiement><type>5</type></paiement>
            <crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $this->gestionFlux->validerContenu($this->creerInstanceFlux($xmlInfo, md5($xmlInfo), '127.0.0.1'));
    }

    /**
     * On valide un flux dont la date de commande n'existe pas => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage La valeur de l'attribut timestamp est incorrecte.
     */
    public function testDateCommandeInexistante()
    {
        $timestamp = '2015-13-07 15:12:47';
        $crypt = $this->container->get('sceau.flux')
            ->getCrypt(
                $this->site->getClePriveeSceau(),
                $this->fluxOk['refid'],
                $timestamp,
                $this->fluxOk['email']
            );

        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="'. $timestamp .'">83.112.81.91</ip>
            <produits><produit><id>0335991</id><categorie>70</categorie>
            <libelle>Bracelet or 750 topaze bleue traité</libelle><montant>313.8</montant></produit>
            </produits><typeLivraison>1-2</typeLivraison></infocommande><paiement><type>5</type></paiement>
            <crypt>' . $crypt . '</crypt></control>';

        $this->gestionFlux->validerContenu($this->creerInstanceFlux($xmlInfo, md5($xmlInfo), '127.0.0.1'));
    }

    /**
     * On valide un flux dont la date de commande est dans le futur => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage La valeur de l'attribut timestamp est supérieure à la date du jour.
     */
    public function testDateCommandeFuturiste()
    {
        $timestamp = '2050-01-07 15:12:47';
        $crypt = $this->container->get('sceau.flux')
            ->getCrypt(
                $this->site->getClePriveeSceau(),
                $this->fluxOk['refid'],
                $timestamp,
                $this->fluxOk['email']
            );

        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="'. $timestamp .'">83.112.81.91</ip>
            <produits><produit><id>0335991</id><categorie>70</categorie>
            <libelle>Bracelet or 750 topaze bleue traité</libelle><montant>313.8</montant></produit>
            </produits><typeLivraison>1-2</typeLivraison></infocommande><paiement><type>5</type></paiement>
            <crypt>' . $crypt . '</crypt></control>';

        $this->gestionFlux->validerContenu($this->creerInstanceFlux($xmlInfo, md5($xmlInfo), '127.0.0.1'));
    }

    /**
     * On valide un flux dont la date de commande est trop ancienne => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage La valeur de l'attribut timestamp est trop ancienne.
     */
    public function testDateCommandeTropAncienne()
    {
        $timestamp = '2000-01-07 15:12:47';
        $crypt = $this->container->get('sceau.flux')
            ->getCrypt(
                $this->site->getClePriveeSceau(),
                $this->fluxOk['refid'],
                $timestamp,
                $this->fluxOk['email']
            );

        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="'. $timestamp .'">83.112.81.91</ip>
            <produits><produit><id>0335991</id><categorie>70</categorie>
            <libelle>Bracelet or 750 topaze bleue traité</libelle><montant>313.8</montant></produit>
            </produits><typeLivraison>1-2</typeLivraison></infocommande><paiement><type>5</type></paiement>
            <crypt>' . $crypt . '</crypt></control>';

        $this->gestionFlux->validerContenu($this->creerInstanceFlux($xmlInfo, md5($xmlInfo), '127.0.0.1'));
    }

    /**
     * On valide un flux dont la date d'utilisation n'existe pas => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage La valeur de la balise dateutilisation est incorrecte.
     */
    public function testDateUtilisationInexistante()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            <produits><produit><id>0335991</id><categorie>70</categorie>
            <libelle>Bracelet or 750 topaze bleue traité</libelle><montant>313.8</montant></produit>
            </produits><typeLivraison>1-2</typeLivraison>
            <dateutilisation>2015-01-32</dateutilisation></infocommande><paiement><type>5</type></paiement>
            <crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $this->gestionFlux->validerContenu($this->creerInstanceFlux($xmlInfo, md5($xmlInfo), '127.0.0.1'));
    }

    /**
     * On valide un flux dont la date d'utilisation est déjà passé => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage La valeur de la balise dateutilisation est antérieure à la date du jour.
     */
    public function testDateUtilisationPassee()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            <produits><produit><id>0335991</id><categorie>70</categorie>
            <libelle>Bracelet or 750 topaze bleue traité</libelle><montant>313.8</montant></produit>
            </produits><typeLivraison>1-2</typeLivraison>
            <dateutilisation>2015-01-07</dateutilisation></infocommande><paiement><type>5</type></paiement>
            <crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $this->gestionFlux->validerContenu($this->creerInstanceFlux($xmlInfo, md5($xmlInfo), '127.0.0.1'));
    }

    /**
     * On valide un flux dont le questionnaire donné ne peut être utilisé par le site => exception
     *
     * @expectedException SceauBundle\Exception\FluxException
     * @expectedExceptionMessage Vous n'êtes pas autorisé à utiliser ce type de questionnaire.
     */
    public function testQuestionnaireNonAutorise()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><questionnaire>7</questionnaire>
            <utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            <produits><produit><id>0335991</id><categorie>70</categorie>
            <libelle>Bracelet or 750 topaze bleue traité</libelle><montant>313.8</montant></produit>
            </produits><typeLivraison>1-2</typeLivraison></infocommande><paiement><type>5</type></paiement>
            <crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        $this->gestionFlux->validerContenu($this->creerInstanceFlux($xmlInfo, md5($xmlInfo), '127.0.0.1'));
    }

    /**
     * On valide un flux correct => aucune exception
     */
    public function testContenuOK()
    {
        $xmlInfo = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <control><questionnaire>'.
            $this->site->getQuestionnairePersonnalisations()[0]->getQuestionnaireType()->getId() .'</questionnaire>
            <utilisateur><nom titre="2">MARTINI</nom><prenom>CHRISTIANE</prenom>
            <email>' . $this->fluxOk['email'] . '</email></utilisateur>
            <infocommande><siteid>' . $this->site->getId() . '</siteid><refid>' . $this->fluxOk['refid'] . '</refid>
            <montant devise="EUR">313.8</montant><ip timestamp="' . $this->fluxOk['timestamp'] . '">83.112.81.91</ip>
            <produits><produit><id>0335991</id><categorie>70</categorie>
            <libelle>Bracelet or 750 topaze bleue traité</libelle><montant>313.8</montant></produit>
            </produits><typeLivraison>1-2</typeLivraison></infocommande><paiement><type>5</type></paiement>
            <crypt>' . $this->fluxOk['crypt'] . '</crypt></control>';

        try {
            $this->gestionFlux->validerContenu($this->creerInstanceFlux($xmlInfo, md5($xmlInfo), '127.0.0.1'));
            $this->assertTrue(true);

        } catch (\Exception $e) {
            $this->fail();
        }
    }
}
