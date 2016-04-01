<?php
/**
 * newsletter42 (www.raum42.at)
 *
 * @link      http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Newsletter42\Command\Mailchimp;

use Zend\Http\Client;
use Zend\Http\Request;

trait MailchimpTrait
{

    protected function send($path, $apiKey, $data = [], $method = Request::METHOD_GET)
    {
        $client = new Client();
        $client->getAdapter()->setOptions(['sslcafile' => 'data/cacert.pem']);

        $dc = substr($apiKey, strpos($apiKey, '-') + 1);
        $uri = "https://{$dc}.api.mailchimp.com/3.0/";

        $client->setUri($uri . $path);
        $client->setAuth('mailchimp', $apiKey);
        $client->setMethod($method);
        if (!empty($data)) {
            $client->setRawBody(json_encode($data));
        }

        $response = $client->send();

        return json_decode($response->getBody(), true);
    }
}
