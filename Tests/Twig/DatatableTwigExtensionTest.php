<?php

namespace Sg\DatatablesBundle\Tests\Twig;

use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Tests\Datatables\PostDatatable;
use Sg\DatatablesBundle\Twig\DatatableTwigExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DatatableTwigExtensionTest extends KernelTestCase
{

    /**
     * @var DatatableTwigExtension
     */
    private $datatableTwigExtension;

    /**
     * @var AbstractDatatable
     */
    private $postDatatable;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp()
    {
        parent::setUp();
        $container = $this->bootKernel()->getContainer();
        $this->datatableTwigExtension = $container->get('test.sg_datatables.twig.extension');
        $this->postDatatable = $container->get(PostDatatable::class);
    $this->twig = $container->get('twig');
    }

    public function testDatatablesRenderJsValues()
    {
        $this->postDatatable->setLocale('en');
        $this->postDatatable->buildDatatable();
        /* @noinspection PhpUnhandledExceptionInspection */
        $renderedContent = $this->datatableTwigExtension->datatablesRenderJsValues($this->twig, $this->postDatatable);
        $this->assertContains('id', $renderedContent);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->datatableTwigExtension = null;
        $this->postDatatable = null;
        $this->twig = null;
    }

}