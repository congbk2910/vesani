<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Plugin\Email\Model\Template;

use Magento\Email\Model\Template\Filter;
use Magento\Framework\App\CacheInterface;

class FilterPlugin
{
    public const CSS_CACHE_TAG = 'amasty_acart_email_css_cache';

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var array
     */
    private $inMemoryStorage = [];

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function aroundGetCssFilesContent(Filter $subject, \Closure $proceed, array $files)
    {
        $files = array_unique($files);

        if (empty($files)) {
            return $proceed($files);
        }

        $cssCacheId = $this->getCacheId($files, $subject->getDesignParams());

        if (!isset($this->inMemoryStorage[$cssCacheId])) {
            $cachedCssContent = $this->cache->load($cssCacheId);

            if ($cachedCssContent) {
                $this->inMemoryStorage[$cssCacheId] = $cachedCssContent;
            } else {
                $cssContent = $proceed($files);
                $this->cache->save($cssContent, $cssCacheId, [], 86400);
                $this->inMemoryStorage[$cssCacheId] = $cssContent;
            }
        }

        return $this->inMemoryStorage[$cssCacheId];
    }

    private function getCacheId(array $files, array $designConfig): string
    {
        $filesIdentifier = implode('_', $files);
        $designIdentifier = vsprintf(
            '%s_%s_%s',
            [
                $designConfig['area'] ?? '',
                $designConfig['theme'] ?? '',
                $designConfig['locale'] ?? '',
            ]
        );

        return implode('_', [self::CSS_CACHE_TAG, $filesIdentifier, $designIdentifier]);
    }
}
