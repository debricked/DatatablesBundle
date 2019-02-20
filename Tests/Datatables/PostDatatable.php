<?php

/**
 * This file is part of the SgDatatablesBundle package.
 *
 * (c) stwe <https://github.com/stwe/DatatablesBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sg\DatatablesBundle\Tests\Datatables;

use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Tests\Entity\Post;

/**
 * Class PostDatatable
 *
 * @package Sg\DatatablesBundle\Tests\Datatables
 */
class PostDatatable extends AbstractDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
        $this->ajax->set(
            array(
                'url' => $this->router->generate('app.datatable.post'),
                'type' => 'GET',
            )
        );

        $this->options->set(
            array(
                'individual_filtering' => true,
            )
        );

        $this->columnBuilder
            ->add(
                'id',
                Column::class,
                array(
                    'title' => 'Id',
                )
            )
            ->add(
                'title',
                Column::class,
                array(
                    'title' => 'Title',
                )
            )
            ->add(
                null,
                ActionColumn::class,
                [
                    'title' => $this->translator->trans('datatables.actions.title'),
                    'actions' => [
                        [
                            'icon' => 'fa fa-trash-o',
                            'render_if' => function () {
                                return $this->authorizationChecker->isGranted(Role::DEFAULT_ROLE_COMPANY_ADMIN);
                            },
                            'label' => 'Delete',
                            'route' => 'app.datatable.post',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => $this->translator->trans('datatables.actions.delete'),
                                'class_name' => 'btn btn-danger btn-xs delete',
                                'role' => 'button',
                            ],
                        ],
                    ],
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return Post::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'post_datatable';
    }
}
