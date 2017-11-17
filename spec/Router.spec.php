<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Ellipse\Router;
use Ellipse\Router\Adapter\Handler;
use Ellipse\Router\Adapter\Match;
use Ellipse\Router\Adapter\RouterAdapterInterface;
use Ellipse\Router\Exceptions\RequestNotHandledException;

describe('Router', function () {

    beforeEach(function () {

        $this->adapter = mock(RouterAdapterInterface::class);

        $this->router = new Router($this->adapter->get());

        $this->request1 = mock(ServerRequestInterface::class);
        $this->request2 = mock(ServerRequestInterface::class);

        $this->request1->getMethod->returns('GET');
        $this->request1->withMethod->returns($this->request2);

    });

    describe('->getMatchName()', function () {

        context('when the ->handle() method has not been called yet', function () {

            it('should throw a RequestNotHandledException', function () {

                $test = function () {

                    $this->router->getMatchName();

                };

                $exception = new RequestNotHandledException;

                expect($test)->toThrow($exception);

            });

        });

        context('when the ->handle() method has been called', function () {

            it('should return the match name', function () {

                $handler = mock(Handler::class)->get();

                $match = new Match('name', $handler);

                $this->adapter->match->returns($match);

                $this->router->handle($this->request1->get());

                $test = $this->router->getMatchName();

                expect($test)->toEqual('name');

            });

        });

    });

    describe('->handle()', function () {

        beforeEach(function () {

            $this->response = mock(ResponseInterface::class);

        });

        it('should get a match from the given adapter then proxy its ->handle() method', function () {

            $match = mock(Match::class);

            $this->adapter->match->with($this->request2)->returns($match);

            $match->handle->with($this->request2)->returns($this->response);

            $test = $this->router->handle($this->request1->get());

            expect($test)->toBe($this->response->get());

        });

        context('when the given request does not contain an input overwriting the method', function () {

            context('when the given request method is uppercased', function () {

                it('should use a request with the same method', function () {

                    $this->request1->getMethod->returns('GET');

                    $this->router->handle($this->request1->get());

                    $this->request1->withMethod->calledWith('GET');

                });

            });

            context('when the given request method is lowercased', function () {

                it('should use a request with the uppercased method', function () {

                    $this->request1->getMethod->returns('get');

                    $this->router->handle($this->request1->get());

                    $this->request1->withMethod->calledWith('GET');

                });

            });

        });

        context('when the given request contains an input overwriting the method', function () {

            context('when the input value is uppercased', function () {

                it('should use a request with the method from the input', function () {

                    $this->request1->getParsedBody->returns([
                        Router::METHOD_INPUT_KEY => 'PUT',
                    ]);

                    $this->router->handle($this->request1->get());

                    $this->request1->withMethod->calledWith('PUT');

                });

            });

            context('when the input value is lowercased', function () {

                it('should use a request with the uppercased method from the input', function () {

                    $this->request1->getParsedBody->returns([
                        Router::METHOD_INPUT_KEY => 'put',
                    ]);

                    $this->router->handle($this->request1->get());

                    $this->request1->withMethod->calledWith('PUT');

                });

            });

        });

    });

});
