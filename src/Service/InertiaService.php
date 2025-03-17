<?php

declare(strict_types=1);

namespace Nytodev\InertiaSymfony\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class InertiaService implements InertiaServiceInterface
{
    protected string $rootView = 'base.html.twig';

    /**
     * @var array<string, mixed>
     */
    protected array $sharedProps = [];

    public function __construct(
        private readonly ContainerInterface $container,
        protected Environment $twig,
        protected RequestStack $requestStack,
        private readonly string $projectDir,
    ) {
        if ($this->container->hasParameter('inertia_symfony.root_view')) {
            $rootView = $this->container->getParameter('inertia_symfony.root_view');

            if (\is_string($rootView)) {
                $this->setRootView($rootView);
            }
        }
    }

    private function setRootView(string $name): void
    {
        $this->rootView = $name;
    }

    /**
     * @param array<string, mixed> $props
     */
    public function render(string $component, array $props = []): Response
    {
        $request = $this->requestStack->getCurrentRequest();
        $url = $request?->getRequestUri();

        $page = [
            'component' => $component,
            'props' => array_merge(
                $props,
                $this->sharedProps
            ),
            'url' => $url,
            'version' => $this->getVersion(),
        ];

        return new Response(
            $this->twig->render($this->rootView, ['page' => $page]),
            Response::HTTP_OK
        );
    }

    public function getVersion(): ?string
    {
        if ($this->container->hasParameter('app.asset_url')) {
            $assetUrl = $this->container->getParameter('app.asset_url');
            if (\is_string($assetUrl) && filter_var($assetUrl, \FILTER_VALIDATE_URL)) {
                return md5($assetUrl);
            }
        }

        if (file_exists($manifest = $this->projectDir.'/public/mix-manifest.json')) {
            return md5_file($manifest) !== false ? md5_file($manifest) : null;
        }

        if (file_exists($manifest = $this->projectDir.'/public/build/manifest.json')) {
            return md5_file($manifest) !== false ? md5_file($manifest) : null;
        }

        return null;
    }
}
