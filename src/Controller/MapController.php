<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\UX\Map\InfoWindow;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;

class MapController extends AbstractController
{

    public function __construct(private readonly HttpClientInterface $httpClient) {}

    #[Route('/map', name: 'app_map')]
    public function index(): Response
    {
        // 1. Create a new map instance
        $map = (new Map())
            // Explicitly set the center and zoom
            ->center(new Point(48.856614, 2.352222))
            ->zoom(12);
            // Or automatically fit the bounds to the markers
           // ->fitBoundsToMarkers();

        //chercher le fichier JSON 
        $fichier = $this->httpClient->request('GET', 'https://www.data.gouv.fr/fr/datasets/r/1d61b1f4-4730-4dfa-aa44-34220f67f493');
        $points = json_decode($fichier->getContent(), true);
        //Ajouter les points sur la carte 

        foreach ($points as $pt) {
            $map->addMarker(new Marker(
                position: new Point((float)$pt['latitude'], (float)$pt['longitude']),
                title: $pt['nom_site'],
                extra: [
                    'icon_mask_url' => 'https://maps.gstatic.com/mapfiles/place_api/icons/v2/tree_pinlet.svg',
                ],
                infoWindow: new InfoWindow(
                    headerContent: '<b>' . $pt['nom_site'] . '</b>',
                    content: $pt['sports']
                )
            ));
        }
      //  dd($points);
        return $this->render('map/index.html.twig', [
            'map' => $map
        ]);
    }
}
