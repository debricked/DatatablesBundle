<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 2018-06-20
 * Time: 17:53
 */

namespace Sg\DatatablesBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Post
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

}
