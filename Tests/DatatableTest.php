<?php

/**
 * This file is part of the SgDatatablesBundle package.
 *
 * (c) stwe <https://github.com/stwe/DatatablesBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sg\DatatablesBundle\Tests;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Sg\DatatablesBundle\Tests\Datatables\PostDatatable;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class DatatableTest
 *
 * @package Sg\DatatablesBundle\Tests
 */
class DatatableTest extends TestCase
{

    /**
     * testCreate.
     */
    public function testCreate()
    {
        $tableClass = PostDatatable::class;

        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $securityToken = $this->createMock(TokenStorageInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $router = $this->createMock(RouterInterface::class);
        $twig = $this->createMock(Environment::class);

        $em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                array('getClassMetadata')
            )
            ->getMock();

        $em->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($this->getClassMetadataMock()));

        /** @var \Sg\DatatablesBundle\Tests\Datatables\PostDatatable $table */
        $table = new $tableClass($authorizationChecker, $securityToken, $translator, $router, $em, $twig);

        $this->assertEquals('post_datatable', $table->getName());

        $table->buildDatatable();
    }

    /**
     * Get classMetadataMock.
     *
     * @return mixed
     */
    public function getClassMetadataMock()
    {
        $mock = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->addMethods(array('getEntityShortName'))
            ->getMock();

        $mock->expects($this->any())
            ->method('getEntityShortName')
            ->will($this->returnValue('{entityShortName}'));

        return $mock;
    }

}
