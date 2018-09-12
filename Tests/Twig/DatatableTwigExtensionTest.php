<?php

namespace Sg\DatatablesBundle\Tests\Twig;

use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Tests\Datatables\PostDatatable;
use Sg\DatatablesBundle\Twig\DatatableTwigExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @var RequestStack
     */
    private $requestStack;

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
        $this->requestStack = $container->get('request_stack');
        $this->twig = $container->get('twig');
    }

    public function testDatatablesRenderJsValues()
    {
        $this->requestStack->push(Request::createFromGlobals());
        $this->postDatatable->setLocale('en');
        $this->postDatatable->buildDatatable();
        /* @noinspection PhpUnhandledExceptionInspection */
        $renderedContent = $this->datatableTwigExtension->datatablesRenderJsValues($this->twig, $this->postDatatable);
        $this->assertContains('{"searchCols":[null,null,null]}', $renderedContent);
        $this->assertContains(
            '{"serverSide":true,"pipeline":0,"ajax":{"url":"\/en\/post\/results","type":"GET","pages":0}}',
            $renderedContent
        );
        $this->assertContains(
            '{"columns":[{"title":"Id","searchable":true,"visible":true,"orderable":true,"data":"id"},{"title":"Title","searchable":true,"visible":true,"orderable":true,"data":"title"},{"title":"datatables.actions.title","searchable":false,"visible":true,"orderable":false,"data":2}]}',
            $renderedContent
        );

        $this->postDatatable->getAjax()->setPipeline(1);
        $this->postDatatable->getOptions()->setOrder([[0, 'asc']]);
        $this->postDatatable->getOptions()->setDom(
            "<'row'<'col-sm-6'l><'col-sm-6'f>>".
            "<'row'<'col-sm-12'tr>>".
            "<'row'<'col-sm-5'i><'col-sm-7'p>>"
        );
        $this->postDatatable->getOptions()->setLengthMenu([10, 25, 50, 100]);
        /* @noinspection PhpUnhandledExceptionInspection */
        $modifiedRenderedContent = $this->datatableTwigExtension->datatablesRenderJsValues(
            $this->twig,
            $this->postDatatable
        );
        $this->assertContains(
            '{"serverSide":true,"pipeline":1,"ajax":{"url":"\/en\/post\/results","type":"GET","pages":1}}',
            $modifiedRenderedContent
        );
        $this->assertContains(
            '{"dom":"\u003C\u0027row\u0027\u003C\u0027col-sm-6\u0027l\u003E\u003C\u0027col-sm-6\u0027f\u003E\u003E\u003C\u0027row\u0027\u003C\u0027col-sm-12\u0027tr\u003E\u003E\u003C\u0027row\u0027\u003C\u0027col-sm-5\u0027i\u003E\u003C\u0027col-sm-7\u0027p\u003E\u003E","lengthMenu":[10,25,50,100],"order":[[0,"asc"]],"individualFiltering":true}',
            $modifiedRenderedContent
        );
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->datatableTwigExtension = null;
        $this->postDatatable = null;
        $this->requestStack = null;
        $this->twig = null;
    }

}