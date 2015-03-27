<?php

namespace Pagekit\Mail;

interface MessageInterface
{
    /**
     * Gets the mailer instance.
     *
     * @return MailerInterface
     */
    public function getMailer();

    /**
     * Sets the mailer instance.
     *
     * @return self
     */
    public function setMailer(MailerInterface $mailer);

    /**
     * Sends the message.
     *
     * @param  array $errors
     * @return int
     */
    public function send(&$errors = null);

    /**
     * Queues the message for later sending.
     *
     * @param  array $errors
     * @return int
     */
    public function queue(&$errors = null);
}
