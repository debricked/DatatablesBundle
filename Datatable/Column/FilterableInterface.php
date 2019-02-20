<?php

namespace Sg\DatatablesBundle\Datatable\Column;

use Sg\DatatablesBundle\Datatable\Filter\FilterInterface;

interface FilterableInterface
{

    /**
     * Get Filter instance.
     *
     * @return FilterInterface
     */
    public function getFilter();

    /**
     * Set Filter instance.
     *
     * @param array $filterClassAndOptions
     *
     * @return $this
     * @throws \Exception
     */
    public function setFilter(array $filterClassAndOptions);

}