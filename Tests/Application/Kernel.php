<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 2018-06-20
 * Time: 16:39
 */

namespace Sg\DatatablesBundle\Tests\Application;


use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Sg\DatatablesBundle\Command\GenerateDatatableJavascriptsCommand;
use Sg\DatatablesBundle\SgDatatablesBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class Kernel extends \Symfony\Component\HttpKernel\Kernel implements CompilerPassInterface
{

    /**
     * Returns an array of bundles to register.
     *
     * @return iterable|BundleInterface[] An iterable of bundle instances
     */
    public function registerBundles()
    {
        return
            [
                new FrameworkBundle(),
                new SecurityBundle(),
                new TwigBundle(),
                new DoctrineBundle(),
                new SgDatatablesBundle(),
            ];
    }

    /**
     * Loads the container configuration.
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        /* @noinspection PhpUnhandledExceptionInspection */
        $loader->load($this->getProjectDir().'/Tests/Application/config/config.yaml');
    }

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition(GenerateDatatableJavascriptsCommand::class)->setPublic(true);
    }
}