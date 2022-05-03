<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model\Mail\MessageBuilder;

use Magento\Framework\Mail\EmailMessageInterface;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Zend\Mail\Message as ZendMail;

class MessageBuilder
{
    /**
     * @var bool
     */
    private $legacyBuild = true;

    /**
     * @var EmailMessageInterfaceFactory|null
     */
    protected $emailMessageInterfaceFactory = null;

    /**
     * @var MimeMessageInterfaceFactory|null
     */
    protected $mimeMessageInterfaceFactory = null;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        if (interface_exists(EmailMessageInterface::class)) {
            $this->legacyBuild = false;
            $this->emailMessageInterfaceFactory = $objectManager->get(EmailMessageInterfaceFactory::class);
            $this->mimeMessageInterfaceFactory = $objectManager->get(MimeMessageInterfaceFactory::class);
        }
    }

    /**
     * Build email message
     *
     * @param EmailMessageInterface|MessageInterface $message
     *
     * @return EmailMessageInterface
     */
    public function build($message)
    {
        if (!$this->legacyBuild) {
            $parts = $message->getBody() ? $message->getBody()->getParts() : [];
            $messageData['body'] = $this->mimeMessageInterfaceFactory->create(
                ['parts' => $parts]
            );
            $oldMessage = ZendMail::fromString($message->getRawMessage());
            $messageData['from'][] = $oldMessage->getFrom()->current();
            $messageData['to'][] = $oldMessage->getTo()->current();

            if ($oldMessage->getTo()->current()) {
                foreach ($oldMessage->getTo() as $address) {
                    $messageData['to'][] = $address;
                }
            }

            if ($oldMessage->getBcc()->current()) {
                foreach ($oldMessage->getBcc() as $address) {
                    $messageData['bcc'][] = $address;
                }
            }

            $messageData['subject'] = $oldMessage->getSubject();
            $message = $this->emailMessageInterfaceFactory->create($messageData);
        }

        return $message;
    }
}
