<?php
/*
 * SPL TEST
 * ------------------------------------
 * This unit test suite checks the correctness of the internal Spl
 * datastructure implementation for PHP 5.2.
 */

require_once('PHPUnit/Framework.php');

if(!defined('GROUPED'))
{
	$config = parse_ini_file('../paths.ini', true);
	require($config['libraries']['Opl'].'Base.php');
	Opl_Loader::loadPaths($config);
	Opl_Loader::register();
}

/**
 * The SPL implementation tests.
 */
class splTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers SplDoublyLinkedList
	 */
	public function testLinkedListIteration()
	{
		$ds = new SplDoublyLinkedList;
		$ds->push('foo');
		$ds->push('bar');
		$ds->push('joe');

		$out = array();
		foreach($ds as $item)
		{
			$out[] = $item;
		}
		$this->assertEquals(array('foo', 'bar', 'joe'), $out);
	} // end testLinkedListIteration();

	/**
	 * @covers SplDoublyLinkedList
	 */
	public function testLinkedListIterationInDifferentMode()
	{
		$ds = new SplDoublyLinkedList;
		$ds->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_KEEP);
		$ds->push('foo');
		$ds->push('bar');
		$ds->push('joe');

		$out = array();
		foreach($ds as $item)
		{
			$out[] = $item;
		}
		$this->assertEquals(array('joe', 'bar', 'foo'), $out);
	} // end testLinkedListIterationInDifferentMode();

	/**
	 * @covers SplDoublyLinkedList
	 */
	public function testPushingAndPopping()
	{
		$ds = new SplDoublyLinkedList;
		$ds->push('foo');
		$ds->push('bar');
		$ds->push('joe');

		$out = array();
		$out[] = $ds->pop();
		$out[] = $ds->pop();
		$out[] = $ds->pop();
		$this->assertEquals(array('joe', 'bar', 'foo'), $out);
	} // end testPushingAndPopping();

	/**
	 * @covers SplDoublyLinkedList
	 */
	public function testShiftingAndUnshifting()
	{
		$ds = new SplDoublyLinkedList;
		$ds->unshift('foo');
		$ds->unshift('bar');
		$ds->unshift('joe');

		$out = array();
		$out[] = $ds->shift();
		$out[] = $ds->shift();
		$out[] = $ds->shift();
		$this->assertEquals(array('joe', 'bar', 'foo'), $out);
	} // end testShiftingAndUnshifting();

	/**
	 * @covers SplDoublyLinkedList::pop
	 * @expectedException RuntimeException
	 */
	public function testEmptyPoppingThrowsException()
	{
		$ds = new SplDoublyLinkedList;
		$ds->pop();
	} // end testEmptyPoppingThrowsException();

	/**
	 * @covers SplDoublyLinkedList::top
	 * @expectedException RuntimeException
	 */
	public function testEmptyToppingThrowsException()
	{
		$ds = new SplDoublyLinkedList;
		$ds->top();
	} // end testEmptyToppingThrowsException();

	/**
	 * @covers SplDoublyLinkedList::bottom
	 * @expectedException RuntimeException
	 */
	public function testEmptyBottomingThrowsException()
	{
		$ds = new SplDoublyLinkedList;
		$ds->bottom();
	} // end testEmptyBottomingThrowsException();

	/**
	 * @covers SplDoublyLinkedList::shift
	 * @expectedException RuntimeException
	 */
	public function testEmptyShiftingThrowsException()
	{
		$ds = new SplDoublyLinkedList;
		$ds->shift();
	} // end testEmptyBottomingThrowsException();

	/**
	 * @covers SplDoublyLinkedList::isEmpty
	 */
	public function testIsEmptyShowsTheCorrectStatus()
	{
		$ds = new SplDoublyLinkedList;
		$this->assertTrue($ds->isEmpty());
		$ds->push('foo');
		$this->assertFalse($ds->isEmpty());
	} // end testIsEmptyShowsTheCorrectStatus();

	/**
	 * @covers SplDoublyLinkedList::count
	 */
	public function testCountShowsTheNumberOfElements()
	{
		$ds = new SplDoublyLinkedList;
		$this->assertEquals(0, $ds->count());
		$ds->push('foo');
		$this->assertEquals(1, $ds->count());
		$ds->push('foo');
		$this->assertEquals(2, $ds->count());
		$ds->push('foo');
		$this->assertEquals(3, $ds->count());
		$ds->pop();
		$this->assertEquals(2, $ds->count());
	} // end testCountShowsTheNumberOfElements();

	/**
	 * @covers SplDoublyLinkedList
	 */
	public function testLinkedListIterationWithDeleting()
	{
		$ds = new SplDoublyLinkedList;
		$ds->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO | SplDoublyLinkedList::IT_MODE_DELETE);
		$ds->push('foo');
		$ds->push('bar');
		$ds->push('joe');

		$out = array();
		foreach($ds as $item)
		{
			$out[] = $item;
		}
		$this->assertEquals(array('foo', 'bar', 'joe'), $out);
		$this->assertTrue($ds->isEmpty());
	} // end testLinkedListIterationWithDeleting();

	/**
	 * @covers SplDoublyLinkedList
	 */
	public function testLinkedListIterationInDifferentModeWithDeleting()
	{
		$ds = new SplDoublyLinkedList;
		$ds->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_DELETE);
		$ds->push('foo');
		$ds->push('bar');
		$ds->push('joe');

		$out = array();
		foreach($ds as $item)
		{
			$out[] = $item;
		}
		$this->assertEquals(array('joe', 'bar', 'foo'), $out);
		$this->assertTrue($ds->isEmpty());
	} // end testLinkedListIterationInDifferentModeWithDeleting();

	/**
	 * @covers SplDoublyLinkedList::getIteratorMode
	 */
	public function testGettingIteratorMode()
	{
		$ds = new SplDoublyLinkedList;
		$this->assertEquals(0, $ds->getIteratorMode());
		$ds->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO);
		$this->assertEquals(2, $ds->getIteratorMode());
		$ds->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_DELETE);
		$this->assertEquals(3, $ds->getIteratorMode());
		$ds->setIteratorMode(SplDoublyLinkedList::IT_MODE_DELETE);
		$this->assertEquals(1, $ds->getIteratorMode());
	} // end testGettingIteratorMode();

	/**
	 * @covers SplQueue
	 */
	public function testEnqueuingAndDequeuing()
	{
		$ds = new SplQueue;
		$ds->enqueue('foo');
		$this->assertEquals(1, $ds->count());
		$ds->enqueue('bar');
		$this->assertEquals(2, $ds->count());
		$ds->enqueue('joe');
		$this->assertEquals(3, $ds->count());

		$out = array();
		$out[] = $ds->dequeue();
		$this->assertEquals(2, $ds->count());
		$out[] = $ds->dequeue();
		$this->assertEquals(1, $ds->count());
		$out[] = $ds->dequeue();
		$this->assertEquals(0, $ds->count());
		$this->assertEquals(array('foo', 'bar', 'joe'), $out);
	} // end testEnqueuingAndDequeuing();

	/**
	 * @covers SplQueue
	 * @expectedException RuntimeException
	 */
	public function testChangingDirectionForQueueThrowsException()
	{
		$ds = new SplQueue;
		$ds->setIteratorMode(SplQueue::IT_MODE_LIFO);
	} // end testChangingIteratorModesForQueueThrowsException();

	/**
	 * @covers SplStack
	 * @expectedException RuntimeException
	 */
	public function testChangingDirectionForStackThrowsException()
	{
		$ds = new SplStack;
		$ds->setIteratorMode(SplQueue::IT_MODE_FIFO);
	} // end testChangingIteratorModesForStackThrowsException();

	/**
	 * @covers SplQueue
	 */
	public function testChangingModeForQueueDoesNotThrowException()
	{
		$ds = new SplQueue;
		$ds->setIteratorMode(SplQueue::IT_MODE_DELETE);
	} // end testChangingModeForQueueDoesNotThrowException();

	/**
	 * @covers SplStack
	 */
	public function testChangingModeForStackDoesNotThrowException()
	{
		$ds = new SplStack;
		$ds->setIteratorMode(SplQueue::IT_MODE_LIFO | SplQueue::IT_MODE_DELETE);
	} // end testChangingModeForStackDoesNotThrowException();

	/**
	 * @covers SplStack
	 */
	public function testStackIteration()
	{
		$ds = new SplStack;
		$ds->push('foo');
		$ds->push('bar');
		$ds->push('joe');

		$out = array();
		foreach($ds as $item)
		{
			$out[] = $item;
		}
		$this->assertEquals(array('joe', 'bar', 'foo'), $out);
	} // end testStackIteration();

	/**
	 * @covers SplQueue
	 */
	public function testQueueIteration()
	{
		$ds = new SplQueue;
		$ds->push('foo');
		$ds->push('bar');
		$ds->push('joe');

		$out = array();
		foreach($ds as $item)
		{
			$out[] = $item;
		}
		$this->assertEquals(array('foo', 'bar', 'joe'), $out);
	} // end testQueueIteration();
} // end splTest;