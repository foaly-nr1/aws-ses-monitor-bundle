<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

use Aws\Credentials\Credentials;
use Aws\Sns\MessageValidator;
use Doctrine\ORM\EntityManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Topic;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the confirmation of the subscription.
 *
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
class SubscriptionConfirmationHandler extends HandlerAbstract
{
    const HEADER_TYPE = 'SubscriptionConfirmation';

    /** @var EntityManager $entityManager */
    private $entityManager;

    /** @var AwsClientFactory $clientFactory */
    private $clientFactory;

    /**
     * @param EntityManager    $entityManager
     * @param AwsClientFactory $clientFactory
     * @param MessageValidator $messageValidator
     */
    public function __construct(EntityManager $entityManager, AwsClientFactory $clientFactory, MessageValidator $messageValidator)
    {
        parent::__construct($messageValidator);
        $this->entityManager    = $entityManager;
        $this->clientFactory    = $clientFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(Request $request, Credentials $credentials)
    {
        $data = $this->extractDataFromRequest($request);

        // If 'code' exists this is an HTTP status code
        if (isset($data['code'])) {
            return $data;
        }

        if (false === isset($data['Token']) || false === isset($data['TopicArn'])) {
            return ['code' => 403, 'content' => 'Token or TopicArn is missed.'];
        }

        $topicArn = $data['TopicArn'];
        $token    = $data['Token'];

        /** @var Topic $topicEntity */
        $topicEntity = $this->entityManager->getRepository('AwsSesMonitorBundle:Topic')->findOneByTopicArn($topicArn);
        if (null === $topicEntity) {
            return ['code' => 404, 'content' => 'Topic not found'];
        }

        $topicEntity->setToken($token);
        $this->entityManager->persist($topicEntity);

        $client = $this->clientFactory->getSnsClient($credentials);
        $client->confirmSubscription(
            [
                'TopicArn' => $topicEntity->getTopicArn(),
                'Token'    => $topicEntity->getToken()
            ]
        );

        $this->entityManager->flush();

        return ['code' => 200, 'content' => 'OK'];
    }
}
