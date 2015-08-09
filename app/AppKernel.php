<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class AppKernel
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = array(
            new Carcel\UserBundle\CarcelUserBundle(),
            new Carcel\DocumentsBundle\CarcelDocumentsBundle(),
            new Khatovar\Bundle\WebBundle\KhatovarWebBundle(),
            new Khatovar\Bundle\PhotoBundle\KhatovarPhotoBundle(),
            new Khatovar\Bundle\HomepageBundle\KhatovarHomepageBundle(),
            new Khatovar\Bundle\MemberBundle\KhatovarMemberBundle(),
            new Khatovar\Bundle\ExactionBundle\KhatovarExactionBundle(),
            new Khatovar\Bundle\ContactBundle\KhatovarContactBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new CoreSphere\ConsoleBundle\CoreSphereConsoleBundle();
        }

        $bundles = array_merge(
            $this->getSymfonyBundles(),
            $this->getAdditionalBundles(),
            $bundles
        );

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }

    /**
     * Get some additional, useful plugins.
     *
     * @return array
     */
    protected function getAdditionalBundles()
    {
        return array(
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new FOS\UserBundle\FOSUserBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        );
    }

    /**
     * Get Symfony bundles.
     *
     * @return array
     */
    protected function getSymfonyBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
        );
    }
}
