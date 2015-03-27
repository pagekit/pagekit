<?php

namespace Pagekit\Tree\Tests;

use Pagekit\Tree\Node;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    public function testSetGetParent()
    {
        $tree = new Node;
        $node = new Node;

        $tree->add($node);

        $this->assertEquals($tree, $node->getParent());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetParentOnItself()
    {
        $node = new Node;

        $node->setParent($node);
    }

    public function testGetDepth()
    {
        $tree = new Node;
        $node1 = new Node;
        $node2 = new Node;

        $tree->add($node1);
        $node1->add($node2);

        $this->assertEquals(1, $node1->getDepth());
        $this->assertEquals(2, $node2->getDepth());
    }

    public function testAdd()
    {
        $tree = new Node;
        $node = new Node;
        $hash = $node->hashCode();

        $tree->add($node);

        $this->assertTrue($tree->contains($node));
        $this->assertTrue($tree->contains($hash));
        $this->assertEquals($node, $tree->find($hash));
        $this->assertEquals(1, count($tree));
    }

    public function testAddAll()
    {
        $tree = new Node;

        $tree->addAll([new Node, New Node]);

        $this->assertEquals(2, count($tree));
    }

    public function testRemove()
    {
        $tree = new Node;
        $node = new Node;
        $hash = $node->hashCode();

        $tree->add($node);

        $this->assertTrue($tree->remove($node));
        $this->assertFalse($tree->contains($hash));
        $this->assertNull($tree->find($hash));
        $this->assertEquals(0, count($tree));
    }

    public function testRemoveAll()
    {
        $tree = new Node;
        $nodes = [new Node, new Node];

        $tree->addAll($nodes);
        $tree->removeAll($nodes);

        $this->assertEquals(0, count($tree));
    }

    public function testFind()
    {
        $tree = new Node;
        $node1 = new Node;
        $node2 = new Node;

        $tree->add($node1);
        $node1->add($node2);

        $this->assertNull($tree->find($node2->hashCode(), false));
        $this->assertEquals($node2, $tree->find($node2->hashCode()));
    }

    public function testIterator()
    {
        $tree = new Node;
        $nodes = [new Node, new Node];

        $tree->addAll($nodes);

        foreach ($tree as $node) {
            $this->assertEquals(array_shift($nodes), $node);
        }
    }

    public function testRecursiveIterator()
    {
        $tree = new Node;
        $tree->add($node1 = new Node);
        $node1->add($node11 = new Node);
        $node11->add($node111 = new Node);
        $node1->add($node12 = new Node);
        $tree->add($node2 = new Node);

        $nodes = [$node111, $node12, $node2];
        foreach(new \RecursiveIteratorIterator($tree, \RecursiveIteratorIterator::LEAVES_ONLY) as $node) {
            $this->assertEquals(array_shift($nodes), $node);
        }

        $nodes = [$node1, $node11, $node111, $node12, $node2];
        foreach(new \RecursiveIteratorIterator($tree, \RecursiveIteratorIterator::SELF_FIRST) as $node) {
            $this->assertEquals(array_shift($nodes), $node);
        }

        $nodes = [$node111, $node11, $node12, $node1, $node2];
        foreach(new \RecursiveIteratorIterator($tree, \RecursiveIteratorIterator::CHILD_FIRST) as $node) {
            $this->assertEquals(array_shift($nodes), $node);
        }
    }
}
