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
    private function getJobsPage($slug, $page)
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

        if (!is_numeric($page) || $page < 1 || $page > 10) {
            $page = '';
        }

        $client = new \GuzzleHttp\Client(['base_uri' => 'http://www.computrabajo.com.ar/']);
        $response = $client->request('GET', $endpoint . '?p=' . $page);
        return $response->getBody()->getContents();
    }

    /**
     * Gets jobs page and do scrapping for show only the job list
     * @return string
     */
    private function getJobs($slug):string
    {
        $html = '';
        for ($page = 0; $page < 10; $page++) {
            $body = $this->getJobsPage($slug, $page);
            $crawler = new Crawler($body);
            $crawler = $crawler->filter('#p_ofertas');
            $html .= $crawler->html();
        }
        return $html;
    }
}