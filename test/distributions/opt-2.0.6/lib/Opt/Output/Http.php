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
 */

	class Opt_Output_Http implements Opt_Output_Interface
	{
		const XHTML = 0;
		const HTML = 1;
		const FORCED_XHTML = 2;
		const WML = 3;
		const XML = 4;
		const TXT = 5;
	
		protected $_tpl;
		protected $_mode;
		
		// Headers
		protected $_headers = array();
		protected $_headersSent = false;

		/**
		 * The constructor that creates the new HTTP output object.
		 */
		public function __construct()
		{
			$this->_tpl = Opl_Registry::get('opt');
		} // end __construct();

		/**
		 * Returns the name of this output system.
		 *
		 * @return String The "HTTP" word.
		 */
		public function getName()
		{
			return 'HTTP';
		} // end getName();
		
		/*
		 * Header management
		 */

		/**
		 * Sets a HTTP header and secures it by removing the new line characters.
		 *
		 * @param String $name Header name
		 * @param String $value Header value
		 * @return Boolean True if succeed.
		 */
		public function setHeader($name, $value)
		{
			if($this->_headersSent)
			{
				return false;
			}
			$name = strtr($name, "\r\n", '  ');
			$value = strtr($value, "\r\n", '  ');
			if(!$this->_tpl->headerBuffering)
			{
				header($name.': '.$value);
			}
			$this->_headers[$name] = $value;
			return true;
		} // end setHeader();

		/**
		 * Returns the list of headers currently set in the output system.
		 *
		 * @return array
		 */
		public function getHeaders()
		{
			return $this->_headers;
		} // end getHeaders();

		/**
		 * Sends the buffered HTTP headers to the browser.
		 *
		 * @return Boolean True, if succeed.
		 */
		public function sendHeaders()
		{
			if(!$this->_headersSent && $this->_tpl->headerBuffering)
			{
				foreach($this->headers as $name => $value)
				{
					if($name == 'HTTP/1.0' || $name == 'HTTP/1.1')
					{
						header($name.' '.$value);
					}
					else
					{
						header($name.': '.$value);
					}
				}
				return true;
			}
			return false;
		} // end sendHeaders();

		/**
		 * Creates a "Content-type" header from the information provided in the
		 * arguments or in OPT configuration. If one of the arguments is NULL,
		 * the method tries to import equivalent setting from the Opt_Class configuration
		 * fields.
		 *
		 * If Open Power Classes is available, the method may perform a full
		 * content-negotiation procedure.
		 *
		 * @param String|Int $contentType The content type described manually or by the class constants.
		 * @param String $charset The output document encoding.
		 */
		public function setContentType($contentType = null, $charset = null)
		{
			$charset = (is_null($charset) ? $this->_tpl->charset : $charset);
			$contentType = (is_null($contentType) ? $this->_tpl->contentType : $contentType);

			$this->_tpl->charset = $charset;
			$this->_tpl->contentType = $contentType;

			if($charset !== null)
			{
				$charset = ';charset='.$charset;
			}
			$replacements = array(
				self::HTML => 'text/html',
				self::XML => 'application/xml',
				self::WML => 'text/vnd.wap.wml',
				self::TXT => 'text/plain'
			);
			
			if($this->_tpl->contentNegotiation)
			{
				// This part of the code requires OPC!
				$visit = Opc_Visit::getInstance();
				if($contentType == self::XHTML)
				{
					// Choose XHTML or HTML depending on their priority
					$contentType = 'text/html';
					foreach($visit->mimeTypes as $type)
					{
						if($type == 'application/xhtml+xml' || $type == 'text/html')
						{
							$contentType = $type;
							break;
						}
					}
				}
				elseif($contentType == self::FORCED_XHTML)
				{
					// Choose XHTML, if the browser accepts it.
					$contentType = 'text/html';
					if(in_array('application/xhtml+xml',$visit->mimeTypes))
					{
						$contentType = 'application/xhtml+xml';
					}
				}
				else
				{
					// Optionally convert the other constants and check whether
					// the browser supports them.
					if(in_array($contentType, $replacements))
					{
						$contentType = $replacements[$replacements];
					}
					
					if(!in_array($contentType, $visit->mimeTypes))
					{
						$contentType = 'application/octet-stream';
					}					
				}
				$this->setHeader('Vary', 'Accept');
			}
			else
			{
				// Just in case of malformed headers.
				if(!isset($_SERVER['HTTP_ACCEPT']))
				{
					$_SERVER['HTTP_ACCEPT'] = '';
				}

				// No content-negotiation. Do the basic checks only.
				if($contentType == self::XHTML || $contentType == self::FORCED_XHTML)
				{
					if($this->_tpl->debugConsole)
					{
						$contentType = 'text/html';
					}
					elseif(stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
					{
						$contentType = 'application/xhtml+xml';
					}
					else
					{
						$contentType = 'text/html';
					}
				}
				elseif(isset($replacements[$contentType]))
				{
					$contentType = $replacements[$contentType];
				}
			}
			$this->setHeader('Content-type', $contentType.$charset);
		} // end setContentType();
		
		/*
		 * Template rendering
		 */

		/**
		 * Renders the view and sends the results to the browser. Please note
		 * that in order to ensure that the script output will be a valid
		 * XML document, this method can be called only once, for one XML view, forcing
		 * you to modularize the templates with the template inheritance or
		 * opt:include instruction.
		 *
		 * @param Opt_View $view The view object to be rendered.
		 * @return Boolean True, if succeed.
		 */
		public function render(Opt_View $view)
		{			
			if($this->_mode === null)
			{
				$this->_mode = $view->getMode();

				// Initialize output buffering and turn on the compression, if necessary.
				if(!$this->_tpl->debugConsole && $this->_tpl->gzipCompression == true && extension_loaded('zlib') && ini_get('zlib.output_compression') == 0)
				{
					ob_start('ob_gzhandler');
					ob_implicit_flush(0);
				}
				else
				{
					ob_start();
				}

				// Send the headers, if necessary
				$this->sendHeaders();
			}
			elseif($this->_mode == Opt_Class::XML_MODE)
			{
				throw new Opt_OutputOverloaded_Exception;
			}
			$result = $view->_parse($this, true);
			ob_end_flush();
			return $result;
		} // end output();
	} // end Opt_Output_Http;
