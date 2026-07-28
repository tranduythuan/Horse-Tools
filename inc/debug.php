<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_debug_options;
// class thay doi thuoc tinh
if( ! class_exists( 'horsetools_chandebug' ) ) {
class horsetools_chandebug {
	const ANCHOR_EOF = 'EOF';
	protected $wp_config_path;
	protected $wp_config_src;
	protected $wp_configs = array();
	public function __construct( $wp_config_path ) {
		$basename = basename( $wp_config_path );
		if ( ! file_exists( $wp_config_path ) ) {
			throw new Exception( "{$basename} does not exist." );
		}

		if ( ! is_writable( $wp_config_path ) ) {
			throw new Exception( "{$basename} is not writable." );
		}
		$this->wp_config_path = $wp_config_path;
	}
	public function exists( $type, $name ) {
		$wp_config_src = file_get_contents( $this->wp_config_path );
		if ( ! trim( $wp_config_src ) ) {
			throw new Exception( 'Config file is empty.' );
		}
		$this->wp_config_src = str_replace( array( "\r\n", "\n\r", "\r" ), "\n", $wp_config_src );
		$this->wp_configs    = $this->parse_wp_config( $this->wp_config_src );

		if ( ! isset( $this->wp_configs[ $type ] ) ) {
			throw new Exception( "Config type '{$type}' does not exist." );
		}
		return isset( $this->wp_configs[ $type ][ $name ] );
	}
	public function get_value( $type, $name ) {
		$wp_config_src = file_get_contents( $this->wp_config_path );
		if ( ! trim( $wp_config_src ) ) {
			throw new Exception( 'Config file is empty.' );
		}
		$this->wp_config_src = $wp_config_src;
		$this->wp_configs    = $this->parse_wp_config( $this->wp_config_src );
		if ( ! isset( $this->wp_configs[ $type ] ) ) {
			throw new Exception( "Config type '{$type}' does not exist." );
		}
		return $this->wp_configs[ $type ][ $name ]['value'];
	}
	public function add( $type, $name, $value, array $options = array() ) {
		if ( ! is_string( $value ) ) {
			throw new Exception( 'Config value must be a string.' );
		}
		if ( $this->exists( $type, $name ) ) {
			return false;
		}
		$defaults = array(
			'raw'       => false, 
			'anchor'    => "require_once",
			'separator' => PHP_EOL, 
			'placement' => 'before', 
		);
		list( $raw, $anchor, $separator, $placement ) = array_values( array_merge( $defaults, $options ) );
		$raw       = (bool) $raw;
		$anchor    = (string) $anchor;
		$separator = (string) $separator;
		$placement = (string) $placement;
		if ( self::ANCHOR_EOF === $anchor ) {
			$contents = $this->wp_config_src . $this->normalize( $type, $name, $this->format_value( $value, $raw ) );
		} else {
			// Find the insertion point as a real PHP TOKEN, not as a raw
			// substring. Searching for the text "require_once" would also match
			// inside a comment or a string literal — a host that ships
			//   // Managed by host: require_once of the bootstrap is below.
			// would get the define() spliced into the middle of that comment,
			// producing a wp-config.php that no longer parses. The site and
			// wp-admin both die at that point and the plugin cannot be
			// deactivated, because wp-config.php fails before WordPress loads.
			$anchor_offset = $this->find_token_offset(
				array( T_REQUIRE_ONCE, T_REQUIRE, T_INCLUDE_ONCE, T_INCLUDE )
			);

			// A caller-supplied anchor other than the default keeps the old
			// substring behaviour, since only the caller knows what it means.
			if ( 'require_once' !== $anchor ) {
				$anchor_offset = strpos( $this->wp_config_src, $anchor );
			}

			if ( false === $anchor_offset ) {
				// No safe insertion point. Do nothing rather than guess.
				return false;
			}

			$new_src  = $this->normalize( $type, $name, $this->format_value( $value, $raw ) ) . $separator;
			$contents = substr_replace( $this->wp_config_src, $new_src, $anchor_offset, 0 );
		}
		return $this->save( $contents );
	}

	/**
	 * Byte offset of the first occurrence of any of the given token types.
	 *
	 * token_get_all() reports line numbers, not offsets, so the offset is
	 * accumulated from the token texts — which concatenate back to the exact
	 * original source.
	 *
	 * @param int[] $token_types Token constants to look for.
	 * @return int|false Byte offset, or false when none is present.
	 */
	protected function find_token_offset( array $token_types ) {
		$tokens = @token_get_all( $this->wp_config_src );
		if ( ! is_array( $tokens ) ) {
			return false;
		}
		$offset = 0;
		foreach ( $tokens as $token ) {
			if ( is_array( $token ) ) {
				if ( in_array( $token[0], $token_types, true ) ) {
					return $offset;
				}
				$offset += strlen( $token[1] );
			} else {
				$offset += strlen( $token );
			}
		}
		return false;
	}
	public function update( $type, $name, $value, array $options = array() ) {
		if ( ! is_string( $value ) ) {
			throw new Exception( 'Config value must be a string.' );
		}
		$defaults = array(
			'add'       => true, 
			'raw'       => false, 
			'normalize' => false, 
		);
		list( $add, $raw, $normalize ) = array_values( array_merge( $defaults, $options ) );
		$add       = (bool) $add;
		$raw       = (bool) $raw;
		$normalize = (bool) $normalize;
		if ( ! $this->exists( $type, $name ) ) {
			return ( $add ) ? $this->add( $type, $name, $value, $options ) : false;
		}
		$old_src   = $this->wp_configs[ $type ][ $name ]['src'];
		$old_value = $this->wp_configs[ $type ][ $name ]['value'];
		$new_value = $this->format_value( $value, $raw );
		if ( $normalize ) {
			$new_src = $this->normalize( $type, $name, $new_value );
		} else {
			$new_parts    = $this->wp_configs[ $type ][ $name ]['parts'];
			$new_parts[1] = str_replace( $old_value, $new_value, $new_parts[1] ); 
			$new_src      = implode( '', $new_parts );
		}
		$contents = preg_replace(
			sprintf( '/(?<=^|;|<\?php\s|<\?\s)(\s*?)%s/m', preg_quote( trim( $old_src ), '/' ) ),
			'$1' . str_replace( '$', '\$', trim( $new_src ) ),
			$this->wp_config_src
		);

		return $this->save( $contents );
	}
	public function remove( $type, $name ) {
		if ( ! $this->exists( $type, $name ) ) {
			return false;
		}
		$pattern  = sprintf( '/(?<=^|;|<\?php\s|<\?\s)%s\s*(\S|$)/m', preg_quote( $this->wp_configs[ $type ][ $name ]['src'], '/' ) );
		$contents = preg_replace( $pattern, '$1', $this->wp_config_src );
		return $this->save( $contents );
	}
	protected function format_value( $value, $raw ) {
		if ( $raw && '' === trim( $value ) ) {
			throw new Exception( 'Raw value for empty string not supported.' );
		}
		return ( $raw ) ? $value : var_export( $value, true );
	}
	protected function normalize( $type, $name, $value ) {
		if ( 'constant' === $type ) {
			$placeholder = "define( '%s', %s );";
		} elseif ( 'variable' === $type ) {
			$placeholder = '$%s = %s;';
		} else {
			throw new Exception( "Unable to normalize config type '{$type}'." );
		}
		return sprintf( $placeholder, $name, $value );
	}
	protected function parse_wp_config( $src ) {
		$configs             = array();
		$configs['constant'] = array();
		$configs['variable'] = array();
		foreach ( token_get_all( $src ) as $token ) {
			if ( in_array( $token[0], array( T_COMMENT, T_DOC_COMMENT ), true ) ) {
				if ( '//' === $token[1] ) {
					$src = preg_replace( '/' . preg_quote( '//', '/' ) . '$/m', '', $src );
				} else {
					$src = str_replace( $token[1], '', $src );
				}
			}
		}
		preg_match_all( '/(?<=^|;|<\?php\s|<\?\s)(\h*define\s*\(\s*[\'"](\w*?)[\'"]\s*)(,\s*(\'\'|""|\'.*?[^\\\\]\'|".*?[^\\\\]"|.*?)\s*)((?:,\s*(?:true|false)\s*)?\)\s*;)/ims', $src, $constants );
		preg_match_all( '/(?<=^|;|<\?php\s|<\?\s)(\h*\$(\w+)\s*=)(\s*(\'\'|""|\'.*?[^\\\\]\'|".*?[^\\\\]"|.*?)\s*;)/ims', $src, $variables );
		if ( ! empty( $constants[0] ) && ! empty( $constants[1] ) && ! empty( $constants[2] ) && ! empty( $constants[3] ) && ! empty( $constants[4] ) && ! empty( $constants[5] ) ) {
			foreach ( $constants[2] as $index => $name ) {
				$configs['constant'][ $name ] = array(
					'src'   => $constants[0][ $index ],
					'value' => $constants[4][ $index ],
					'parts' => array(
						$constants[1][ $index ],
						$constants[3][ $index ],
						$constants[5][ $index ],
					),
				);
			}
		}
		if ( ! empty( $variables[0] ) && ! empty( $variables[1] ) && ! empty( $variables[2] ) && ! empty( $variables[3] ) && ! empty( $variables[4] ) ) {
			$variables[2] = array_reverse( array_unique( array_reverse( $variables[2], true ) ), true );
			foreach ( $variables[2] as $index => $name ) {
				$configs['variable'][ $name ] = array(
					'src'   => $variables[0][ $index ],
					'value' => $variables[4][ $index ],
					'parts' => array(
						$variables[1][ $index ],
						$variables[3][ $index ],
					),
				);
			}
		}
		return $configs;
	}
	protected function save( $contents ) {
		if ( ! trim( $contents ) ) {
			throw new Exception( 'Cannot save the config file with empty contents.' );
		}
		if ( $contents === $this->wp_config_src ) {
			return false;
		}

		// Never write a wp-config.php that does not parse. TOKEN_PARSE makes
		// token_get_all() run the real parser and raise ParseError on invalid
		// syntax, so this catches a bad edit before it can take the site down.
		try {
			token_get_all( $contents, TOKEN_PARSE );
		} catch ( \ParseError $e ) {
			throw new Exception( 'Refusing to write wp-config.php: the result would not parse (' . $e->getMessage() . ').' );
		}

		// Keep a copy of the last known-good file next to it, so a bad write
		// can be undone over FTP without needing the database or wp-admin.
		$backup = $this->wp_config_path . '.horsetools.bak';
		if ( ! file_exists( $backup ) ) {
			@file_put_contents( $backup, $this->wp_config_src, LOCK_EX );
		}

		$result = file_put_contents( $this->wp_config_path, $contents, LOCK_EX );

		if ( false === $result ) {
			throw new Exception( 'Failed to update the config file.' );
		}
		return true;
	}
}
}
// tao duong dan
class horsetools_get_config {
    public $config_path;
    public function __construct() {
        $this->config_path = ABSPATH . 'wp-config.php';
        if (!file_exists($this->config_path)) {
            if (@file_exists(dirname(ABSPATH) . '/wp-config.php') && !@file_exists(dirname(ABSPATH) . '/wp-settings.php')) {
                $this->config_path = dirname(ABSPATH) . '/wp-config.php';
            }
        }
    }
}
// Chi ghi wp-config.php khi admin thuc su thay doi cai dat (khong chay moi request)
function horsetools_apply_debug_constants() {
	global $horsetools_debug_options;

	// Chi chay trong admin, boi nguoi dung co quyen, va khong phai request AJAX/cron.
	if ( ! is_admin() || wp_doing_ajax() || wp_doing_cron() || ! current_user_can('manage_options') ) {
		return;
	}

	$desired = array(
		'WP_DEBUG'         => isset($horsetools_debug_options['debug1']) ? 'true' : 'false',
		'WP_DEBUG_LOG'     => isset($horsetools_debug_options['debug2']) ? 'true' : 'false',
		'WP_DEBUG_DISPLAY' => isset($horsetools_debug_options['debug3']) ? 'true' : 'false',
	);

	// Neu trang thai da duoc ap dung truoc do thi khong dung toi wp-config.php.
	$applied = get_option('horsetools_debug_applied');
	if ( is_array($applied) && $applied === $desired ) {
		return;
	}

	$wp_debug_toggle = new horsetools_get_config();
	if ( ! is_writable($wp_debug_toggle->config_path) ) {
		return;
	}

	try {
		$transformer = new horsetools_chandebug($wp_debug_toggle->config_path);
		foreach ( $desired as $constant_name => $constant_value ) {
			$transformer->update('constant', $constant_name, $constant_value, array('raw' => true));
		}
		update_option('horsetools_debug_applied', $desired);
	} catch ( Exception $e ) {
		return;
	}
}
add_action('admin_init', 'horsetools_apply_debug_constants');
// xoa debug log
function horsetools_clear_debug_log() {
	check_ajax_referer('horsetools_nonce_deldebug', 'security');
	if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'horse-tools'));
    }
    if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
        $debug_log_path = WP_CONTENT_DIR . '/debug.log';
        if (file_exists($debug_log_path)) {
            $result = file_put_contents($debug_log_path, '');
            if ($result !== false) {
                wp_send_json_success();
            } else {
                wp_send_json_error();
            }
        } else {
            wp_send_json_error();
        }
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_horsetools_clear_debug_log', 'horsetools_clear_debug_log');
// load debug
function horsetools_get_debug_log_callback() {
    check_ajax_referer('horsetools_nonce_getdebug', 'security');
    if (!current_user_can('manage_options')){
        wp_send_json_error('forbidden', 403);
    }
    $debug_log_path = WP_CONTENT_DIR . '/debug.log';
    // Kiểm tra xem tệp tồn tại không trước khi đọc nó
    if (file_exists($debug_log_path)) {
        $debug_log_content = file_get_contents($debug_log_path);
        if ($debug_log_content !== false) {
            wp_send_json_success(esc_html($debug_log_content));
        } else {
            wp_send_json_error(__('Failed to load debug log', 'horse-tools'));
        }
    } else {
        wp_send_json_error(__('Debug log file does not exist', 'horse-tools'));
    }

    wp_die();
}
add_action('wp_ajax_horsetools_get_debug_log', 'horsetools_get_debug_log_callback');




