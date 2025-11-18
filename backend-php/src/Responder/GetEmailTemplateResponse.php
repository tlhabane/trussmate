<?php

namespace App\Responder;

use App\Factory\ContainerFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Exception;

final class GetEmailTemplateResponse
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws SyntaxError
     * @throws ContainerExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public static function invoke(string $template, array $data): string
    {
        $container = (new ContainerFactory())->createInstance();
        $settings = $container->get('settings');
        $twigSettings = $settings['twig'];

        $options = $twigSettings['options'];
        $options['cache'] = $options['cache_enabled'] ? $options['cache_path'] : false;

        $loader = new FilesystemLoader();
        $paths = is_array($twigSettings['paths']) ? $twigSettings['paths'] : [$twigSettings['paths']];
        foreach ($paths as $namespace => $path) {
            if (is_string($namespace)) {
                $loader->setPaths($path, $namespace);
            } else {
                $loader->addPath($path);
            }
        }

        $twig = new Environment($loader, $options);
        return $twig->render($template, $data);
    }
}
