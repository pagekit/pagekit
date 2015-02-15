<?php

namespace Pagekit\Package;

use Pagekit\Package\Exception\LogicException;
use Pagekit\Package\Repository\RepositoryInterface;

class Package implements PackageInterface
{
    protected $name;
    protected $version;
    protected $type;
    protected $title;
    protected $description;
    protected $keywords;
    protected $homepage;
    protected $license = [];
    protected $authors;
    protected $extra = [];
    protected $releaseDate;
    protected $installationSource;
    protected $sourceType;
    protected $sourceUrl;
    protected $distType;
    protected $distUrl;
    protected $distSha1Checksum;
    protected $repository;
    protected $autoload = [];
    protected $resources = [];

    /**
     * Creates a new package.
     *
     * @param  string  $name
     * @param  string  $version
     */
    public function __construct($name, $version)
    {
        $this->name = strtolower($name);
        $this->title = $this->name;
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywords()
    {
        return (array) $this->keywords;
    }

    /**
     * Set the keywords
     *
     * @param array $keywords
     */
    public function setKeywords(array $keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * {@inheritdoc}
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Set the homepage
     *
     * @param string $homepage
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    }

    /**
     * {@inheritdoc}
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Set the license
     *
     * @param array $license
     */
    public function setLicense(array $license)
    {
        $this->license = $license;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthor()
    {
        if ($this->authors) {
            return current($this->authors);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Set the authors
     *
     * @param array $authors
     */
    public function setAuthors(array $authors)
    {
        $this->authors = $authors;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     */
    public function setExtra(array $extra)
    {
        $this->extra = $extra;
    }

    /**
     * {@inheritdoc}
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    /**
     * Set the releaseDate
     *
     * @param \DateTime $releaseDate
     */
    public function setReleaseDate(\DateTime $releaseDate)
    {
        $this->releaseDate = $releaseDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstallationSource()
    {
        return $this->installationSource;
    }

    /**
     * {@inheritdoc}
     */
    public function setInstallationSource($type)
    {
        $this->installationSource = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceType()
    {
        return $this->sourceType;
    }

    /**
     * @param string $type
     */
    public function setSourceType($type)
    {
        $this->sourceType = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceUrl()
    {
        return $this->sourceUrl;
    }

    /**
     * @param string $url
     */
    public function setSourceUrl($url)
    {
        $this->sourceUrl = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getDistType()
    {
        return $this->distType;
    }

    /**
     * @param string $type
     */
    public function setDistType($type)
    {
        $this->distType = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getDistUrl()
    {
        return $this->distUrl;
    }

    /**
     * @param string $url
     */
    public function setDistUrl($url)
    {
        $this->distUrl = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getDistSha1Checksum()
    {
        return $this->distSha1Checksum;
    }

    /**
     * @param string $sha1checksum
     */
    public function setDistSha1Checksum($sha1checksum)
    {
        $this->distSha1Checksum = $sha1checksum;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * {@inheritdoc}
     */
    public function setRepository(RepositoryInterface $repository)
    {
        if ($this->repository) {
            throw new LogicException('A package can only be added to one repository');
        }

        $this->repository = $repository;
    }

    /**
     * Returns the autoload namespace => directory mapping
     */
    public function getAutoload()
    {
        return $this->autoload;
    }

    /**
     * Set the autoload namespace => directory mapping
     *
     * @param array $autoload
     */
    public function setAutoload($autoload)
    {
        $this->autoload = $autoload;
    }

    /**
     * Returns the resources scheme => path(s)
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Set the resources scheme => path(s)
     *
     * @param array $resources
     */
    public function setResources(array $resources = [])
    {
        $this->resources = $resources;
    }

    /**
     * {@inheritdoc}
     */
    public function getUniqueName()
    {
        return sprintf('%s-%s', $this->getName(), $this->getVersion());
    }

    /**
     * {@inheritdoc}
     */
    public function compare(PackageInterface $package, $operator = '==')
    {
        return strtolower($this->getName()) === strtolower($package->getName()) &&
            version_compare(strtolower($this->getVersion()), strtolower($package->getVersion()), $operator);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getUniqueName();
    }
}
