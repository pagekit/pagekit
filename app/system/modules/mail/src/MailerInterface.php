<?php

namespace Pagekit\Mail;

interface MailerInterface
{
    /**
     * Creates a new message instance.
     *
     * @param  string $subject
     * @param  string $body
     * @param  mixed  $to
     * @param  mixed  $from
     * @return object
     */
    public function create($subject = null, $body = null, $to = null, $from = null);

    /**
     * Sends the given message.
     *
     * @param  mixed $message
     * @param  array $errors
     * @return int
     */
    public function send($message, &$errors = []);

    /**
     * Queues the given message and send it later.
     *
     * @param  mixed $message
     * @param  array $errors
     * @return int
     */
    public function queue($message, &$errors = []);

    /**
     * Registers a plugin.
     *
     * @param object $plugin
     */
    public function registerPlugin($plugin);
}
