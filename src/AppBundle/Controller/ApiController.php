<?php

namespace AppBundle\Controller;

use AppBundle\Utils\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    /**
     * @Route("/dummyRestServer")
     * description: aaaaa... póki poprzednia aplikacja nie jest skończona - generuje gloopie dane ;)
     */
    public function dummyRestServerAction()
    {
        $dane1 = new Ticket('Bilety Polska Czechy Bilet Wr...IO HIT', 'http://allegro.pl/show_item.php?item=57621555201','description1', 15, 'sport' );
        $dane2 = new Ticket('Bilety inny ', 'http://allegro.pl/show_item.php?item=57621533201','description32', 5, 'sport' );
        $dane3 = new Ticket('Bilety trzeci ', 'http://allegro.pl/show_item.php?item=67821533201','description long', 10, 'sport' );

        $table = array($dane1, $dane2, $dane3);


        return new JsonResponse($table);
    }




    /**
     * @Route("/import")
     */
    public function importAction()
    {
        $result = $this->callApi('GET', 'http://localhost:8080/tickets-filter/app_dev.php/dummyRestServer');
        $dateFromRest = json_decode($result);
        print_r($dateFromRest);
        // Piotrka clasa wyszukująca
        //tworze instancje jego klasy
        //wywoluje metody jego klasy na moich $dataFromRest
        //zwracam nowego JsonResponsa (od wyfiltorwanych danych)
        return new Response($dateFromRest);
    }

    private function callApi($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }


}
