<?php

namespace Sg\DatatablesBundle\Tests\Twig;

use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Tests\Datatables\PostDatatable;
use Sg\DatatablesBundle\Twig\DatatableTwigExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

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
        static::$container->get('request_stack')->push(Request::createFromGlobals());
        $this->postDatatable->setLocale('en');
        $this->postDatatable->buildDatatable();
        /* @noinspection PhpUnhandledExceptionInspection */
        $renderedContent = $this->datatableTwigExtension->datatablesRenderJsValues($this->twig, $this->postDatatable);
        $this->assertContains('{"searchCols":[null,null,null]}', $renderedContent);
        $this->assertContains('{"serverSide":true,"ajax":{"url":"\/en\/post\/results","type":"GET"}}', $renderedContent);
        $this->assertContains('[{"title":"Id","searchable":true,"visible":true,"orderable":true,"data":"id"},{"title":"Title","searchable":true,"visible":true,"orderable":true,"data":"title"},{"title":"datatables.actions.title","searchable":false,"visible":true,"orderable":false,"data":2}]', $renderedContent);

        $this->postDatatable->getAjax()->setPipeline(1);
        /* @noinspection PhpUnhandledExceptionInspection */
        $modifiedRenderedContent = $this->datatableTwigExtension->datatablesRenderJsValues($this->twig, $this->postDatatable);
        $this->assertContains('{"serverSide":true,"ajax":"$.fn.dataTable.pipeline({\u0022url\u0022:\u0022\/en\/post\/results\u0022,\u0022type\u0022:\u0022GET\u0022,\u0022pages\u0022:1})"}', $modifiedRenderedContent);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->datatableTwigExtension = null;
        $this->postDatatable = null;
        $this->twig = null;
    }

}