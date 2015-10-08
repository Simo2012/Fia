<?php

namespace SceauBundle\Controller\Extranet;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\GeoChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AccueilController extends Controller
{
    /**
     * Affiche la page d'accueil de l'extranet.
     *
     * @Route("/", name="extranet_accueil")
     * @Method("GET")
     */
    public function indexAction()
    {
        $menu = $this->get('sceau.extranet.menu');
        $menu->getChild('accueil')->setCurrent(true);

        $pieChart = new PieChart();
        $pieChart->setElementID('pieTreshold');
        $pieChart->getData()->setArrayToTable([
            [['label' => 'Pizza', 'type' => 'string'], ['label' => 'Populartiy', 'type' => 'number']],
            ['Pepperoni', 33],
            ['Hawaiian', 26],
            ['Mushroom', 22],
            ['Sausage', 10], // Below limit.
            ['Anchovies', 9] // Below limit.
        ]);
        $pieChart->getOptions()->setTitle('Popularity of Types of Pizza');
        $pieChart->getOptions()->setSliceVisibilityThreshold(.2);
        $pieChart->getOptions()->setHeight(250);
        $pieChart->getOptions()->setWidth(450);

        $geoChart = new GeoChart();
        $geoChart->getData()->setArrayToTable(
            [
                ['City',   'Population', 'Area'],
                ['Rome',      2761477,    1285.31],
                ['Milan',     1324110,    181.76],
                ['Naples',    959574,     117.27],
                ['Turin',     907563,     130.17],
                ['Palermo',   655875,     158.9],
                ['Genoa',     607906,     243.60],
                ['Bologna',   380181,     140.7],
                ['Florence',  371282,     102.41],
                ['Fiumicino', 67370,      213.44],
                ['Anzio',     52192,      43.43],
                ['Ciampino',  38262,      11]
            ]
        );
        $geoChart->getOptions()->setRegion('IT');
        $geoChart->getOptions()->setDisplayMode('markers');
        $geoChart->getOptions()->getColorAxis()->setColors(['green', 'blue']);
        $geoChart->getOptions()->setWidth(700);
        $geoChart->getOptions()->setHeight(400);

        $lineChart = new LineChart();
        $lineChart->getData()->setArrayToTable([
            [['label' => 'X', 'type' => 'number'], ['label' => 'Dogs', 'type' => 'number'],
                ['label' => 'Cats', 'type' => 'number']],
            [0, 0, 0],    [1, 10, 5],   [2, 23, 15],  [3, 17, 9],   [4, 18, 10],  [5, 9, 5],
            [6, 11, 3],   [7, 27, 19],  [8, 33, 25],  [9, 40, 32],  [10, 32, 24], [11, 35, 27],
            [12, 30, 22], [13, 40, 32], [14, 42, 34], [15, 47, 39], [16, 44, 36], [17, 48, 40],
            [18, 52, 44], [19, 54, 46], [20, 42, 34], [21, 55, 47], [22, 56, 48], [23, 57, 49],
            [24, 60, 52], [25, 50, 42], [26, 52, 44], [27, 51, 43], [28, 49, 41], [29, 53, 45],
            [30, 55, 47], [31, 60, 52], [32, 61, 53], [33, 59, 51], [34, 62, 54], [35, 65, 57],
            [36, 62, 54], [37, 58, 50], [38, 55, 47], [39, 61, 53], [40, 64, 56], [41, 65, 57],
            [42, 63, 55], [43, 66, 58], [44, 67, 59], [45, 69, 61], [46, 69, 61], [47, 70, 62],
            [48, 72, 64], [49, 68, 60], [50, 66, 58], [51, 65, 57], [52, 67, 59], [53, 70, 62],
            [54, 71, 63], [55, 72, 64], [56, 73, 65], [57, 75, 67], [58, 70, 62], [59, 68, 60],
            [60, 64, 56], [61, 60, 52], [62, 65, 57], [63, 67, 59], [64, 68, 60], [65, 69, 61],
            [66, 70, 62], [67, 72, 64], [68, 75, 67], [69, 80, 72]
        ]);
        $lineChart->getOptions()->getHAxis()->setTitle('Time');
        $lineChart->getOptions()->getHAxis()->getTextStyle()->setColor('#01579b');
        $lineChart->getOptions()->getHAxis()->getTextStyle()->setFontSize(20);
        $lineChart->getOptions()->getHAxis()->getTextStyle()->setFontName('Arial');
        $lineChart->getOptions()->getHAxis()->getTextStyle()->setBold(true);
        $lineChart->getOptions()->getHAxis()->getTextStyle()->setItalic(true);
        $lineChart->getOptions()->getHAxis()->getTitleTextStyle()->setColor('#01579b');
        $lineChart->getOptions()->getHAxis()->getTitleTextStyle()->setFontSize(16);
        $lineChart->getOptions()->getHAxis()->getTitleTextStyle()->setFontName('Arial');
        $lineChart->getOptions()->getHAxis()->getTitleTextStyle()->setBold(false);
        $lineChart->getOptions()->getHAxis()->getTitleTextStyle()->setItalic(true);
        $lineChart->getOptions()->getVAxis()->setTitle('Popularity');
        $lineChart->getOptions()->getVAxis()->getTextStyle()->setColor('#1a237e');
        $lineChart->getOptions()->getVAxis()->getTextStyle()->setFontSize(24);
        $lineChart->getOptions()->getVAxis()->getTextStyle()->setBold(true);
        $lineChart->getOptions()->getVAxis()->getTitleTextStyle()->setColor('#1a237e');
        $lineChart->getOptions()->getVAxis()->getTitleTextStyle()->setFontSize(24);
        $lineChart->getOptions()->getVAxis()->getTitleTextStyle()->setBold(true);
        $lineChart->getOptions()->setColors(['#a52714', '#097138']);
        $lineChart->getOptions()->setWidth(500);
        $lineChart->getOptions()->setHeight(400);

        return $this->render(
            "SceauBundle:Extranet/Accueil:index.html.twig",
            array('pieChart' => $pieChart, 'geoChart' => $geoChart, 'lineChart' => $lineChart)
        );


    }
}
