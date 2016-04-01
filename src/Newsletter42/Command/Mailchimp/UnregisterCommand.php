<?php
/**
 * newsletter42 (www.raum42.at)
 *
 * @link      http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Newsletter42\Command\Mailchimp;

use Core42\Command\AbstractCommand;
use Zend\Http\Request;

class UnregisterCommand extends AbstractCommand
{
    use MailchimpTrait;

    /**
     * @var string
     */
    protected $email;

    protected $lists = [];

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
     * @param string $list
     * @return $this
     */
    public function addList($list)
    {
        $this->lists[] = $list;
        return $this;
    }

    /**
     * @param array $lists
     * @return $this
     */
    public function setLists($lists)
    {
        $this->lists = $lists;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        $config = $this->getServiceManager()->get('Config');
        $config = $config['newsletter']['mailchimp'];

        $apiKey = $config['api_key'];

        $id = md5(strtolower($this->email));

        $data = [
            'status' => 'unsubscribed',
        ];

        foreach ($this->lists as $list) {
            if (!empty($config['lists'][$list])) {
                try {
                    $response = $this->send("lists/{$config['lists'][$list]}/members/{$id}",
                        $apiKey, $data, Request::METHOD_PUT);
                } catch (\Exception $e) {

                }
            }
        }
    }
}
