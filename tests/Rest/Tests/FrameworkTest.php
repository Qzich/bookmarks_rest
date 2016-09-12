<?php

namespace Rest\Tests;

use Rest\Framework;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class FrameworkTest extends \PHPUnit_Framework_TestCase
{
    public function testNotFoundHandling()
    {
        $framework = $this->getFrameworkForException(new ResourceNotFoundException());

        $response = $framework->handle(new Request());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testErrorHandling()
    {
        $framework = $this->getFrameworkForException(new \RuntimeException());

        $response = $framework->handle(new Request());

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testControllerResponse()
    {
        $matcher = $this->createMock('Symfony\Component\Routing\Matcher\RequestMatcherInterface');
        $matcher
            ->expects($this->once())
            ->method('matchRequest')
            ->will(
                $this->returnValue(
                    array(
                        'name' => 'Yurii',
                        '_controller' => function ($name) {
                            return ["name" => $name, "greeting" => "Hello"];
                        },
                    )
                )
            );

        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();

        $framework = new Framework($matcher, $controllerResolver, $argumentResolver);

        $response = $framework->handle(new Request());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('{"name":"Yurii","greeting":"Hello"}', $response->getContent());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }


    private function getFrameworkForException($exception)
    {
        $router = $this->createMock('Symfony\Component\Routing\Matcher\RequestMatcherInterface');
        $router
            ->expects($this->once())
            ->method('matchRequest')
            ->will($this->throwException($exception));
        $controllerResolver = $this->createMock('Symfony\Component\HttpKernel\Controller\ControllerResolverInterface');
        $argumentResolver = $this->createMock('Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface');

        return new Framework($router, $controllerResolver, $argumentResolver);
    }
}