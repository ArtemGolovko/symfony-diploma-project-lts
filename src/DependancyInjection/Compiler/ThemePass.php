<?php

namespace App\DependancyInjection\Compiler;

use App\Service\ArticleContentGenerator\Theme\ThemeChain;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ThemePass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(ThemeChain::class)) {
            return;
        }

        $definition = $container->findDefinition(ThemeChain::class);

        $taggedServices = $container->findTaggedServiceIds('article_content_generator.theme_provider');

        foreach ($taggedServices as $id => $tags) {
            // add the transport service to the TransportChain service
            $definition->addMethodCall('addProvider', [new Reference($id)]);
        }
    }
}