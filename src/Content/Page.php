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

        // Adding static asset revisioning, if enabled.
        $revision = new Twig\TwigFunction('assets', function ($originalAsset) use ($settings) {
            try {
                $revisionedAsset = $originalAsset;

                if ($settings['twig']['revisionAssets']) {
                    $manifestPath = sprintf('%s/%s', $settings['basePath'], $settings['twig']['manifestPath']);

                    if (!is_file($manifestPath)) {
                        throw new PageException(PageException::MISSING_MANIFEST_FILE, ['manifestPath' => $manifestPath]);
                    }

                    // Reading the manifest file and searching for a static asset replacement candidate.
                    $manifest = json_decode(file_get_contents(realpath($manifestPath)), true);
                    foreach ($manifest as $manifestOriginal => $manifestRevisioned) {
                        if ($manifestOriginal === $originalAsset) {
                            $revisionedAsset = $manifestRevisioned;
                            break;
                        }
                    }
                }

                return $revisionedAsset;
            } catch (PageException $e) {
                Log::log($e->getMessage(), $e->getSeverity(), $e->getContext());
                // In case of an exception, Twig must return the original asset URI!
                return $originalAsset;
            }
        });

        $twig->addFunction($revision);

        // Displaying the actual page.
        if (is_file(realpath(sprintf('%s/pages/%s.twig', $templatesPath, $uri)))) {
            echo $twig->render(sprintf('pages/%s.twig', $uri), ['environment' => Config::getEnvironment()]);
        } else {
            $this->show404();
        }
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
