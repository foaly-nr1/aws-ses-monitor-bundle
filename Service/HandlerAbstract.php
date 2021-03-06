<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles notifications.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
abstract class HandlerAbstract implements HandlerInterface
{
    /** @var MessageValidator */
    private $messageValidator;

    /**
     * @param MessageValidator $messageValidator
     */
    public function __construct(MessageValidator $messageValidator)
    {
        $this->messageValidator = $messageValidator;
    }

    /**
     * @param Request $request
     *
     * @return int|array
     */
    public function extractDataFromRequest(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return ['code' => 405, 'content' => 'Only POST requests are accepted.'];
        }

        try {
            $data      = json_decode($request->getContent(), true);
            $message   = new Message($data);

            if (false === $this->messageValidator->isValid($message)) {
                return ['code' => 403, 'content' => 'The message is invalid.'];
            }

            return $data;
        } catch (\Exception $e) {
            return ['code' => 403, 'content' => 'Exception: ' . $e->getMessage()];
        }
    }
}
