<?php

/**
 * This file is part of the SgDatatablesBundle package.
 *
 * (c) stwe <https://github.com/stwe/DatatablesBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sg\DatatablesBundle\Twig;

use Closure;
use Sg\DatatablesBundle\Datatable\Action\Action;
use Sg\DatatablesBundle\Datatable\Column\ColumnInterface;
use Sg\DatatablesBundle\Datatable\DatatableInterface;
use Sg\DatatablesBundle\Datatable\Filter\FilterInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class DatatableTwigExtension
 *
 * @package Sg\DatatablesBundle\Twig
 */
class DatatableTwigExtension extends AbstractExtension
{
    /**
     * The PropertyAccessor.
     *
     * @var PropertyAccessor
     */
    protected $accessor;

    //-------------------------------------------------
    // Ctor.
    //-------------------------------------------------

    /**
     * DatatableTwigExtension constructor.
     */
    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    //-------------------------------------------------
    // Twig\Extension\ExtensionInterface
    //-------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sg_datatables_twig_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction(
                'sg_datatables_render',
                array($this, 'datatablesRender'),
                array('is_safe' => array('html'), 'needs_environment' => true)
            ),
            new TwigFunction(
                'sg_datatables_render_html',
                array($this, 'datatablesRenderHtml'),
                array('is_safe' => array('html'), 'needs_environment' => true)
            ),
            new TwigFunction(
                'sg_datatables_render_js_values',
                array($this, 'datatablesRenderJsValues'),
                array('is_safe' => array('html'), 'needs_environment' => true)
            ),
            new TwigFunction(
                'sg_datatables_render_filter',
                array($this, 'datatablesRenderFilter'),
                array('is_safe' => array('html'), 'needs_environment' => true)
            ),
            new TwigFunction(
                'sg_datatables_render_multiselect_actions',
                array($this, 'datatablesRenderMultiselectActions'),
                array('is_safe' => array('html'), 'needs_environment' => true)
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('sg_datatables_bool_var', array($this, 'boolVar')),
        );
    }

    //-------------------------------------------------
    // Functions
    //-------------------------------------------------

    /**
     * Renders the template.
     *
     * @param Environment        $twig
     * @param DatatableInterface $datatable
     *
     * @return string
     */
    public function datatablesRender(Environment $twig, DatatableInterface $datatable)
    {
        return $twig->render(
            '@SgDatatables/datatable/datatable.html.twig',
            array(
                'sg_datatables_view' => $datatable,
            )
        );
    }

    /**
     * Renders the html template.
     *
     * @param Environment        $twig
     * @param DatatableInterface $datatable
     *
     * @return string
     */
    public function datatablesRenderHtml(Environment $twig, DatatableInterface $datatable)
    {
        return $twig->render(
            '@SgDatatables/datatable/datatable_html.html.twig',
            array(
                'sg_datatables_view' => $datatable,
            )
        );
    }

    /**
     * Renders values used by datatable's JavaScript.
     *
     * @param Environment        $twig
     * @param DatatableInterface $datatable
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function datatablesRenderJsValues(Environment $twig, DatatableInterface $datatable): string
    {
        return $twig->render(
            '@SgDatatables/datatable/datatable_js_values.html.twig',
            array(
                'sg_datatables_view' => $datatable,
            )
        );
    }

    /**
     * Renders a Filter template.
     *
     * @param Environment        $twig
     * @param DatatableInterface $datatable
     * @param ColumnInterface    $column
     * @param string             $position
     *
     * @return string
     */
    public function datatablesRenderFilter(
        Environment $twig,
        DatatableInterface $datatable,
        ColumnInterface $column,
        $position
    ) {
        /** @var FilterInterface $filter */
        $filter = $this->accessor->getValue($column, 'filter');
        $index = $this->accessor->getValue($column, 'index');
        $searchColumn = $this->accessor->getValue($filter, 'searchColumn');

        if (null !== $searchColumn) {
            $columns = $datatable->getColumnBuilder()->getColumnNames();
            $searchColumnIndex = $columns[$searchColumn];
        }
        else {
            $searchColumnIndex = $index;
        }

        return $twig->render(
            $filter->getTemplate(),
            array(
                'column' => $column,
                'search_column_index' => $searchColumnIndex,
                'datatable_name' => $datatable->getName(),
                'position' => $position,
            )
        );
    }

    /**
     * Renders the MultiselectColumn Actions.
     *
     * @param Environment     $twig
     * @param ColumnInterface $multiselectColumn
     * @param int             $pipeline
     *
     * @return string
     */
    public function datatablesRenderMultiselectActions(
        Environment $twig,
        ColumnInterface $multiselectColumn,
        $pipeline
    ) {
        $parameters = array();
        $values = array();
        $actions = $this->accessor->getValue($multiselectColumn, 'actions');
        $domId = $this->accessor->getValue($multiselectColumn, 'renderActionsToId');
        $datatableName = $this->accessor->getValue($multiselectColumn, 'datatableName');

        /** @var Action $action */
        foreach ($actions as $actionKey => $action) {
            $routeParameters = $action->getRouteParameters();
            if (is_array($routeParameters)) {
                foreach ($routeParameters as $key => $value) {
                    $parameters[$actionKey][$key] = $value;
                }
            }
            elseif ($routeParameters instanceof Closure) {
                $parameters[$actionKey] = call_user_func($routeParameters);
            }
            else {
                $parameters[$actionKey] = array();
            }

            if ($action->isButton()) {
                if (null !== $action->getButtonValue()) {
                    $values[$actionKey] = $action->getButtonValue();

                    if (is_bool($values[$actionKey])) {
                        $values[$actionKey] = (int) $values[$actionKey];
                    }

                    if (true === $action->isButtonValuePrefix()) {
                        $values[$actionKey] = 'sg-datatables-'.$datatableName.'-multiselect-button-'.$actionKey.'-'.$values[$actionKey];
                    }
                }
                else {
                    $values[$actionKey] = null;
                }
            }
        }

        return $twig->render(
            '@SgDatatables/datatable/multiselect_actions.html.twig',
            array(
                'actions' => $actions,
                'route_parameters' => $parameters,
                'values' => $values,
                'datatable_name' => $datatableName,
                'dom_id' => $domId,
                'pipeline' => $pipeline,
            )
        );
    }

    //-------------------------------------------------
    // Filters
    //-------------------------------------------------

    /**
     * Renders: {{ var ? 'true' : 'false' }}
     *
     * @param mixed $value
     *
     * @return string
     */
    public function boolVar($value)
    {
        if ($value) {
            return 'true';
        }
        else {
            return 'false';
        }
    }
}
