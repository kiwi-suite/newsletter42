<?php
/**
 * newsletter42 (www.raum42.at)
 *
 * @link      http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Newsletter42\Selector\Mailchimp;

use Core42\Selector\AbstractSelector;
use Newsletter42\Command\Mailchimp\MailchimpTrait;

class UserSubscriptionSelector extends AbstractSelector
{
    use MailchimpTrait;

    /**
     * @var string
     */
    protected $email;

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        $config = $this->getServiceManager()->get('Config');
        $config = $config['newsletter']['mailchimp'];

        $apiKey = $config['api_key'];

        $id = md5(strtolower($this->email));

        $subscribed = [];
        foreach ($config['lists'] as $name => $list) {
            $response = $this->send("lists/{$list}/members/{$id}", $apiKey);
            if ($response['status'] != 404 && $response['status'] == 'subscribed') {
                $subscribed[] = $name;
            }
        }
        return $subscribed;
    }
}
