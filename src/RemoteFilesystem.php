<?php

declare(strict_types=1);

namespace ISAAC\Velocita\Composer;

use Composer\Config as ComposerConfig;
use Composer\IO\IOInterface;
use Composer\Util\RemoteFilesystem as ComposerFilesystem;

class RemoteFilesystem extends ComposerFilesystem
{
    /**
     * @var UrlMapper
     */
    protected $urlMapper;
    /**
     * @var IOInterface
     */
    protected $io;

    public function __construct(
        UrlMapper $urlMapper,
        IOInterface $io,
        ComposerConfig $config = null,
        array $options = [],
        bool $disableTls = false
    ) {
        parent::__construct($io, $config, $options, $disableTls);

        $this->urlMapper = $urlMapper;
        $this->io = $io;
    }

    protected function patchURL(string $url): string
    {
        $patchedUrl = $this->urlMapper->applyMappings($url);

        if ($patchedUrl !== $url) {
            $this->io->write(\sprintf('%s(url=%s): %s', __METHOD__, $url, $patchedUrl), true, IOInterface::DEBUG);
        }

        return $patchedUrl;
    }

    /**
     * @inheritdoc
     */
    protected function get($originUrl, $fileUrl, $additionalOptions = [], $fileName = null, $progress = true)
    {
        $patchedUrl = $this->patchURL($fileUrl);
        return parent::get($originUrl, $patchedUrl, $additionalOptions, $fileName, $progress);
    }
}
