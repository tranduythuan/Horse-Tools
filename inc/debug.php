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

		// This used to keep a copy of the whole file as wp-config.php.horsetools.bak,
		// next to wp-config.php, so a bad write could be undone over FTP. The
		// intent was right and the filename was a hole: .bak is not a PHP
		// extension, so a web server hands that file over as plain text — the
		// database password, every salt, the table prefix, to anyone who guesses
		// the name. An .htaccess would not have covered nginx.
		//
		// The backup existed to protect against a half-written file. Writing to a
		// temporary file and renaming it into place protects against that
		// properly: rename() within one directory is atomic, so wp-config.php is
		// either the old file or the new one and never something in between.
		// Nothing containing a credential is left lying around.
		$dir = dirname( $this->wp_config_path );
		$tmp = $dir . '/.ht-cfg-' . bin2hex( random_bytes( 6 ) ) . '.tmp';

		if ( false === @file_put_contents( $tmp, $contents, LOCK_EX ) ) {
			@unlink( $tmp );
			throw new Exception( 'Failed to update the config file.' );
		}
		// Carry the original file's mode over, and keep the temporary file
		// unreadable to anyone else for the moment it exists.
		@chmod( $tmp, 0600 );
		$mode = @fileperms( $this->wp_config_path );

		if ( @rename( $tmp, $this->wp_config_path ) ) {
			if ( $mode ) {
				@chmod( $this->wp_config_path, $mode & 0777 );
			}
			return true;
		}

		// rename() over an existing file fails on Windows. Fall back to the
		// direct write — the contents have already been parsed, so the only risk
		// left is the machine dying mid-write, which is where we started.
		@unlink( $tmp );
		if ( false === file_put_contents( $this->wp_config_path, $contents, LOCK_EX ) ) {
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
/**
 * Where the debug log should be written.
 *
 * Not wp-content/debug.log. That is WordPress' default, it is inside the web
 * root, and it is served to anyone who asks for it — PHP errors carry absolute
 * server paths, fragments of SQL, and sometimes whatever was in the request
 * that failed. Switching logging on should not publish that.
 *
 * WordPress has accepted a path for WP_DEBUG_LOG since 5.1, so the log goes in
 * a folder of its own with an unguessable file name. The folder gets the usual
 * index.php and an .htaccess for Apache; the random name is what covers nginx,
 * where a plugin cannot write server config at all.
 *
 * @return string Absolute path.
 */
function horsetools_debug_log_path() {
	$name = get_option( 'horsetools_debug_log_name' );
	if ( ! is_string( $name ) || ! preg_match( '/^debug-[a-f0-9]{16}\.log$/', $name ) ) {
		$name = 'debug-' . bin2hex( random_bytes( 8 ) ) . '.log';
		update_option( 'horsetools_debug_log_name', $name, false );
	}

	$dir = WP_CONTENT_DIR . '/horsetools-logs';
	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
	}
	if ( is_dir( $dir ) ) {
		if ( ! file_exists( $dir . '/index.php' ) ) {
			@file_put_contents( $dir . '/index.php', "<?php\n// Silence is golden.\n" );
		}
		if ( ! file_exists( $dir . '/.htaccess' ) ) {
			// Both spellings, each behind its own guard. An unguarded "Require"
			// is a 500 on Apache 2.2, and an unguarded "Deny" is deprecated on 2.4.
			@file_put_contents(
				$dir . '/.htaccess',
				"<IfModule mod_authz_core.c>\n\tRequire all denied\n</IfModule>\n"
				. "<IfModule !mod_authz_core.c>\n\tOrder allow,deny\n\tDeny from all\n</IfModule>\n"
			);
		}
	}

	// Whatever WordPress already wrote to the public default is the same log,
	// and it is readable by anyone right now. Move it rather than leave it, and
	// rather than delete it — it is the owner's data and they may be mid-debug.
	$public = WP_CONTENT_DIR . '/debug.log';
	if ( is_dir( $dir ) && file_exists( $public ) && ! file_exists( $dir . '/' . $name ) ) {
		@rename( $public, $dir . '/' . $name );
	}

	return $dir . '/' . $name;
}

/**
 * The log actually in use — what wp-config says, not what we would choose.
 *
 * Reading the constant rather than recomputing means the viewer and the clear
 * button still work on a site that was set up before this changed, or where the
 * owner pointed WP_DEBUG_LOG somewhere themselves.
 *
 * @return string
 */
function horsetools_debug_log_current() {
	if ( defined( 'WP_DEBUG_LOG' ) && is_string( WP_DEBUG_LOG ) && '' !== WP_DEBUG_LOG ) {
		return WP_DEBUG_LOG;
	}
	return WP_CONTENT_DIR . '/debug.log';
}

function horsetools_apply_debug_constants() {
	global $horsetools_debug_options;

	// Chi chay trong admin, boi nguoi dung co quyen, va khong phai request AJAX/cron.
	if ( ! is_admin() || wp_doing_ajax() || wp_doing_cron() || ! current_user_can('manage_options') ) {
		return;
	}

	// var_export() so the path becomes a properly quoted PHP literal — the
	// transformer writes the value verbatim ('raw' => true).
	$log = isset($horsetools_debug_options['debug2'])
		? var_export( horsetools_debug_log_path(), true )
		: 'false';

	$desired = array(
		'WP_DEBUG'         => isset($horsetools_debug_options['debug1']) ? 'true' : 'false',
		'WP_DEBUG_LOG'     => $log,
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
			// 'raw' means the value goes into wp-config.php verbatim, so the
			// only safe values are the ones this function builds: a boolean
			// literal or a single-quoted path. The parse check inside save()
			// is not enough on its own — "true ) ; foo ( bar" is valid PHP and
			// would take the site down at run time rather than at parse time.
			if ( ! preg_match( "/^(?:true|false|'[^'\\\\]*')$/", $constant_value ) ) {
				continue;
			}
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
        $debug_log_path = horsetools_debug_log_current();
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
    $debug_log_path = horsetools_debug_log_current();
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




