<?php

namespace Sg\DatatablesBundle\Datatable\Column;

use Sg\DatatablesBundle\Datatable\Editable\EditableInterface;

interface IsEditableInterface
{

    /**
     * Get editable.
     *
     * @return null|EditableInterface
     */
    public function getEditable(): ?EditableInterface;

    /**
     * Set editable.
     *
     * @param null|array $editableClassAndOptions
     *
     * @return $this
     * @throws \Exception
     */
    public function setEditable(?array $editableClassAndOptions);

}