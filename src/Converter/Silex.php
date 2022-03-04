<?php

namespace Bramus\Datatypes\Converter;

use Silex\Application;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class Silex
{
	abstract public function convert($item, Application $app);

    public function getNotFoundValue($item, Application $app) {
        return null;
    }

    public function optionalConvert($item, Application $app) {
        try {
            $converted = $this->convert($item, $app);
        }
        // Allow not founds
        catch (NotFoundHttpException $e) {
            return $this->getNotFoundValue($item, $app);
        }
        // Disallow bad hierarchies
        catch (BadRequestHttpException $e) {
            throw $e;
        }

        return $converted;
    }

}