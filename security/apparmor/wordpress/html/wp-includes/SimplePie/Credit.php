<?php
/**
 * SimplePie
 *
 * A PHP-Based RSS and Atom Feed Framework.
 * Takes the hard work out of managing a complete RSS/Atom solution.
 *
 * Copyright (c) 2004-2012, Ryan Parman, Geoffrey Sneddon, Ryan McCue, and contributors
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 *
 * 	* Redistributions of source code must retain the above copyright notice, this list of
 * 	  conditions and the following disclaimer.
 *
 * 	* Redistributions in binary form must reproduce the above copyright notice, this list
 * 	  of conditions and the following disclaimer in the documentation and/or other materials
 * 	  provided with the distribution.
 *
 * 	* Neither the name of the SimplePie Team nor the names of its contributors may be used
 * 	  to endorse or promote products derived from this software without specific prior
 * 	  written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS
 * OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 * AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS
 * AND CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package SimplePie
 * @version 1.3.1
 * @copyright 2004-2012 Ryan Parman, Geoffrey Sneddon, Ryan McCue
 * @author Ryan Parman
 * @author Geoffrey Sneddon
 * @author Ryan McCue
 * @link http://simplepie.org/ SimplePie
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Handles `<media:credit>` as defined in Media RSS
 *
 * Used by {@see SimplePie_Enclosure::get_credit()} and {@see SimplePie_Enclosure::get_credits()}
 *
 * This class can be overloaded with {@see SimplePie::set_credit_class()}
 *
 * @package SimplePie
 * @subpackage API
 */
class SimplePie_Credit
{
	/**
	 * Credited role
	 *
	 * @var string
	 * @see get_role()
	 */
	var $role;

	/**
	 * Organizational scheme
	 *
	 * @var string
	 * @see get_scheme()
	 */
	var $scheme;

	/**
	 * Credited name
	 *
	 * @var string
	 * @see get_name()
	 */
	var $name;

	/**
	 * Constructor, used to input the data
	 *
	 * For documentation on all the parameters, see the corresponding
	 * properties and their accessors
	 */
	public function __construct($role = null, $scheme = null, $name = null)
	{
		$this->role = $role;
		$this->scheme = $scheme;
		$this->name = $name;
	}

	/**
	 * String-ified version
	 *
	 * @return string
	 */
	public function __toString()
	{
		// There is no $this->data here
		return md5(serialize($this));
	}

	/**
	 * Get the role of the person receiving credit
	 *
	 * @return string|null
	 */
	public function get_role()
	{
		if ($this->role !== null)
		{
			return $this->role;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the organizational scheme
	 *
	 * @return string|null
	 */
	public function get_scheme()
	{
		if ($this->scheme !== null)
		{
			return $this->scheme;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get the credited person/entity's name
	 *
	 * @return string|null
	 */
	public function get_name()
	{
		if ($this->name !== null)
		{
			return $this->name;
		}
		else
		{
			return null;
		}
	}
}

