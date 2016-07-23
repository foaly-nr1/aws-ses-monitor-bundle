<?php
namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the coplaints.
 */
class ComplaintsController extends Controller
{
    public function complaintsAction(Request $request)
    {
        $factory = $this->get('aws_ses_monitor.handler.factory');
        $monitorHandler = $factory->buildHandler($request);
        $responseCode  = $monitorHandler->handleRequest($request);
        return new Response('', $responseCode);
    }
}