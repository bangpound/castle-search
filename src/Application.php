<?php

class Application extends Silex\Application
{
    use Silex\Application\MonologTrait;
    use Silex\Application\TwigTrait;
    use Silex\Application\SecurityTrait;
    use Silex\Application\UrlGeneratorTrait;
    use Silex\Application\FormTrait;
}
