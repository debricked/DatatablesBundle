<?php

/**
 * This file is part of the SgDatatablesBundle package.
 *
 * (c) stwe <https://github.com/stwe/DatatablesBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sg\DatatablesBundle\Controller;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class DatatableController
 *
 * @package Sg\DatatablesBundle\Controller
 */
class DatatableController extends AbstractController
{
    //-------------------------------------------------
    // Actions
    //-------------------------------------------------

    /**
     * Edit field.
     *
     * @param Request $request
     *
     * @Route("/datatables/edit/field", methods={"POST"}, name="sg_datatables_edit")
     *
     * @return Response
     * @throws Exception
     */
    public function editAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            // x-editable sends some default parameters
            $pk = $request->request->get('pk');       // entity primary key
            $field = $request->request->get('name');  // e.g. comments.createdBy.username
            $value = $request->request->get('value'); // the new value

            // additional params
            $entityClassName = $request->request->get('entityClassName'); // e.g. AppBundle\Entity\Post
            $token = $request->request->get('token');
            $originalTypeOfField = $request->request->get('originalTypeOfField');
            $path = $request->request->get('path'); // for toMany - the current element

            // check token
            if (!$this->isCsrfTokenValid('sg-datatables-editable', $token)) {
                throw new AccessDeniedException('DatatableController::editAction(): The CSRF token is invalid.');
            }

            // get an object by its primary key
            $entity = $this->getEntityByPk($entityClassName, $pk);

            /** @var PropertyAccessor $accessor */
            $accessor = PropertyAccess::createPropertyAccessorBuilder()
                ->enableMagicCall()
                ->getPropertyAccessor();

            // normalize the new value
            $value = $this->normalizeValue($originalTypeOfField, $value);

            // set new value
            null !== $path ? $accessor->setValue($entity, $path, $value) : $accessor->setValue($entity, $field, $value);

            // save all
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return new Response('Success', 200);
        }

        return new Response('Bad request', 400);
    }

    //-------------------------------------------------
    // Helper
    //-------------------------------------------------

    /**
     * Finds an object by its primary key / identifier.
     *
     * @param string $entityClassName
     * @param mixed  $pk
     *
     * @return object
     */
    private function getEntityByPk($entityClassName, $pk)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($entityClassName)->find($pk);
        if (!$entity) {
            throw $this->createNotFoundException('DatatableController::getEntityByPk(): The entity does not exist.');
        }

        return $entity;
    }

    /**
     * Normalize value.
     *
     * @param string $originalTypeOfField
     * @param mixed  $value
     *
     * @return bool|DateTime|float|int|null|string
     * @throws Exception
     */
    private function normalizeValue($originalTypeOfField, $value)
    {
        switch ($originalTypeOfField) {
            case Types::DATETIMETZ_MUTABLE:
            case Types::DATETIME_MUTABLE:
                $value = new DateTime($value);
                break;
            case Types::BOOLEAN:
                $value = $this->strToBool($value);
                break;
            case Types::TEXT:
            case Types::STRING:
                break;
            case Types::SMALLINT:
            case Types::INTEGER:
                $value = (int) $value;
                break;
            case Types::BIGINT:
                $value = (string) $value;
                break;
            case Types::FLOAT:
            case Types::DECIMAL:
                $value = (float) $value;
                break;
            default:
                throw new Exception(
                    "DatatableController::prepareValue(): The field type {$originalTypeOfField} is not editable."
                );
        }

        return $value;
    }

    /**
     * String to boolean.
     *
     * @param string $str
     *
     * @return null|bool
     * @throws Exception
     */
    private function strToBool($str)
    {
        $str = strtolower($str);

        if ('null' === $str) {
            return null;
        }

        if ($str === 'true' || $str === '1') {
            return true;
        }
        elseif ($str === 'false' || $str === '0') {
            return false;
        }
        else {
            throw new Exception(
                'DatatableController::strToBool(): Cannot convert string to boolean, expected string "true" or "false".'
            );
        }
    }
}
