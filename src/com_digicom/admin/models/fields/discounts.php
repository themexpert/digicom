<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

/**
 * Form Field class for the Joomla Platform.
 *
 * @package     Joomla.Libraries
 * @subpackage  Form
 * @since       3.2
 */
class JFormFieldDiscounts extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   3.2
	 */
	protected $type = 'Discounts';

	/**
	 * The form field content type.
	 *
	 * @var		string
	 * @since   3.2
	 */
	protected $contentType;

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   3.2
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'contentType':
				return $this->contentType;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'contentType':
				$this->contentType = (string) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   3.2
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$result = parent::setup($element, $value, $group);

		if ($result == true)
		{
			$this->contentType = (string) $this->element['content_type'];
		}

		return $result;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 *
	 * @since   3.2
	 */
	protected function getInput()
	{
		$attr = '';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= $this->disabled ? ' disabled' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';

		// Initialize JavaScript field attributes.
		$attr .= !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

		$itemId = (int) $this->getItemId();

		$options = $this->getQuery();
		
		// Create a regular list.
		//return JHtml::_('select.genericlist', $this->name, $options, trim($attr), $this->value, $itemId ? 0 : 1);
		return JHTML::_('select.genericlist', $options, $this->name, $attr, 'value', 'text', $this->value);
	
	}

	/**
	 * Builds the query for the ordering list.
	 *
	 * @return  JDatabaseQuery  The query for the ordering form field
	 *
	 * @since   3.2
	 */
	protected function getQuery()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->select('TRIM(code) AS alphabetical')
			->from($db->quoteName('#__digicom_promocodes'))
			->where($db->quoteName('published') . ' = 1')
			->order('alphabetical ASC');
		$db->setQuery($query);
		//echo $query->__tostring();die;
		$promocodes = $db->loadObjectList();
		//print_r($promocodes);die;
		//$options[] = JHTML::_('select.option', $folder, ucfirst($folder));
		$options = array();
		$options[] = (object) array('text' => 'none', 'value' => 'none');

		foreach($promocodes as $key=>$promo)
		{
			$timestart = $promo->codestart;
			$timeend = $promo->codeend;
			$limit = $promo->codelimit;
			$used = $promo->used;
			$now = time();

			$promo_status = false;

			if ( $timeend == 0)
			{
				$promo_status = true;
			}
			else
			{
				if ( $now < $timestart && ( $now <= $timeend || $timeend == $nullDate ) )
				{
					$promo_status = true;
				}
			}
			if ($limit > 0 && $limit == $used)
			{
				$promo_status = false;
			}

			if ($promo_status)
				$options[] = JHTML::_('select.option', $promo->code, $promo->title);
		}
		//print_r($options);die;
		return $options;

	}

	/**
	 * Retrieves the current Item's Id.
	 *
	 * @return  integer  The current item ID
	 *
	 * @since   3.2
	 */
	protected function getItemId()
	{
		return (int) $this->form->getValue('id');
	}
}
