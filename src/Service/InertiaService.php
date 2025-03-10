<?php
declare(strict_types=1);

namespace Nytodev\InertiaSymfony\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class InertiaService implements InertiaServiceInterface
{
    protected ?string $rootView = null;

    protected array $sharedProps = [];

    public function __construct(
        private readonly ContainerInterface $container,
        protected Environment $twig,
        protected RequestStack $requestStack,
        private readonly string $projectDir

    ) {
        if ($this->container->hasParameter('inertia_symfony.root_view')) {
            $this->setRootView(
                $this->container->getParameter('inertia_symfony.root_view')
            );
        }
    }

    private function setRootView(string $name): void
    {
        $this->rootView = $name;
    }

    public function render(string $component, array $props = []): Response
    {
        $url = $this->requestStack->getCurrentRequest()->getRequestUri();
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
            return md5($this->container->getParameter('app.asset_url'));
        }

        if (file_exists($manifest = $this->projectDir . '/public/mix-manifest.json')) {
            return md5_file($manifest);
        }

        if (file_exists($manifest = $this->projectDir . '/public/build/manifest.json')) {
            return md5_file($manifest);
        }

        return null;
    }
}
