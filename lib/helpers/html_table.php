<?php
class HtmlTable {
	private static function _transform_value_boolean($value) {
		$value = !empty($value) ? 'Yes' : 'No';
		return $value;
	}

	private static function _transform_value_date($value) {
		if ((string) strtotime('now', (int) $value) === $value) {
			$time = (int) $value;
		} else {
			$time = strtotime($value);
		}
		
		if ($time == strtotime('error')) {
			return '';
		}
		
		$value = date('Y-m-d', $time);
		return $value;
	}
	
	private static function _transform_value_datetime($value) {
		if ((string) strtotime('now', (int) $value) === $value) {
			$time = (int) $value;
		} else {
			$time = strtotime($value);
		}
		
		if ($time == strtotime('error')) {
			return '';
		}
		
		$value = date('Y-m-d H:i:s', $time);
		return $value;
	}

	private static function _transform_value_html_format($value, $item, $params) {
		$options = !empty($params[2]) ? $params[2] : array();
		$sprintf_args = array($params[0]);
		
		foreach ($params[1] as $key => $field) {
			if (is_array($item)) {
				$sprintf_args[] = htmlentities($item[$field]);
			} elseif (is_object($item)) {
				$sprintf_args[] = htmlentities($item->$field);
			} else {
				$sprintf_args[] = $field;
			}
		}
		
		if (!empty($options['urlencode'])) {
			foreach ($sprintf_args as $key => $arg) {
				if ($key == 0) {
					continue;
				}

				$sprintf_args[$key] = urlencode($arg);
			}
		}

		$value = call_user_func_array('sprintf', $sprintf_args);
		return $value;
	}
	
	private static function _transform_value_url_format($value, $item, $params) {
		$options = !empty($params[2]) ? $params[2] : array();
		$url =  self::_transform_value_html_format($value, $item, $params);

		$anchor = '<a href="';
		$anchor .= $url;
		$anchor .= '"';
		
		if (!empty($options) && !empty($options['new_window'])) {
			$anchor .= ' target="_blank"';
		}
		
		$anchor .= '>';
		$anchor .= $value;
		$anchor .= '</a>';
		return $anchor;
	}

	public static function build($params) {
		if (empty($params['columns'])) {
			return '';
		}
		
		if (empty($params['data'])) {
			return '';
		}
		
		if (empty($params['order'])) $params['order'] = '';
		
		$fields = array();
		$url = !empty($params['url']) ? $params['url'] : $_SERVER['REQUEST_URI'];
		
		foreach ($params['columns'] as $field => $field_params) {
			if ($field_params === null) continue;
		
			if (is_int($field) && is_scalar($field_params)) {
				$field = $field_params;
				$field_params = array('label' => $field_params);
			}

			if (empty($field_params['label']) && ((!array_key_exists('label', $field_params)) || $field_params['label'] !== false)) {
				$field_params['label'] = ucwords(str_replace(array('-', '_'), ' ', $field));
			}
			
			$fields[$field] = $field_params;
		}
	
		$table = '<table';
		if (!empty($params['class'])) $table .= ' class="' . $params['class'] . '"';
		if (!empty($params['id'])) $table .= ' id="' . $params['id'] . '"';
		$table .= '>';
		
		$table .= '<thead>';
		$table .= '<tr>';
		
		foreach ($fields as $field => $field_params) {
			$var = !empty($field_params['field']) ? $field_params['field'] : $field;
			$table .= '<th';
			if (!empty($field_params['class'])) $table .= ' class="' . $field_params['class'] . '"';
			$table .= '>';
			$label = htmlentities($field_params['label']);
			
			if (!empty($field_params['sortable'])) {
				$new_url = $url;
				if (strpos($new_url, '?') === false) $new_url .= '?';
				$newer_url = substr($new_url, 0, strpos($new_url, '?') + 1);
				$url_params_string = substr($new_url, strlen($newer_url));
		
				if (strlen($url_params_string) > 0) {
					$url_params = explode('&', $url_params_string);
			
					foreach ($url_params as $param) {
						$key_val = explode('=', $param, 2);
						if ($key_val[0] == '') continue;
						if ($key_val[0] == 'order') continue;
						$newer_url .= $param . '&';
					}
				}
				
				$link_start = '<a href="' . $newer_url;
				$link_start .= 'order=';
				
				if (empty($field_params['sortable_reverse'])) {
					if ($var == $params['order']) {
						$link_start .= '-';
					}
				} else {
					if ('-' . $var != $params['order']) {
						$link_start .= '-';
					}
				}
				
				$link_start .= htmlentities($var);
				$link_start .= '">';
				$link_end = '</a>';
				$label = $link_start . $label . $link_end;
			}
			
			$table .= $label;
			$table .= '</th>';
		}
		
		$table .= '</tr>';
		$table .= '</thead>';
		$table .= '<tbody>';
		$i = 0;
		
		foreach ($params['data'] as $key => $data_item) {
			if (empty($data_item)) continue;
			if (!is_array($data_item) && !is_object($data_item)) continue;
			$i++;
		
			$row = '<tr';
			if ($i % 2 == 0) $row .= ' class="even"';
			if ($i % 2 == 1) $row .= ' class="odd"';
			$row .= '>';

			foreach ($fields as $field => $field_params) {
				$var = !empty($field_params['field']) ? $field_params['field'] : $field;
				$cell = '<td';
				if (!empty($field_params['class'])) $cell .= ' class="' . $field_params['class'] . '"';
				$cell .= '>';
			
				if (isset($field_params['value'])) {
					$value = $field_params['value'];
				} elseif (is_array($data_item)) {
					$value = htmlentities($data_item[$var]);
				} elseif (is_object($data_item)) {
					$value = $data_item->$var;
					
					if (!is_array($value)) {
						$value = htmlentities($value);
					}
				}
				
				if (!empty($field_params['values']) && is_scalar($value)) {
					$value = !empty($field_params['values'][$value]) ? $field_params['values'][$value] : '';
				}
				
				if (!empty($field_params['boolean'])) {
					$value = self::_transform_value_boolean($value);
				} elseif (!empty($field_params['date'])) {
					$value = self::_transform_value_date($value);
				} elseif (!empty($field_params['datetime']) || !empty($field_params['timestamp'])) {
					$value = self::_transform_value_datetime($value);
				} elseif (!empty($field_params['html_format'])) {
					$value = self::_transform_value_html_format($value, $data_item, $field_params['html_format']);
				} elseif (!empty($field_params['url'])) {
					$value = self::_transform_value_url_format($value, $data_item, array($value, array()));
				} elseif (!empty($field_params['url_format'])) {
					$value = self::_transform_value_url_format($value, $data_item, $field_params['url_format']);
				}
			
				$cell .= $value;
				$cell .= '</td>';
				$row .= $cell;
			}
			
			$row .= '</tr>';
			$table .= $row;
		}
		
		$table .= '</tbody>';
		$table .= '</table>';
		return $table;
	}
	
	public static function build_single($params) {
		if (empty($params['fields'])) {
			return '';
		}
		
		if (empty($params['item'])) {
			return '';
		}
		
		if (!is_array($params['item']) && !is_object($params['item'])) {
			return '';
		}
		
		$fields = array();
		
		foreach ($params['fields'] as $field => $field_params) {
			if ($field_params === null) {
				continue;
			}
		
			if (is_int($field) && is_scalar($field_params)) {
				$field = $field_params;
				$field_params = array('label' => $field_params);
			}
			
			if (empty($field_params['label']) && ((!array_key_exists('label', $field_params)) || $field_params['label'] !== false)) {
				$field_params['label'] = ucwords(str_replace(array('-', '_'), ' ', $field));
			}
			
			$fields[$field] = $field_params;
		}
		
		$table = '<table';
		if (!empty($params['class'])) $table .= ' class="' . $params['class'] . '"';
		if (!empty($params['id'])) $table .= ' id="' . $params['id'] . '"';
		$table .= '>';
		$i = 0;
		
		foreach ($fields as $field => $field_params) {
			$i++;

			if ($i % 2 == 0) {
				if (!isset($field_params['class'])) {
					$field_params['class'] = 'even';
				} else {
					$field_params['class'] .= ' even';
				}
			}
			
			if ($i % 2 == 1) {
				if (!isset($field_params['class'])) {
					$field_params['class'] = 'odd';
				} else {
					$field_params['class'] .= ' odd';
				}
			}

			$row = '<tr';
			$row .= ' class="' . $field_params['class'] . '"';
			$row .= '>';

			$row .= '<td class="item-table-key">';
			$row .= $field_params['label'];
			$row .= '</td>';
			
			$row .= '<td class="item-table-value">';
			
			if (isset($field_params['value'])) {
				$value = $field_params['value'];
			} elseif (is_array($params['item'])) {
				$value = htmlentities($params['item'][$field]);
			} elseif (is_object($params['item'])) {
				$value = htmlentities($params['item']->$field);
			}
			
			if (!empty($field_params['boolean'])) {
				$value = self::_transform_value_boolean($value);
			} elseif (!empty($field_params['date'])) {
				$value = self::_transform_value_date($value);
			} elseif (!empty($field_params['datetime']) || !empty($field_params['timestamp'])) {
				$value = self::_transform_value_datetime($value);
			} elseif (!empty($field_params['html_format'])) {
				$value = self::_transform_value_html_format($value, $params['item'], $field_params['html_format']);
			} elseif (!empty($field_params['url'])) {
				$value = self::_transform_value_url_format($value, $params['item'], array($value, array()));
			} elseif (!empty($field_params['url_format'])) {
				$value = self::_transform_value_url_format($value, $params['item'], $field_params['url_format']);
			}
			
			$row .= $value;
			$row .= '</td>';
			
			$row .= '</tr>';
			$table .= $row;
		}
		
		$table .= '</table>';
		return $table;
	}
	
	public static function order($order, $default, $fields) {
		$order = !empty($order) ? $order : $default;
		$reverse_order = (substr($order, 0, 1) == '-');
		if ($reverse_order) $order = substr($order, 1);
		if (empty($fields[$order])) $order = $default;
		if ($reverse_order) $order .= ' desc';
		return $order;
	}
	
	public static function paginate($count, $page, $results_per_page) {
		$pagination = array();
		$pagination['count'] = ($count > 0) ? floor($count) : 0;
		$pagination['results_per_page'] = ($results_per_page > 0) ? floor($results_per_page) : 0;
		
		if ($pagination['count'] > 0) {
			$pagination['pages'] = ceil($pagination['count'] / $pagination['results_per_page']);
			if ($page > $pagination['pages']) $page = $pagination['pages'];
			$pagination['page'] = ($page > 1) ? floor($page) : 1;
			$pagination['start'] = 1 + (($pagination['page'] - 1) * $pagination['results_per_page']);
			$pagination['record_start'] = $pagination['start'] - 1;
			$pagination['end'] = ($pagination['page'] == $pagination['pages']) ? $pagination['count'] : $pagination['record_start'] + $pagination['results_per_page'];
			$pagination['range'] = $pagination['start'] . '-' . $pagination['end'];
			$pagination['results'] = $pagination['end'] - $pagination['record_start'];
		} else {
			$pagination['pages'] = 0;
			$pagination['page'] = 0;
			$pagination['start'] = 0;
			$pagination['record_start'] = 0;
			$pagination['end'] = 0;
			$pagination['range'] = 0;
			$pagination['results'] = 0;
		}

		return $pagination;
	}
	
	public static function pagination_links($params) {
		if (empty($params['pagination'])) {
			return '';
		}
	
		$pagination = $params['pagination'];
		$url = !empty($params['url']) ? $params['url'] : $_SERVER['REQUEST_URI'];
		$url_key = !empty($params['url_key']) ? $params['url_key'] : 'page';
		$current_page_class = !empty($params['current_page_class']) ? $params['current_page_class'] : 'current-page';
		
		if (strpos($url, '?') === false) $url .= '?';
		$new_url = substr($url, 0, strpos($url, '?') + 1);
		$url_params_string = substr($url, strlen($new_url));
		
		if (strlen($url_params_string) > 0) {
			$url_params = explode('&', $url_params_string);
			
			foreach ($url_params as $param) {
				$key_val = explode('=', $param, 2);
				if ($key_val[0] == '') continue;
				if ($key_val[0] == $url_key) continue;
				$new_url .= $param . '&';
			}
		}
		
		if ($pagination['count'] == 0) {
			return '';
		}
		
		$links = '';
		
		if ($pagination['page'] > 1) {
			$link = '<a href="' . $new_url . $url_key . '=1">&laquo;</a>';
			$links .= $link . "\r\n";
			$link = '<a href="' . $new_url . $url_key . '=' . ($pagination['page'] - 1) . '">&lt; Previous</a>';
			$links .= $link . "\r\n";
		}
		
		if ($pagination['pages'] <= 11) {
			$start = 1;
			$end = $pagination['pages'];
		} else {
			$start = ($pagination['page'] > 5) ? $pagination['page'] - 5 : 1;
			$end = ($start + 10 < $pagination['pages']) ? $start + 10 : $pagination['pages'];
		}

		for ($i = $start; $i <= $end; $i++) {
			if ($i == $pagination['page']) {
				$span = '<span class="' . $current_page_class . '">' . $i . '</span>';
				$links .= $span . "\r\n";
				continue;
			}
			
			$link = '<a href="' . $new_url . $url_key . '=' . $i . '">' . $i . '</a>';
			$links .= $link . "\r\n";
		}
		
		if ($pagination['page'] < $pagination['pages']) {
			$link = '<a href="' . $new_url . $url_key . '=' . ($pagination['page'] + 1) . '">Next &gt;</a>';
			$links .= $link . "\r\n";
			$link = '<a href="' . $new_url . $url_key . '=' . $pagination['pages'] . '">&raquo;</a>';
			$links .= $link . "\r\n";
		}
		
		return $links;
	}
}
