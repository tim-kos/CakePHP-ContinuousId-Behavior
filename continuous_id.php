<?php
/**
 * ContinuousId Behavior Behavior class file.
 *
 * @filesource
 * @author Tim Koschuetzki
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package app
 * @subpackage app.models.behaviors
 */
/**
 * Model behavior to support an uuid-independent incrementable id for a model object.
 * Used to have mysql auto_increment behavior and uuid primary keys at the same time.
 *
 * @package app
 * @subpackage app.models.behaviors
 */
class ContinuousIdBehavior extends ModelBehavior {
/**
 * Contain settings indexed by model name.
 *
 * @var array
 * @access private
 */
	var $__settings = array();
/**
 * Initiate behavior for the model using settings.
 *
 * @param object $Model Model using the behavior
 * @param array $settings Settings to override for model.
 * @access public
 */
	function setup($Model, $settings = array()) {
		$defaults = array(
			'field' => 'continuous_id',
			'conditions' => array(),
			'offset' => '1'
		);

		if (!isset($this->__settings[$Model->alias])) {
			$this->__settings[$Model->alias] = $defaults;
		}
		$this->__settings[$Model->alias] = am(
			$this->__settings[$Model->alias],
			ife(is_array($settings), $settings, array())
		);
	}
/**
 * afterSave callback for the model
 *
 * @param string $Model
 * @param string $created
 * @return void
 * @access public
 */
	function afterSave($Model, $created, $primary = false) {
		if ($created) {
			$this->id($Model, $Model->id, true);
		}
		return true;
	}
/**
 * Generates a new continuous id based on the last record in the model's table
 *
 * @param string $Model
 * @param string $id
 * @return void
 * @author Tim Koschuetzki
 */
	function id($Model, $id) {
		$field = $this->__settings[$Model->alias]['field'];
		$offset = $this->__settings[$Model->alias]['offset'];
		$conditions = $this->__settings[$Model->alias]['conditions'];
		require_once(LIBS . 'security.php');

		$key = $Model->lookup(compact('id'), $field, false);
		if (!empty($key)) {
			return $key;
		}

		$conditions[$Model->alias . '.id <>'] = $id;
		$last = $Model->find('first', array(
			'conditions' => $conditions,
			'order' => array($Model->alias . '.' . $field => 'desc'),
		));

		$key = $offset;
		if (!empty($last) && $last[$Model->alias][$field] >= $offset) {
			$key = $last[$Model->alias][$field] + 1;
		}
		$Model->set(array('id' => $id, $field => $key));
		$Model->save(null, array('callbacks' => false));

		return $key;
	}
}
?>