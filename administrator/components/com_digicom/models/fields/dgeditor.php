<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('textarea');

/**
 * Form Field class for the Joomla CMS.
 * A textarea field for content creation
 *
 * @see    JEditor
 * @since  1.6
 */
class JFormFieldDGEditor extends JFormFieldTextarea
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	public $type = 'DGEditor';

	/**
	 * The template object.
	 *
	 * @var    template
	 * @since  1.6
	 */
	protected $template;
	/**
	 * The JEditor object.
	 *
	 * @var    JEditor
	 * @since  1.6
	 */
	protected $editor;

	/**
	 * The height of the editor.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $height;

	/**
	 * The width of the editor.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $width;

	/**
	 * The assetField of the editor.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $assetField;

	/**
	 * The authorField of the editor.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $authorField;

	/**
	 * The asset of the editor.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $asset;

	/**
	 * The buttons of the editor.
	 *
	 * @var    mixed
	 * @since  3.2
	 */
	protected $buttons;

	/**
	 * The hide of the editor.
	 *
	 * @var    array
	 * @since  3.2
	 */
	protected $hide;

	/**
	 * The editorType of the editor.
	 *
	 * @var    array
	 * @since  3.2
	 */
	protected $editorType;

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
			case 'height':
			case 'width':
			case 'assetField':
			case 'authorField':
			case 'asset':
			case 'buttons':
			case 'hide':
			case 'editorType':
				return $this->$name;
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
			case 'height':
			case 'width':
			case 'assetField':
			case 'authorField':
			case 'asset':
				$this->$name = (string) $value;
				break;

			case 'buttons':
				$value = (string) $value;

				if ($value == 'true' || $value == 'yes' || $value == '1')
				{
					$this->buttons = true;
				}
				elseif ($value == 'false' || $value == 'no' || $value == '0')
				{
					$this->buttons = false;
				}
				else
				{
					$this->buttons = explode(',', $value);
				}
				break;

			case 'hide':
				$value = (string) $value;
				$this->hide = $value ? explode(',', $value) : array();
				break;

			case 'editorType':
				// Can be in the form of: editor="desired|alternative".
				$this->editorType  = explode('|', trim((string) $value));
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
			$this->height      = $this->element['height'] ? (string) $this->element['height'] : '500';
			$this->width       = $this->element['width'] ? (string) $this->element['width'] : '100%';
			$this->assetField  = $this->element['asset_field'] ? (string) $this->element['asset_field'] : 'asset_id';
			$this->authorField = $this->element['created_by_field'] ? (string) $this->element['created_by_field'] : 'created_by';
			$this->asset       = $this->form->getValue($this->assetField) ? $this->form->getValue($this->assetField) : (string) $this->element['asset_id'];

			$buttons    = (string) $this->element['buttons'];
			$hide       = (string) $this->element['hide'];
			$editorType = (string) $this->element['editor'];

			if ($buttons == 'true' || $buttons == 'yes' || $buttons == '1')
			{
				$this->buttons = true;
			}
			elseif ($buttons == 'false' || $buttons == 'no' || $buttons == '0')
			{
				$this->buttons = false;
			}
			else
			{
				$this->buttons = !empty($hide) ? explode(',', $buttons) : array();
			}

			$this->hide        = !empty($hide) ? explode(',', (string) $this->element['hide']) : array();
			$this->editorType  = !empty($editorType) ? explode('|', trim($editorType)) : array();
		}

		return $result;
	}
	
	
	/**
	 * Method to get the field input markup for the editor area
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.6
	 */
	protected function getInput()
	{
		
		$app = JFactory::getApplication();
		$path = $this->getAttribute('path'); ///components/com_digicom/emails/
		$filename = $this->getAttribute('filename'); //new-order.php
		$overridepath = $this->getAttribute('overridepath'); //template || root
		$override = $this->getAttribute('override'); // /html/com_digicom/emails/
		
		// override is always false for now
		// when we will support edit file then it will be true
		$override_action = false;
		$wclass        = !empty($this->class) ? ' '. $this->class : '';
		$return = '<div class="dgeditor email_template_digicom'.$wclass.'">';

		if(!empty($overridepath)){
			//TODO:: !empty
			switch($overridepath){
				case 'template':
					if (!$this->template)
					{
						$this->template  = $this->getTemplate();
					}
					
					if ($this->template)
					{
						$client   = JApplicationHelper::getClientInfo($this->template->client_id);
						$filePath = JPath::clean($client->path . '/templates/' . $this->template->template . $override.'/'.$filename);
						//echo $filePath;die;
						if (file_exists($filePath))
						{
							$return .= JText::sprintf('COM_DIGICOM_CONFIG_EDITOR_EDIT_PATH_YOURSELF',$filename,'templates/yourtemplates'.$override).'<br/>';
							$this->value = file_get_contents($filePath);
						}
						else
						{
							$override_action = false;
							$overridemsg = $this->getAttribute('overridemsg'); ///html/com_digicom/emails/
							$return .= JText::_($overridemsg).'<br/>';
							
							$filePath = JPath::clean($client->path . $path . '/'.$filename);
							$this->value = file_get_contents($filePath);
						}
						
					}
					break;
				case 'root':
					break;
			}
		}
		
		if($override_action){
			// Get an editor object.
			$editor = $this->getEditor();

			$return .= $editor->display(
				$this->name, htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'), $this->width, $this->height, $this->cols, $this->rows,
				$this->buttons ? (is_array($this->buttons) ? array_merge($this->buttons, $this->hide) : $this->hide) : false, $this->id, $this->asset,
				$this->form->getValue($this->authorField), array('syntax' => (string) $this->element['syntax'])
			);

		}else{
			// Including fallback code for HTML5 non supported browsers.
			JHtml::_('jquery.framework');
			JHtml::_('script', 'system/html5fallback.js', false, true);
			// Translate placeholder text
			$hint = $this->translateHint ? JText::_($this->hint) : $this->hint;

			// Initialize some field attributes.
			$class        = !empty($this->class) ? ' class="' . $this->class . '"' : '';
			$disabled     = $this->disabled ? ' disabled' : '';
			$readonly     = $this->readonly ? ' readonly' : '';
			$columns      = $this->columns ? ' cols="' . $this->columns . '"' : '';
			$rows         = $this->rows ? ' rows="' . $this->rows . '"' : '';
			$required     = $this->required ? ' required aria-required="true"' : '';
			$hint         = $hint ? ' placeholder="' . $hint . '"' : '';
			$autocomplete = !$this->autocomplete ? ' autocomplete="off"' : ' autocomplete="' . $this->autocomplete . '"';
			$autocomplete = $autocomplete == ' autocomplete="on"' ? '' : $autocomplete;
			$autofocus    = $this->autofocus ? ' autofocus' : '';
			$spellcheck   = $this->spellcheck ? '' : ' spellcheck="false"';

			// Initialize JavaScript field attributes.
			$onchange = $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
			$onclick = $this->onclick ? ' onclick="' . $this->onclick . '"' : '';
			
			$return .= '<textarea name="' . $this->name . '" id="' . $this->id . '"' . $columns . $rows . $class
				. $hint . $disabled . $readonly . $onchange . $onclick . $required . $autocomplete . $autofocus . $spellcheck . ' >'
				. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '</textarea>';
		}
		$return .='</div>';
		return $return; 
	}

	/**
	 * Method to get a JEditor object based on the form field.
	 *
	 * @return  JEditor  The JEditor object.
	 *
	 * @since   1.6
	 */
	protected function getEditor()
	{
		// Only create the editor if it is not already created.
		if (empty($this->editor))
		{
			$editor = null;

			if ($this->editorType)
			{
				// Get the list of editor types.
				$types = $this->editorType;

				// Get the database object.
				$db = JFactory::getDbo();

				// Iterate over teh types looking for an existing editor.
				foreach ($types as $element)
				{
					// Build the query.
					$query = $db->getQuery(true)
						->select('element')
						->from('#__extensions')
						->where('element = ' . $db->quote($element))
						->where('folder = ' . $db->quote('editors'))
						->where('enabled = 1');

					// Check of the editor exists.
					$db->setQuery($query, 0, 1);
					$editor = $db->loadResult();

					// If an editor was found stop looking.
					if ($editor)
					{
						break;
					}
				}
			}

			// Create the JEditor instance based on the given editor.
			if (is_null($editor))
			{
				$conf = JFactory::getConfig();
				$editor = $conf->get('editor');
			}

			$this->editor = JEditor::getInstance($editor);
		}

		return $this->editor;
	}

	/**
	 * Method to get the JEditor output for an onSave event.
	 *
	 * @return  string  The JEditor object output.
	 *
	 * @since   1.6
	 */
	public function save()
	{
		return $this->getEditor()->onSave($this->id);
	}

	/*
	* getTemplate
	* get the site template for frontend
	*/

	public static function getTemplate(){
		// Get the database object.
		$db = JFactory::getDbo();
		// Build the query.
		$query = $db->getQuery(true)
			->select('*')
			->from('#__template_styles')
			->where('client_id = ' . $db->quote(0))
			->where('home = ' . $db->quote(1));

		// Check of the editor exists.
		$db->setQuery($query);
		return $db->loadObject();

	}
}
