<?php

namespace App\Content;

use App\Core\Config;
use App\Core\Log;
use Twig\TwigFunction;

/**
 * Twig extension for dealing with static assets.
 *
 * @author Miša Brežanac <brezanac@gmail.com>
 */
class TwigAssets extends \Twig\Extension\AbstractExtension
{

    public function getFunctions()
    {
        return [
            new TwigFunction('assets', [$this, 'assets']),
            new TwigFunction('response', [$this, 'response']),
            new TwigFunction('srcset', [$this, 'srcset'])
        ];
    }

    /**
     * Revisions static assets according to the supplied manifest file.
     *
     * If revisioning is enabled, all occurrences of the original assets will
     * be replaced by the revisioned version. If no matching pair is found in the
     * manifest file the original asset is returned.
     *
     * Example: assets/images/img.jpg -> assets/images/img-5d4a328f53.jpg
     *
     * @param string $originalAsset Path to the original asset.
     * @return string Revisioned asset path or the original one.
     */
    public function assets(string $originalAsset)
    {
        $settings = Config::get('app');

        /*
         * In order to facilitate a graceful way to ignore a missing manifest for static assets
         * when revisioning is enabled but no manifest data is actually available, we just skip
         * revisioning and return the original asset.
         *
         * This helps in cases when development is done without revisioned assets even though the actual
         * manifest file has not been generated yet (`gulp production` hasn't been run yet).
         */
        if (!$settings['twig']['revisionAssets'] || is_null(Page::getManifest())) {
            return $originalAsset;
        }

        $revisionedAsset = $originalAsset;

        if ($settings['twig']['revisionAssets']) {
            foreach (Page::getManifest() as $manifestOriginal => $manifestRevisioned) {
                if ($manifestOriginal === $originalAsset) {
                    $revisionedAsset = $manifestRevisioned;
                    break;
                }
            }
        }

        return $revisionedAsset;
    }

    /**
     * Generates srcset attribute values from data provided in Twig templates.
     *
     * The general idea behind this function is to make generating srcset values explicit.
     * The function will accept an array of array items, each containing at least two elements,
     * the first one specifying the asset path and the second one the "w" unit.
     *
     * Example: [['assets/images/img-600.jpg', '600w'], ['assets/images/img-1200.jpg', '1200w']]
     *
     * @param array $srcset The srcset data from the template.
     * @return string Generated value for the srcset attribute.
     */
    public function srcset(array $srcset)
    {
        $output = [];
        foreach ($srcset as $set) {
            // Both parameters need to exist or the set isn't added to the output...
            if (!(isset($set[0]) && isset($set[1]))) {
                // ... but we also make sure to log that detail if it happens.
                Log::info('[TwigAssets] Incomplete srcset set', ['srcset' => $set]);
                continue;
            }
            $output[] = sprintf('%s %s', $set[0], $set[1]);
        }

        return implode(', ', $output);
    }

    /**
     * Sends a specific HTTP status code back with the response.
     *
     * Useful for triggering on-the-fly HTTP responses like 404, 302 etc.
     * directly from within templates.
     *
     * @param int $responseCode The HTTP response code to return.
     */
    public function response(int $responseCode)
    {
        http_response_code($responseCode);
    }
}
