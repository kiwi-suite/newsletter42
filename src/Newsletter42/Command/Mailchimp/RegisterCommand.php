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

class RegisterCommand extends AbstractCommand
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
     * @param array $mergeFields
     */
    public function setMergeFields($mergeFields)
    {
        $this->mergeFields = $mergeFields;
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

    protected function preExecute()
    {
        if (empty($this->email)) {
            $this->addError('user', 'user must not be empty');
        }

        if (empty($this->lists)) {
            $this->addError('lists', 'lists must not be empty');
        }
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
            'email_address' => $this->email,
            'status' => 'subscribed',
            'merge_fields' => new \stdClass(),
        ];

        if (!empty($this->mergeFields)) {
            $data['merge_fields'] = (object) $this->mergeFields;
        }

        foreach ($this->lists as $list) {
            if (!empty($config['lists'][$list])) {
                $response = $this->send(
                    "lists/{$config['lists'][$list]}/members/{$id}",
                    $apiKey,
                    $data,
                    Request::METHOD_PUT
                );

                if ($response['status'] == 404) {
                    $response = $this->send(
                        "lists/{$config['lists'][$list]}/members",
                        $apiKey,
                        $data,
                        Request::METHOD_POST
                    );
                }
            }
        }
    }
}
