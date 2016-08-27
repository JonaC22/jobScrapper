<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
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
        return new Response($this->getJobs('all'));
    }

    /**
     * @Route("/jobs/{slug}", name="jobs_show")
     */
    public function showAction($slug)
    {
        return new Response($this->getJobs($slug));
    }

    /**
     * @param $slug
     * @return string
     */
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
        return $response->getBody()->getContents();
    }

    /**
     * Gets jobs page and do scrapping for show only the job list
     * @return string
     */
    private function getJobs($slug):string
    {
        $body = $this->getJobsPage($slug);
        $crawler = new Crawler($body);
        $crawler = $crawler->filter('#p_ofertas');
        return $crawler->html();
    }
}