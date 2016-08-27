<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('jobs_list');
    }

    /**
     * @Route("/jobs", name="jobs_list")
     */
    public function listAction()
    {
        $body = $this->getJobsPage('all');
        return new Response($body);
    }

    /**
     * @Route("/jobs/{slug}", name="jobs_show")
     */
    public function showAction($slug)
    {
        $body = $this->getJobsPage($slug);
        return new Response($body);
    }

    private function getJobsPage($slug)
    {
        switch($slug){
            case 'remote':
                $endpoint = 'empleos-de-informatica-y-telecom-jornada-desde-casa';
                break;
            case 'all':
            default:
                $endpoint = 'empleos-de-informatica-y-telecom';
                break;
        }

        $client = new \GuzzleHttp\Client(['base_uri' => 'http://www.computrabajo.com.ar/']);
        $response = $client->request('GET', $endpoint);
        return $response->getBody();
    }
}