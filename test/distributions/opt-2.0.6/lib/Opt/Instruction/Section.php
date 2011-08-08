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

	class Opt_Instruction_Section extends Opt_Instruction_BaseSection
	{
		protected $_name = 'section';
		
		public function configure()
		{
			$this->_addInstructions(array('opt:section', 'opt:sectionelse', 'opt:show', 'opt:showelse'));
			$this->_addAttributes('opt:section');
		} // end configure();

		protected function _processSection(Opt_Xml_Element $node)
		{
			$section = $this->_sectionCreate($node);
			$this->_sectionStart($section);

			$code = $section['format']->get('section:loopBefore');
			if($section['order'] == 'asc')
			{
				$code .= $section['format']->get('section:startAscLoop');
			}
			else
			{
				$code .= $section['format']->get('section:startDescLoop');
			}
			$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $code);
			$this->processSeparator('$__sect_'.$section['name'], $section['separator'], $node);
			$this->_sortSectionContents($node, 'opt', 'sectionelse');

			$node->set('postprocess', true);
			$this->_process($node);
		} // end _processSection();
		
		protected function _postprocessSection(Opt_Xml_Element $node)
		{
			$section = self::getSection($node->get('priv:section'));
			if(!$node->get('priv:alternative'))
			{
				$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $section['format']->get('section:endLoop'));
				$this->_sectionEnd($node);
			}
		} // end _postprocessSection();
		
		protected function _processShowelse(Opt_Xml_Element $node)
		{
			$parent = $node->getParent();
			if($parent instanceof Opt_Xml_Element && $parent->getXmlName() == 'opt:show')
			{
				$parent->set('priv:alternative', true);
				$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, ' } else { ');
				$this->_process($node);
			}
			else
			{
				throw new Opt_InstructionInvalidParent_Exception($node->getXmlName(), 'opt:show');
			}
		} // end _processShowelse();

		protected function _processSectionelse(Opt_Xml_Element $node)
		{
			$parent = $node->getParent();
			if($parent instanceof Opt_Xml_Element && $parent->getXmlName() == 'opt:section')
			{
				$parent->set('priv:alternative', true);
				
				$section = self::getSection($parent->get('priv:section'));
				if(!is_array($section))
				{
					throw new Opt_APINoDataReturned_Exception('Opt_Instruction_BaseSection::getSection', 'processing opt:sectionelse');
				}
				$node->addBefore(Opt_Xml_Buffer::TAG_BEFORE, $section['format']->get('section:endLoop').' } else { ');
				
				$this->_sectionEnd($parent);

				$this->_process($node);
			}
			else
			{
				throw new Opt_InstructionInvalidParent_Exception($node->getXmlName(), 'opt:section');
			}
		} // end _processSectionelse();
		
		protected function _processAttrSection(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$section = $this->_sectionCreate($node, $attr);
			$this->_sectionStart($section);
			$code = $section['format']->get('section:loopBefore');
			if($section['order'] == 'asc')
			{
				$code .= $section['format']->get('section:startAscLoop');
			}
			else
			{
				$code .= $section['format']->get('section:startDescLoop');
			}
			$node->addAfter(Opt_Xml_Buffer::TAG_BEFORE, $code);
			$this->processSeparator('$__sect_'.$section['name'], $section['separator'], $node);
			$attr->set('postprocess', true);
		} // end _processAttrSection();
		
		protected function _postprocessAttrSection(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
		{
			$section = self::getSection($node->get('priv:section'));
			$node->addBefore(Opt_Xml_Buffer::TAG_AFTER, $section['format']->get('section:endLoop'));
			$this->_sectionEnd($node);
		} // end _postprocessAttrSection();
	} // end Opt_Instruction_Section;