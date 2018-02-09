<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 *
 * @ORM\Table(name="page", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_140AB6205E237E06727ACA70", columns={"name", "parent_id"})}, indexes={@ORM\Index(name="IDX_140AB620727ACA70", columns={"parent_id"})})
 * @ORM\Entity
 */
class Page
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="header", type="string", length=255, nullable=false)
     */
    protected $header;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    protected $content;

    /**
     * @var \AppBundle\Entity\Page
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Page")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    protected $parent;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return Page|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Page|null $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return string
     */
//    public function __toString()
//    {
//        return (string)$this->getId();
//    }
}

