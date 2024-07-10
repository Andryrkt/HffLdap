<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/trigger-error", name="trigger_error")
     */
    public function triggerError()
    {
        throw new \Exception("This is a test exception");
    }
}
