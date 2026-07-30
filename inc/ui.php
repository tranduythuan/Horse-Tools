<?php
/**
 * Horse Tools — admin field components.
 *
 * Every control used to be hand-written like this, ~187 times:
 *
 *     <label class="nut-switch">
 *       <input type="checkbox" name="horsetools_settings[speed-off1]" value="1" ... />
 *       <span class="slider"></span></label>
 *     <label class="ht-label-right">Disable jQuery Migrate</label>
 *
 * The input is wrapped by a label containing only a decorative span, and the
 * visible text sits in a second label with no `for` and no wrapped control. So
 * the accessible name of every toggle was empty — a screen reader announced
 * "checkbox, unchecked" 187 times — and clicking the text did nothing, because
 * a label with neither `for` nor a wrapped control is inert markup.
 *
 * These helpers emit the same visual result with a real label association, a
 * description tied by aria-describedby, and dependency wiring that sets the
 * genuine `disabled` attribute rather than pointer-events:none (which blocks
 * the mouse but not the keyboard, so "disabled" settings were still saved).
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * A stable DOM id for a field.
 *
 * @param string $module
 * @param string $key
 * @return string
 */
function horsetools_field_id( $module, $key ) {
	return 'ht-' . sanitize_html_class( $module ) . '-' . sanitize_html_class( $key );
}

/**
 * Render an on/off switch.
 *
 * @param string $key  Setting key.
 * @param string $label Visible label.
 * @param array  $args {
 *     @type string $module      Module key. Default 'main'.
 *     @type string $description Help text, rendered as an ht-note and linked
 *                               with aria-describedby.
 *     @type string $parent      Key of the toggle this one depends on. When the
 *                               parent is off this control is really disabled.
 *     @type string $tab         Tab id, for the search panel.
 *     @type string $section     Section heading, for the search panel.
 *     @type string $warning     Shown in red; use for anything destructive.
 *     @type string $id          Override the generated DOM id. Only for
 *                               controls that existing JavaScript selects by a
 *                               hand-assigned id (see main/extend.php, where
 *                               searchToggle() matches input[id^="more"]).
 *     @type string $class       Extra class on the input, for the same reason.
 *     @type bool   $bare        Emit only the switch and label, without the
 *                               wrapping <p class="ht-field">. Use when the
 *                               control sits inside a layout that supplies its
 *                               own container.
 * }
 */
function horsetools_toggle( $key, $label, array $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'module'      => 'main',
			'description' => '',
			'parent'      => '',
			'tab'         => '',
			'section'     => '',
			'warning'     => '',
			'id'          => '',
			'class'       => '',
			'bare'        => false,
		)
	);

	$map = horsetools_module_map();
	if ( ! isset( $map[ $args['module'] ] ) ) {
		return;
	}

	$id      = ( '' !== $args['id'] ) ? $args['id'] : horsetools_field_id( $args['module'], $key );
	$name    = $map[ $args['module'] ] . '[' . $key . ']';
	$value   = horsetools_opt( $args['module'], $key );
	$note_id = $id . '-note';
	$has_note = ( '' !== $args['description'] || '' !== $args['warning'] );

	horsetools_register_field(
		array(
			'key'     => $key,
			'module'  => $args['module'],
			'label'   => $label,
			'tab'     => $args['tab'],
			'section' => $args['section'],
			'type'    => 'toggle',
		)
	);

	$parent_attr = '';
	if ( '' !== $args['parent'] ) {
		$parent_attr = ' data-ht-parent="' . esc_attr( horsetools_field_id( $args['module'], $args['parent'] ) ) . '"';
	}
	if ( ! $args['bare'] ) {
		echo '<p class="ht-field"' . $parent_attr . '>'; // phpcs:ignore WordPress.Security.EscapeOutput -- built from esc_attr above
	}
	?>
		<label class="nut-switch">
			<input type="checkbox"
				id="<?php echo esc_attr( $id ); ?>"
				<?php echo '' !== $args['class'] ? ' class="' . esc_attr( $args['class'] ) . '"' : ''; ?>
				name="<?php echo esc_attr( $name ); ?>"
				value="1"
				<?php checked( 1, (int) $value ); ?>
				<?php echo $has_note ? ' aria-describedby="' . esc_attr( $note_id ) . '"' : ''; ?> />
			<span class="slider" aria-hidden="true"></span>
		</label>
		<label class="ht-label-right" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
	<?php
	if ( ! $args['bare'] ) {
		echo '</p>';
	}
	if ( $has_note ) {
		$classes = 'ht-note' . ( '' !== $args['warning'] ? ' ht-note-red' : '' );
		echo '<p class="' . esc_attr( $classes ) . '" id="' . esc_attr( $note_id ) . '">';
		echo '<i class="ti ti-bulb" aria-hidden="true"></i> ';
		if ( '' !== $args['description'] ) {
			echo esc_html( $args['description'] );
		}
		if ( '' !== $args['warning'] ) {
			echo ( '' !== $args['description'] ? '<br>' : '' );
			echo '<strong>' . esc_html( $args['warning'] ) . '</strong>';
		}
		echo '</p>';
	}
}

/**
 * Render a text / number / colour input with a real label.
 *
 * @param string $key
 * @param string $label
 * @param array  $args See horsetools_toggle(), plus 'type', 'placeholder',
 *                     'min', 'max', 'step', 'class'.
 */
function horsetools_input( $key, $label, array $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'module'      => 'main',
			'type'        => 'text',
			'description' => '',
			'placeholder' => '',
			'parent'      => '',
			'tab'         => '',
			'section'     => '',
			'class'       => 'ht-input-big',
			'min'         => null,
			'max'         => null,
			'step'        => null,
		)
	);

	$map = horsetools_module_map();
	if ( ! isset( $map[ $args['module'] ] ) ) {
		return;
	}

	$id      = horsetools_field_id( $args['module'], $key );
	$name    = $map[ $args['module'] ] . '[' . $key . ']';
	$value   = horsetools_opt( $args['module'], $key, '' );
	$note_id = $id . '-note';

	horsetools_register_field(
		array(
			'key'     => $key,
			'module'  => $args['module'],
			'label'   => $label,
			'tab'     => $args['tab'],
			'section' => $args['section'],
			'type'    => $args['type'],
		)
	);

	$parent_attr = '';
	if ( '' !== $args['parent'] ) {
		$parent_attr = ' data-ht-parent="' . esc_attr( horsetools_field_id( $args['module'], $args['parent'] ) ) . '"';
	}
	?>
	<p class="ht-field"<?php echo $parent_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- built from esc_attr above ?>>
		<label class="ht-field-label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<input
			id="<?php echo esc_attr( $id ); ?>"
			class="<?php echo esc_attr( $args['class'] ); ?>"
			type="<?php echo esc_attr( $args['type'] ); ?>"
			name="<?php echo esc_attr( $name ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			<?php echo '' !== $args['placeholder'] ? ' placeholder="' . esc_attr( $args['placeholder'] ) . '"' : ''; ?>
			<?php echo null !== $args['min'] ? ' min="' . esc_attr( $args['min'] ) . '"' : ''; ?>
			<?php echo null !== $args['max'] ? ' max="' . esc_attr( $args['max'] ) . '"' : ''; ?>
			<?php echo null !== $args['step'] ? ' step="' . esc_attr( $args['step'] ) . '"' : ''; ?>
			<?php echo '' !== $args['description'] ? ' aria-describedby="' . esc_attr( $note_id ) . '"' : ''; ?> />
	</p>
	<?php
	if ( '' !== $args['description'] ) {
		echo '<p class="ht-note" id="' . esc_attr( $note_id ) . '"><i class="ti ti-bulb" aria-hidden="true"></i> '
			. esc_html( $args['description'] ) . '</p>';
	}
}

/**
 * Hand the registry to the admin JavaScript.
 *
 * This has to run on admin_footer, not admin_enqueue_scripts: the registry is
 * filled as the screen renders its fields, which happens after enqueue. At
 * enqueue time it would always be empty.
 */
function horsetools_localise_registry() {
	if ( ! current_user_can( 'manage_options' ) || ! horsetools_is_plugin_screen() ) {
		return;
	}

	$fields = array();
	foreach ( horsetools_get_field_registry() as $id => $field ) {
		$fields[] = array(
			'id'      => horsetools_field_id( $field['module'], $field['key'] ),
			'key'     => $field['key'],
			'label'   => $field['label'],
			'tab'     => $field['tab'],
			'section' => $field['section'],
			'page'    => $field['page'],
		);
	}
	$active = array();
	foreach ( horsetools_get_active_fields() as $field ) {
		$active[] = horsetools_field_id( $field['module'], $field['key'] );
	}

	$payload = array(
		'fields' => $fields,
		'active' => $active,
		'i18n'   => array(
			'search'      => __( 'Search settings…', 'horse-tools' ),
			'noResults'   => __( 'No setting matches that.', 'horse-tools' ),
			'activeTitle' => __( 'Currently enabled', 'horse-tools' ),
			'noneActive'  => __( 'Nothing on this page is enabled yet.', 'horse-tools' ),
			/* translators: %d: number of settings. */
			'activeCount' => __( '%d enabled', 'horse-tools' ),
			'unsaved'     => __( 'You have unsaved changes.', 'horse-tools' ),
			'save'        => __( 'Save changes', 'horse-tools' ),
			'discard'     => __( 'Discard', 'horse-tools' ),
		),
	);

	printf(
		'<script id="horsetools-registry">window.horsetoolsRegistry = %s;</script>',
		wp_json_encode( $payload )
	);
}
add_action( 'admin_footer', 'horsetools_localise_registry', 5 );
