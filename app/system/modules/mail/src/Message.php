<?php

namespace Pagekit\Mail;

use Swift_Attachment;
use Swift_Image;
use Swift_Message;
use Swift_Mime_Attachment;

class Message extends Swift_Message implements MessageInterface
{
    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * {@inheritdoc}
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function setMailer(MailerInterface $mailer)
    {
        $this->mailer = $mailer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function send(&$errors = null)
    {
        return $this->mailer->send($this, $errors);
    }

    /**
     * {@inheritdoc}
     */
    public function queue(&$errors = null)
    {
        return $this->mailer->queue($this, $errors);
    }

    /**
     * Attaches a file to the message.
     *
     * @param  string $file
     * @param  string $name
     * @param  string $mime
     * @return self
     */
    public function attachFile($file, $name = null, $mime = null)
    {
		return $this->prepareAttachment(Swift_Attachment::fromPath($file), $name, $mime);
    }

    /**
     * Attaches in-memory data as an attachment.
     *
     * @param  string $data
     * @param  string $name
     * @param  string $mime
     * @return self
     */
    public function attachData($data, $name, $mime = null)
    {
        return $this->prepareAttachment(Swift_Attachment::newInstance($data, $name), null, $mime);
    }

    /**
     * Embeds a file in the message and get the CID.
     *
     * @param  string $file
     * @param  string $cid
     * @return string
     */
    public function embedFile($file, $cid = null)
    {
        $attachment = Swift_Image::fromPath($file);

        if ($cid) {
            $attachment->setId(strpos($cid, 'cid:') === 0 ? $cid : 'cid:'.$cid);
        }

        return $this->embed($attachment);
    }

    /**
     * Embeds in-memory data in the message and get the CID.
     *
     * @param  string $data
     * @param  string $name
     * @param  string $contentType
     * @return string
     */
    public function embedData($data, $name, $contentType = null)
    {
		return $this->embed(Swift_Image::newInstance($data, $name, $contentType));
    }

	/**
	 * Prepare and attach the given attachment.
	 *
	 * @param  Swift_Mime_Attachment $attachment
	 * @param  string                $name
     * @param  string                $mime
	 * @return self
	 */
	protected function prepareAttachment(Swift_Mime_Attachment $attachment, $name = null, $mime = null)
	{
		if (null !== $mime) {
			$attachment->setContentType($mime);
		}

		if (null !== $name) {
			$attachment->setFilename($name);
		}

		$this->attach($attachment);

		return $this;
	}
}
