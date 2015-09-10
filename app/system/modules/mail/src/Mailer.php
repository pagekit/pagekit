<?php

namespace Pagekit\Mail;

use Swift_RfcComplianceException;
use Swift_SmtpTransport;
use Swift_SpoolTransport;
use Swift_Transport;
use Swift_TransportException;

class Mailer implements MailerInterface
{
    /**
     * The Swift Transport instance.
     *
     * @var Swift_Transport
     */
    protected $trans;

    /**
     * The Swift Spool Transport instance.
     *
     * @var Swift_SpoolTransport
     */
    protected $queue;

    /**
     * Create a new Mailer instance.
     *
     * @param Swift_Transport      $trans
     * @param Swift_SpoolTransport $queue
     */
    public function __construct(Swift_Transport $trans, Swift_SpoolTransport $queue)
    {
        $this->trans = $trans;
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function create($subject = null, $body = null, $to = null, $from = null)
    {
        $message = new Message($subject, $body);

        if ($to !== null) {
            $message->setTo($to);
        }

        if ($from !== null) {
            $message->setFrom($from);
        }

        return $message->setMailer($this);
    }

    /**
     * {@inheritdoc}
     */
    public function send($message, &$errors = null)
    {
        $errors = (array) $errors;

        if (!$this->trans->isStarted()) {
            $this->trans->start();
        }

        $sent = 0;

        try {
            $sent = $this->trans->send($message, $errors);
        } catch (Swift_RfcComplianceException $e) {
            foreach ($message->getTo() as $address => $name) {
                $errors[] = $address;
            }
        }

        return $sent;
    }

    /**
     * {@inheritdoc}
     */
    public function queue($message, &$errors = null)
    {
        return $this->queue->send($message, $errors);
    }

    /**
     * {@inheritdoc}
     */
    public function registerPlugin($plugin)
    {
        $this->trans->registerPlugin($plugin);
    }

    /**
     * Test smtp connection with given settings.
     *
     * @param  string  $host
     * @param  integer $port
     * @param  string  $username
     * @param  string  $password
     * @param  string  $encryption
     * @throws Swift_TransportException
     */
    public function testSmtpConnection($host = 'localhost', $port = 25, $username = '', $password = '', $encryption = null)
    {
        Swift_SmtpTransport::newInstance($host, $port)
            ->setUsername($username)
            ->setPassword($password)
            ->setEncryption($encryption)
            ->start();
    }
}
