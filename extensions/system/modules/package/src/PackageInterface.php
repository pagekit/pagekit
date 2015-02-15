<?php

namespace Pagekit\Package;

use Pagekit\Package\Repository\RepositoryInterface;

interface PackageInterface
{
    /**
     * @return array
     */
    public function getAuthor();

    /**
     * @return string[]
     */
    public function getAuthors();

    /**
     * Returns the package's name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the version of this package.
     *
     * @return string
     */
    public function getVersion();

    /**
     * Returns the package type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the package extra data.
     *
     * @return array
     */
    public function getExtra();

    /**
     * Returns the release date of the package.
     *
     * @return \DateTime
     */
    public function getReleaseDate();

    /**
     * Sets source from which this package was installed (source/dist).
     *
     * @param string $type
     */
    public function setInstallationSource($type);

    /**
     * Returns source from which this package was installed (source/dist).
     *
     * @return string
     */
    public function getInstallationSource();

    /**
     * Returns the repository type of this package, e.g. git, svn
     *
     * @return string
     */
    public function getSourceType();

    /**
     * Returns the repository url of this package.
     *
     * @return string
     */
    public function getSourceUrl();

    /**
     * Returns the type of the distribution archive of this version.
     *
     * @return string
     */
    public function getDistType();

    /**
     * Returns the url of the distribution archive of this version.
     *
     * @return string
     */
    public function getDistUrl();

    /**
     * Returns the sha1 checksum for the distribution archive of this version.
     *
     * @return string
     */
    public function getDistSha1Checksum();

    /**
     * Returns a reference to the repository that owns the package.
     *
     * @return RepositoryInterface
     */
    public function getRepository();

    /**
     * Stores a reference to the repository that owns the package.
     *
     * @param RepositoryInterface $repository
     */
    public function setRepository(RepositoryInterface $repository);

    /**
     * Returns package unique name, constructed from name and version.
     *
     * @return string
     */
    public function getUniqueName();

    /**
     * Compares package's version number to given package.
     *
     * @param PackageInterface $package
     * @param string           $operator
     * @return bool
     */
    public function compare(PackageInterface $package, $operator = '==');

    /**
     * Converts the package into a readable and unique string.
     *
     * @return string
     */
    public function __toString();
}
