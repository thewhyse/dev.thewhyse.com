<?php

use ScssPhp\ScssPhp\Exception\SassException;
use ScssPhp\ScssPhp\ValueConverter;

class ameAdminColorScheme {
	/**
	 * @var array
	 */
	private $colors;
	private $hash = null;
	private $cachedIconColor = null;

	public function __construct($colors = array()) {
		reset($colors);
		$firstIndex = key($colors);
		if ( is_int($firstIndex) ) {
			$colors = self::colorListToAssocArray($colors);
		}

		//Discard empty strings. This can happen during live preview because blank
		//settings are not automatically removed in that context.
		$colors = array_filter($colors, function($value) {
			return ($value !== '');
		});

		$this->colors = array_intersect_key($colors, self::getAvailableOptions());
	}

	public function getColors() {
		return $this->colors;
	}

	protected function getNonEmptyColors() {
		return array_filter($this->colors);
	}

	public function hasCustomColors() {
		foreach ($this->colors as $color) {
			if ( !empty($color) ) {
				return true;
			}
		}
		return false;
	}

	public static function getAvailableOptions() {
		return array(
			'base-color'         => 'Base',
			'text-color'         => 'Text',
			'highlight-color'    => 'Highlight',
			'icon-color'         => 'Icon',
			'notification-color' => 'Notification',

			//General UI
			'body-background'    => 'Page background',

			'link'       => 'Link',
			'link-focus' => 'Link hover',

			'button-color'    => 'Button',
			'form-checked'    => 'Check mark (✓)',

			//Admin menu & Toolbar
			'menu-text'       => 'Menu text',
			'menu-icon'       => 'Menu icon',
			'menu-background' => 'Menu background',

			'menu-highlight-text'       => 'Menu highlight text',
			'menu-highlight-icon'       => 'Menu highlight icon',
			'menu-highlight-background' => 'Menu highlight background',

			'menu-current-text'       => 'Menu current text',
			'menu-current-icon'       => 'Menu current icon',
			'menu-current-background' => 'Menu current background',

			'menu-submenu-text'           => 'Submenu text',
			'menu-submenu-background'     => 'Submenu background',
			'menu-submenu-background-alt' => 'Submenu background (alt.)',

			'menu-submenu-focus-text'   => 'Submenu highlight text',
			'menu-submenu-current-text' => 'Submenu current text',

			'menu-bubble-text'               => 'Bubble text',
			'menu-bubble-background'         => 'Bubble background',
			'menu-bubble-current-text'       => 'Bubble current text',
			'menu-bubble-current-background' => 'Bubble current background',

			'menu-collapse-text' => 'Menu collapse button text',
			/*
			 //These three variables appear to be unused, at least in WP 4.9.4.
			'menu-collapse-icon'       => 'Menu collapse button icon',
			'menu-collapse-focus-text' => 'Menu collapse highlight text',
			'menu-collapse-focus-icon' => 'Menu collapse highlight icon',
			//*/

			'adminbar-avatar-frame'     => 'Toolbar avatar border',
			'adminbar-input-background' => 'Toolbar search box background',
		);
	}

	/**
	 * @return array
	 */
	public static function getColorListOrder() {
		return array_keys(self::getAvailableOptions());
	}

	/**
	 * @param array $colorList
	 * @return array
	 */
	private static function colorListToAssocArray($colorList) {
		$colors = array();
		foreach (self::getColorListOrder() as $index => $name) {
			if ( isset($colorList[$index]) && self::isValidHexColor('#' . $colorList[$index]) ) {
				$colors[$name] = '#' . $colorList[$index];
			}
		}
		return $colors;
	}

	/**
	 * Check if the input is a CSS color in hexadecimal format, e.g. #001122.
	 *
	 * @param string $value
	 * @return bool
	 */
	public static function isValidHexColor($value) {
		return is_string($value) && preg_match('@^#[a-f\d]{3,6}$@', $value);
	}

	/**
	 * @throws \ScssPhp\ScssPhp\Exception\SassException
	 */
	public function compileToCss() {
		return self::compileScssToCss(
			$this->getNonEmptyColors(),
			array($this->getAdminDir() . '/css/colors'),
			'@import "_admin.scss";'
		);
	}

	/**
	 * Compile only the part of the admin color scheme which pertains to the Admin Bar/Toolbar.
	 *
	 * @return string
	 * @throws \ScssPhp\ScssPhp\Exception\SassException
	 */
	public function compileAdminBarStylesToCss() {
		$adminScssPath = $this->getAdminDir() . '/css/colors/_admin.scss';
		if ( !is_file($adminScssPath) ) {
			return '/* Error: _admin.scss not found. */';
		}

		//Find the Admin Bar section in the SCSS file.
		$sheet = file_get_contents($adminScssPath);
		if ( !preg_match('@^\s??/\*\s+?Admin Bar\s+?\*/\s{0,20}?$@mi', $sheet, $matches, PREG_OFFSET_CAPTURE) ) {
			return '/* Error: "Admin Bar" section not found in the stylesheet. */';
		}
		$startPosition = $matches[0][1];
		$firstHeadingLength = strlen($matches[0][0]);

		//To find the end of admin bar styles, look for the first section that doesn't mention "admin bar".
		$endPosition = null;
		if ( preg_match_all(
			'@^\s??/\*\s+?[^{}*\r\n#]+?\s+?\*/\s{0,20}?$@m',
			$sheet,
			$headings,
			PREG_OFFSET_CAPTURE | PREG_SET_ORDER,
			$startPosition + $firstHeadingLength
		) ) {
			foreach ($headings as $heading) {
				if ( stripos($heading[0][0], 'admin bar') === false ) {
					$endPosition = $heading[0][1];
					break;
				}
			}
		}
		if ( $endPosition === null ) {
			return '/* Error: Could not find the end of the "Admin Bar" section in the stylesheet. */';
		}

		$adminBarStyles = substr($sheet, $startPosition, $endPosition - $startPosition);

		$code = '@import "_variables.scss";' . "\n";
		$code .= $adminBarStyles;

		return self::compileScssToCss(
			$this->getNonEmptyColors(),
			array($this->getAdminDir() . '/css/colors'),
			$code
		);
	}

	/**
	 * Compile SCSS to CSS.
	 *
	 * @param array $variables
	 * @param string[] $importPaths
	 * @param string $scssCode
	 * @return string
	 * @throws \ScssPhp\ScssPhp\Exception\SassException
	 */
	private static function compileScssToCss($variables, $importPaths, $scssCode) {
		$scss = new ScssPhp\ScssPhp\Compiler();

		if ( !empty($variables) ) {
			if ( method_exists($scss, 'addVariables') ) {
				//ScsPhp will trigger a silenced notice if the variables
				//are not converted to Sass values.
				if ( is_callable(array(ValueConverter::class, 'parseValue')) ) {
					$variables = array_map(function ($value) {
						//This is inefficient because it creates a new parser each
						//time. Unfortunately, the parser class is marked as internal.
						return ValueConverter::parseValue($value);
					}, $variables);
				}
				$scss->addVariables($variables);
			} else {
				/** @noinspection PhpDeprecationInspection Backwards compatibility. */
				$scss->setVariables($variables);
			}
		}

		foreach ($importPaths as $importPath) {
			$scss->addImportPath($importPath);
		}

		if ( method_exists($scss, 'compileString') ) {
			$result = $scss->compileString($scssCode);
			return $result->getCss();
		} else {
			/** @noinspection PhpDeprecationInspection Backwards compatibility. */
			return $scss->compile($scssCode);
		}
	}

	private function getAdminDir() {
		return ABSPATH . '/wp-admin';
	}

	public function getDemoColors() {
		return array(
			$this->getEffectiveBaseColor(),
			$this->getIconColor(),
			ameUtils::get($this->colors, 'notification-color', '#d54e21'),
			ameUtils::get($this->colors, 'highlight-color', '#0073aa'),
		);
	}

	public function getSvgIconColors() {
		$icons = array(
			'base'  => $this->getIconColor(),
			'focus' => ameUtils::get($this->colors, 'menu-highlight-icon', '#fff'),
		);
		$icons['current'] = $icons['focus'];
		return $icons;
	}

	protected function getIconColor() {
		$customColor = ameUtils::get($this->colors, 'icon-color');
		if ( !empty($customColor) ) {
			return $customColor;
		}
		if ( !empty($this->cachedIconColor) ) {
			return $this->cachedIconColor;
		}

		//WordPress computes the default icon color based on the base color, and
		//we'll do the same. Using an entire SCSS compiler just to compute a single
		//color is a bit overkill, but the result should usually be cached.
		$baseColor = $this->getEffectiveBaseColor();
		$scss = '
			$base-color: ' . $baseColor . '; 
			$icon-color: hsl( hue( $base-color ), 7%%, 95%% );
			#output {
				color: $icon-color;
			}';

		$result = $baseColor;
		try {
			$css = self::compileScssToCss(['base-color' => $baseColor], [], $scss);
			if ( preg_match('@#output\s*\{[^}a-z]*color:\s*([^;}\r\n]+)[};]@i', $css, $matches) ) {
				$result = trim($matches[1]);
			}
		} catch (SassException $e) {
			//Ignored. We'll just fall back to the base color.
		}

		$this->cachedIconColor = $result;
		return $result;
	}

	protected function getEffectiveBaseColor() {
		$color = ameUtils::get($this->colors, 'base-color');
		if ( !empty($color) ) {
			return $color;
		}
		return '#23282d';
	}

	public function getHash() {
		if ( !isset($this->hash) ) {
			$this->hash = substr(md5(build_query($this->colors)), 0, 16);
		}
		return $this->hash;
	}

	public function toArray() {
		return [
			'colors'          => $this->colors,
			'hash'            => $this->getHash(),
			'cachedIconColor' => $this->getIconColor(),
		];
	}

	public static function fromArray($properties) {
		$scheme = new self(ameUtils::get($properties, 'colors', []));
		/** @noinspection PhpRedundantOptionalArgumentInspection Hash must be NULL if not set */
		$scheme->hash = ameUtils::get($properties, 'hash', null);
		/** @noinspection PhpRedundantOptionalArgumentInspection */
		$scheme->cachedIconColor = ameUtils::get($properties, 'cachedIconColor', null);
		return $scheme;
	}
}