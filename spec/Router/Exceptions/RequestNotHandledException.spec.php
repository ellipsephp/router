<?php

use Ellipse\Router\Exceptions\RouterExceptionInterface;
use Ellipse\Router\Exceptions\RequestNotHandledException;

describe('RequestNotHandledException', function () {

    it('should implement RouterExceptionInterface', function () {

        $test = new RequestNotHandledException('GET', '/path');

        expect($test)->toBeAnInstanceOf(RouterExceptionInterface::class);

    });

});
