<?php

namespace App\Content;

use App\Core\Log;
use App\Exceptions\PageException;
use Twig;
use App\Core\Config;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Generates and displays page content.
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */
class Page
{
    /**
     * @var array $manifest Manifest data for the revisioned static assets.
     */
    private static $manifest;

    public function __construct()
    {

    }

    /**
     * Displays the requested page.
     *
     * @param string $uri URI of the requested page.
     * @throws Twig\Error\LoaderError
     * @throws Twig\Error\RuntimeError
     * @throws Twig\Error\SyntaxError
     */
    public function display($uri = '/')
    {
        $settings = Config::get('app');
        $templatesPath = sprintf('%s/%s', $settings['basePath'], $settings['twig']['templatesPath']);
        $cachePath = sprintf('%s/%s', $settings['basePath'], $settings['twig']['cachePath']);

        // Loading the static assets manifest file if revisioning is enabled.
        try {
            if ($settings['twig']['revisionAssets'] && !isset(self::$manifest)) {
                $manifestPath = sprintf('%s/%s', $settings['basePath'], $settings['twig']['manifestPath']);
                if (!is_file($manifestPath)) {
                    throw new PageException(PageException::MISSING_MANIFEST_FILE, ['manifestPath' => $manifestPath]);
                }
                self::$manifest = json_decode(file_get_contents(realpath($manifestPath)), true);
            }
        } catch (PageException $e) {
            Log::log($e->getMessage(), $e->getSeverity(), $e->getContext());
        }

        // Making sure a request for '/' points to the index page.
        if ($uri == '/') {
            $uri = 'index';
        }

        // Instantiating the Twig templating engine.
        $loader = new FilesystemLoader($templatesPath);
        $twig = new Environment($loader, [
            'cache' => $cachePath,
            'debug' => $settings['twig']['debugMode']
        ]);
        // Adding the app-wide Twig extension.
        $twig->addExtension(new TwigAssets());

        // Displaying the actual page.
        if (is_file(realpath(sprintf('%s/pages/%s.twig', $templatesPath, $uri)))) {
            echo $twig->render(sprintf('pages/%s.twig', $uri), ['environment' => Config::getEnvironment()]);
        } else {
            $this->show404();
        }
    }

    /**
     * Returns the static assets manifest.
     */
    public static function getManifest()
    {
        return self::$manifest;
    }

    /**
     * Returns a 404 not found page with a proper status code.
     */
    public static function show404()
    {
        http_response_code(404);
        $page = new Page();
        $page->display('/404');
    }
}
