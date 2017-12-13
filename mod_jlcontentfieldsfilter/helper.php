<?php
/**
 * JL Content Fields Filter
 *
 * @version 	1.0.4
 * @author		Joomline
 * @copyright	(C) 2017 Arkadiy Sedelnikov. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_ROOT. '/administrator/components/com_fields/helpers/fields.php';

class ModJlContentFieldsFilterHelper
{
	public static function getFields($params, $category_id, $values)
	{
		$app = JFactory::getApplication();
		$fields = array();
        $enabledFields = $params->get('searchfields', array());
		$template = $app->getTemplate();
        if(!is_array($enabledFields) && !count($enabledFields))
        {
            return $fields;
        }

		$context = 'com_content.article';

		$item = new stdClass();
		$item->language = $app->getLanguage()->getTag();
		$item->catid = $category_id;

		$fields = FieldsHelper::getFields($context, $item);
		if(count($fields)){
			$fieldIds = array_map(
				function ($f)
				{
					return $f->id;
				},
				$fields
			);

			$new = array();

			foreach ($fields as $key => $original)
			{
				if(!in_array($original->id, $enabledFields)){
					unset($fieldIds[$key]);
					continue;
				}

				$field = clone $original;
				$field->value = isset($values[$field->id]) ? $values[$field->id] : '';
				$field->rawvalue = $field->value;

				$basePath = is_file(JPATH_ROOT.'/templates/'.$template.'/html/layouts/mod_jlcontentfieldsfilter/'.$field->type.'.php')
					? JPATH_ROOT.'/templates/'.$template.'/html/layouts'
					: JPATH_ROOT.'/modules/mod_jlcontentfieldsfilter/layouts';

				$new[$key] = JLayoutHelper::render(
					'mod_jlcontentfieldsfilter.'.$field->type,
					array('field' => $field),
					$basePath,
					array('component' => 'auto', 'client' => 0, 'suffixes' => array())
				);
			}
			$fields = $new;
		}
		return $fields;
	}

	public static function getOrderingSelect($selectedOrdering){
		$app = JFactory::getApplication();
		$template = $app->getTemplate();

		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('MOD_JLCONTENTFIELDSFILTER_ORDERING_DEFAULT'));
		$options[] = JHtml::_('select.option', 'ordering.asc', JText::_('MOD_JLCONTENTFIELDSFILTER_ORDERING_ASC'));
		$options[] = JHtml::_('select.option', 'ordering.desc', JText::_('MOD_JLCONTENTFIELDSFILTER_ORDERING_DESC'));
		$options[] = JHtml::_('select.option', 'title.asc', JText::_('MOD_JLCONTENTFIELDSFILTER_TITLE_ASC'));
		$options[] = JHtml::_('select.option', 'title.desc', JText::_('MOD_JLCONTENTFIELDSFILTER_TITLE_DESC'));
		$options[] = JHtml::_('select.option', 'created.asc', JText::_('MOD_JLCONTENTFIELDSFILTER_CREATED_ASC'));
		$options[] = JHtml::_('select.option', 'created.desc', JText::_('MOD_JLCONTENTFIELDSFILTER_CREATED_DESC'));
		$options[] = JHtml::_('select.option', 'created_by.asc', JText::_('MOD_JLCONTENTFIELDSFILTER_CREATED_BY_ASC'));
		$options[] = JHtml::_('select.option', 'created_by.desc', JText::_('MOD_JLCONTENTFIELDSFILTER_CREATED_BY_DESC'));
		$options[] = JHtml::_('select.option', 'hits.asc', JText::_('MOD_JLCONTENTFIELDSFILTER_HITS_ASC'));
		$options[] = JHtml::_('select.option', 'hits.desc', JText::_('MOD_JLCONTENTFIELDSFILTER_HITS_DESC'));

		$basePath = is_file(JPATH_ROOT.'/templates/'.$template.'/html/layouts/mod_jlcontentfieldsfilter/ordering.php')
			? JPATH_ROOT.'/templates/'.$template.'/html/layouts'
			: JPATH_ROOT.'/modules/mod_jlcontentfieldsfilter/layouts';

		$html = JLayoutHelper::render(
			'mod_jlcontentfieldsfilter.ordering',
			array('options' => $options, 'selected' => $selectedOrdering),
			$basePath,
			array('component' => 'auto', 'client' => 0)
		);

		return $html;
	}
}
