<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 * $Id$
 */

/**
 * A reimplementation of PHP 5.3 SplDoublyLinkedList datastructure.
 * The only intended difference is the lacking implementation of
 * the ArrayAccess interface in order to improve reliability.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class SplDoublyLinkedList implements Countable, Iterator
{
	const IT_MODE_LIFO = 2;
	const IT_MODE_FIFO = 0;
	const IT_MODE_DELETE = 1;
	const IT_MODE_KEEP = 0;

	/**
	 * The datastructure iteration mode.
	 * @var integer
	 */
	protected $_iteratorMode = 0;

	/**
	 * The data kept in this datastructure.
	 * @var array
	 */
	protected $_data = array();

	/**
	 * The number of elements in the datastructure.
	 * @var int
	 */
	protected $_count = 0;

	/**
	 * Iteration count.
	 * @var int
	 */
	protected $_itElement = 0;

	/**
	 * The iterator.
	 * @var int
	 */
	protected $_itKey = 0;

	/**
	 * The internal pointer.
	 * @var int
	 */
	protected $_i = 0;

	/**
	 * The internal pointer end
	 * @var int
	 */
	protected $_finish = 0;

	/**
	 * Returns true, if the specified datastructure is empty.
	 *
	 * @return boolean
	 */
	public function isEmpty()
	{
		return ($this->_count == 0);
	} // end isEmpty();

	/**
	 * Pushes the data into the datastructure.
	 *
	 * @param mixed $value The data to push
	 */
	public function push($value)
	{
		array_push($this->_data, $value);
		$this->_count++;
	} // end push();

	/**
	 * Pops the data off the datastructure.
	 *
	 * @throws RuntimeException
	 * @return mixed
	 */
	public function pop()
	{
		if($this->_count > 0)
		{
			$this->_count--;
			return array_pop($this->_data);
		}
		throw new RuntimeException('Can\'t pop from an empty datastructure');
	} // end pop();

	/**
	 * Returns the top element of the datastructure without removing
	 * it.
	 *
	 * @throws RuntimeException
	 * @return mixed
	 */
	public function top()
	{
		if($this->_count == 0)
		{
			throw new RuntimeException('Can\'t peek at an empty datastructure');
		}
		return end($this->_data);
	} // end top();

	/**
	 * Returns the bottom element of the datastructure without removing
	 * it.
	 *
	 * @throws RuntimeException
	 * @return mixed
	 */
	public function bottom()
	{
		if($this->_count == 0)
		{
			throw new RuntimeException('Can\'t peek at an empty datastructure');
		}
		reset($this->_data);
		return current($this->_data);
	} // end bottom();

	/**
	 * Prepends the data to the beginning of the datastructure.
	 *
	 * @param mixed $data The data to prepend.
	 */
	public function unshift($data)
	{
		$this->_count++;
		array_unshift($this->_data, $data);
	} // end enqueue();

	/**
	 * Shifts the data off the beginning of the datastructure.
	 *
	 * @return mixed
	 */
	public function shift()
	{
		if($this->_count > 0)
		{
			$this->_count--;
			return array_shift($this->_data);
		}
		throw new RuntimeException('Can\'t shift from an empty datastructure');
	} // end shift();

	/**
	 * Sets the iteration mode for the datastructure.
	 *
	 * @param int $mode The new mode.
	 */
	public function setIteratorMode($mode)
	{
		$this->_iteratorMode = $mode;
	} // end setIteratorMode();

	/**
	 * Returns the current iteration mode for the datastructure.
	 *
	 * @return int
	 */
	public function getIteratorMode()
	{
		return $this->_iteratorMode;
	} // end getIteratorMode();

	/**
	 * Returns the number of elements in the datastructure.
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->_count;
	} // end count();

	/**
	 * Rewinds the iterator to the beginning of the datastructure.
	 */
	public function rewind()
	{
		if(!($this->_iteratorMode & self::IT_MODE_LIFO))
		{
			$this->_itElement = reset($this->_data);
			$this->_itKey = key($this->_data);
		}
		else
		{
			$this->_itElement = end($this->_data);
			$this->_itKey = key($this->_data);
		}
		$this->_i = 0;
		$this->_finish = $this->_count;
	} // end rewind();

	/**
	 * Moves the internal pointer to the next element according
	 * to the current iteration mode.
	 */
	public function next()
	{
		if(!($this->_iteratorMode & self::IT_MODE_LIFO))
		{
			$this->_itElement = next($this->_data);
			$this->_itKey = key($this->_data);
			if($this->_iteratorMode & self::IT_MODE_DELETE)
			{
				array_shift($this->_data);
				$this->_count--;
			}
		}
		else
		{
			if($this->_iteratorMode & self::IT_MODE_DELETE)
			{
				array_pop($this->_data);
				$this->_itElement = end($this->_data);
				$this->_count--;
			}
			else
			{
				$this->_itElement = prev($this->_data);
			}			
			$this->_itKey = key($this->_data);
		}
		$this->_i++;
	} // end next();

	/**
	 * Returns the currently pointed element. If the pointer
	 * went out of the border, the behaviour is undefined.
	 *
	 * @return mixed
	 */
	public function current()
	{
		return $this->_itElement;
	} // end current();

	/**
	 * Returns true, if the internal iterator is set to
	 * a valid position in the datastructure.
	 *
	 * @return boolean
	 */
	public function valid()
	{
		return ($this->_i < $this->_finish);
	} // end valid();

	/**
	 * Returns the key of the currently pointed element. If the pointer
	 * went out of the border, the behaviour is undefined.
	 *
	 * @return int
	 */
	public function key()
	{
		return $this->_itKey;
	} // end key();

} // end SplDoublyLinkedList;

/**
 * A PHP 5.2 reimplementation of SplQueue datastructure.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class SplQueue extends SplDoublyLinkedList
{
	/**
	 * Enqueues a new element.
	 *
	 * @param mixed $data The data to enqueue.
	 */
	public function enqueue($data)
	{
		array_push($this->_data, $data);
		$this->_count++;
	} // end enqueue();

	/**
	 * Dequeues an element off the datastructure.
	 *
	 * @throws RuntimeException
	 * @return The dequeued data.
	 */
	public function dequeue()
	{
		if($this->_count > 0)
		{
			$this->_count--;
			return array_shift($this->_data);
		}
		throw new RuntimeException('Can\'t shift from an empty datastructure');
	} // end dequeue();

	/**
	 * Sets the iteration mode for the datastructure. Note that it is impossible
	 * to change the iteration direction for queues. In this case, an exception
	 * is thrown.
	 *
	 * @throws RuntimeException
	 * @param int $mode The new mode.
	 */
	public function setIteratorMode($mode)
	{
		if($mode & self::IT_MODE_LIFO)
		{
			throw new RuntimeException('Iterators\' LIFO/FIFO modes for SplStack/SplQueue objects are frozen');
		}
		parent::setIteratorMode($mode);
	} // end setIteratorMode();
} // end SplQueue;

/**
 * A reimplementation of the SplStack datastructure from PHP 5.3.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class SplStack extends SplDoublyLinkedList
{
	/**
	 * The new default iteration mode.
	 * @var integer
	 */
	protected $_iteratorMode = 2;

	/**
	 * Sets the iteration mode for the datastructure. Note that it is impossible
	 * to change the iteration direction for stacks. In this case, an exception
	 * is thrown.
	 *
	 * @throws RuntimeException
	 * @param int $mode The new mode.
	 */
	public function setIteratorMode($mode)
	{
		if(!($mode & self::IT_MODE_LIFO))
		{
			throw new RuntimeException('Iterators\' LIFO/FIFO modes for SplStack/SplQueue objects are frozen');
		}
		parent::setIteratorMode($mode);
	} // end setIteratorMode();
} // end SplStack;
