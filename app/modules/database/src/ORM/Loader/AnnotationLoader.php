<?php

namespace Pagekit\Database\ORM\Loader;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Pagekit\Database\ORM\Annotation\Annotation;
use Pagekit\Database\ORM\Annotation\BelongsTo;
use Pagekit\Database\ORM\Annotation\Column;
use Pagekit\Database\ORM\Annotation\Entity;
use Pagekit\Database\ORM\Annotation\HasOne;
use Pagekit\Database\ORM\Annotation\OrderBy;
use Pagekit\Database\ORM\Relation\HasMany;
use Pagekit\Database\ORM\Relation\ManyToMany;

class AnnotationLoader implements LoaderInterface
{
    /**
     * @var SimpleAnnotationReader
     */
    protected $reader;

    /**
     * @var string
     */
    protected $namespace = 'Pagekit\Database\ORM\Annotation';

    /**
     * {@inheritdoc}
     */
    public function load(\ReflectionClass $class, array $config = [])
    {
        /* @var $annotation Entity */
        if ($annotation = $this->getAnnotation($class, 'Entity')) {

            $config['table']           = $annotation->tableClass ?: strtolower($class->getShortName());
            $config['eventPrefix']     = $annotation->eventPrefix;

        // @MappedSuperclass
        } elseif ($annotation = $this->getAnnotation($class, 'MappedSuperclass')) {

            $config['isMappedSuperclass'] = true;

        } else {
            throw new \Exception(sprintf('No @Entity annotation found for class %s', $class->getName()));
        }

        foreach ($class->getProperties() as $property) {

            $name = $property->getName();

            if (!$property->isPrivate() && isset($config['isMappedSuperclass']) || isset($config['fields'][$name]['inherited']) || isset($config['relations'][$name]['inherited'])) {
                continue;
            }

            /* @var $annotation Column */
            if ($annotation = $this->getAnnotation($property, 'Column')) {

                $field = compact('name');

                if (isset($config['fields'][$name])) {
                    throw new \Exception(sprintf('Duplicate field mapping detected, "%s" already exists.', $name));
                }

                if ($annotation->type) {
                    $field['type'] = $annotation->type;
                }

                if ($annotation->name) {
                    $field['column'] = $annotation->name;
                }

                if ($this->getAnnotation($property, 'Id')) {
                    $field['id'] = true;
                }

                $config['fields'][$name] = $field;

            } else {

                /* @var $annotation BelongsTo|HasMany|HasOne|ManyToMany */
                foreach (['BelongsTo', 'HasOne', 'HasMany', 'ManyToMany'] as $type) {
                    if ($annotation = $this->getAnnotation($property, $type)) {

                        if (isset($config['fields'][$name]) || isset($config['relations'][$name])) {
                            throw new \Exception(sprintf('Duplicate relation mapping detected, "%s" already exists.', $name));
                        }

                        /* @var $order OrderBy */
                        if (property_exists($annotation, 'orderBy') && $order = $this->getAnnotation($property, 'OrderBy')) {
                            $annotation->orderBy = $order->value;
                        }

                        $config['relations'][$name] = array_merge(compact('name', 'type'), (array) $annotation);

                        break;
                    }
                }
            }
        }

        foreach ($class->getMethods() as $method) {

            $name = $method->getName();

            if (!$method->isPublic() || $method->getDeclaringClass()->getName() != $class->getName()) {
                continue;
            }

            // @Saving, @Saved, @Updating, @Updated, @Deleting, @Deleted, @Created, @Creating, @Init
            foreach (['Saving', 'Saved', 'Updating', 'Updated', 'Deleting', 'Deleted', 'Created', 'Creating', 'Init'] as $event) {
                if ($annotation = $this->getAnnotation($method, $event)) {
                    $config['events'][lcfirst($event)][] = $name;
                }
            }
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function isTransient(\ReflectionClass $class)
    {
        return !$this->getAnnotation($class, 'Entity') && !$this->getAnnotation($class, 'MappedSuperclass');
    }

    /**
     * Gets an annotation.
     *
     * @param  mixed  $from
     * @param  string $name
     * @return Annotation
     */
    protected function getAnnotation($from, $name)
    {
        if (!$this->reader) {
            $this->reader = new SimpleAnnotationReader;
            $this->reader->addNamespace($this->namespace);
        }

        $name = "{$this->namespace}\\$name";

        if ($from instanceof \ReflectionClass) {
            $annotation = $this->reader->getClassAnnotation($from, $name);
        } elseif ($from instanceof \ReflectionMethod) {
            $annotation = $this->reader->getMethodAnnotation($from, $name);
        } elseif ($from instanceof \ReflectionProperty) {
            $annotation = $this->reader->getPropertyAnnotation($from, $name);
        }

        return $annotation;
    }
}
