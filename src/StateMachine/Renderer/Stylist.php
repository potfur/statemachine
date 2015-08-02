<?php

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Renderer;

use StateMachine\Exception\InvalidArgumentException;

/**
 * Styling class for state machine renderer
 *
 * @package StateMachine
 */
class Stylist
{
    const GLUE = '.';
    const WILDCARD = '*';

    /**
     * Output density
     *
     * @var int
     */
    private $dpi;

    /**
     * Font name that should be used for rendering
     * Depends on graphviz lib
     *
     * @var string
     */
    private $font;

    /**
     * Default styles
     *
     * @var array
     */
    private $defaultStyles = [
        'state' => ['text' => '#444444', 'color' => '#ebebeb', 'style' => 'ellipse', 'altColor' => '#0066aa'],
        'target' => ['text' => '#999999', 'color' => '#99BB11', 'style' => 'solid'],
        'error' => ['text' => '#999999', 'color' => '#ee1155', 'style' => 'solid']
    ];

    /**
     * Custom styles
     *
     * @var array
     */
    private $customStyles = [
        '*.onStateWasSet' => ['style' => 'bold'],
        '*.onTimeout' => ['style' => 'dashed'],
    ];

    /**
     * Constructor
     *
     * @param int    $dpi    render density
     * @param string $font   font family used for text
     * @param array  $styles array containing custom styles
     */
    public function __construct($dpi = 75, $font = 'Courier', array $styles = [])
    {
        $this->dpi = (int) $dpi;
        $this->font = $font;

        $this->setStyles($styles);
    }

    /**
     * Return DPI
     *
     * @return int
     */
    public function getDPI()
    {
        return $this->dpi;
    }

    /**
     * Return font name
     *
     * @return string
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * Set styles used for states and edges by their name
     * States are named by their name, edges/events by state.name
     * Wildcards are supported
     *     *.onTimeout - all onTimeout events will have such style
     *     New.*       - all events in new will have such style
     * Each element of passed must contain at least key:
     *     text     - color used for elements label,
     *     color    - primary color used in element,
     *     altColor - alternative color for additional info
     *     style    - shape/border/edge style
     * Styles are merged with default value
     *
     * @param array $styles
     */
    public function setStyles(array $styles)
    {
        foreach ($styles as $style) {
            foreach (['text', 'color', 'altColor'] as $key) {
                if (isset($style[$key])) {
                    $this->assertColor($style[$key]);
                }
            }
        }

        $this->customStyles = array_merge($this->customStyles, $styles);
    }

    /**
     * Throw exception if color is invalid
     *
     * @param string $color
     *
     * @throws InvalidArgumentException
     */
    private function assertColor($color)
    {
        if (!preg_match('/^#[0-9a-f]{6}$/i', $color)) {
            throw new InvalidArgumentException('Invalid color provided, must be valid 6 character HTML color code.');
        }
    }

    /**
     * Return array with styles used for states and edges
     *
     * @return array
     */
    public function getStyles()
    {
        return $this->customStyles;
    }

    /**
     * Return style for element
     *
     * @param string $type
     * @param string $state
     * @param string $event
     *
     * @return Style
     */
    public function getStyle($type, $state = null, $event = null)
    {
        $keys = [
            implode(self::GLUE, [$state, $event]),
            implode(self::GLUE, [$state, self::WILDCARD]),
            implode(self::GLUE, [self::WILDCARD, $event]),
            $type
        ];

        foreach ($keys as $key) {
            if (isset($this->customStyles[$key])) {
                return $this->toValueObject(array_merge($this->defaultStyles[$type], $this->customStyles[$key]));
            }
        }

        return $this->toValueObject($this->defaultStyles[$type]);
    }

    /**
     * Converts array style into value object
     *
     * @param array $style
     *
     * @return Style
     */
    private function toValueObject(array $style)
    {
        $style = array_merge(['text' => null, 'color' => null, 'style' => null, 'altColor' => null], $style);

        return new Style(
            $style['text'],
            $style['color'],
            $style['style'],
            $style['altColor']
        );
    }
}
