<?php declare(strict_types=1);

namespace Dms\Web\Expressive\View;

use Dms\Core\Exception\InvalidArgumentException;

/**
 * The navigation element group.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class NavigationElementGroup
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var NavigationElement[]
     */
    protected $elements;

    /**
     * NavigationElementGroup constructor.
     *
     * @param string              $label
     * @param string              $icon
     * @param NavigationElement[] $elements
     */
    public function __construct(string $label, string $icon, array $elements)
    {
        InvalidArgumentException::verifyAllInstanceOf(__METHOD__, 'elements', $elements, NavigationElement::class);

        $this->label = $label;
        $this->icon  = $icon;

        $this->elements = $elements;
    }

    /**
     * @return string
     */
    public function getLabel() : string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getIcon() : string
    {
        return $this->icon;
    }

    /**
     * @return NavigationElement[]
     */
    public function getElements() : array
    {
        return $this->elements;
    }

    /**
     * @return string[]
     */
    public function getAllUrls() : array
    {
        $urls = [];

        foreach ($this->elements as $element) {
            $urls[] = $element->getUrl();
        }

        return $urls;
    }

    /**
     * @param array $usersPermissionNames
     *
     * @return bool
     */
    public function shouldDisplay(array $usersPermissionNames) : bool
    {
        foreach ($this->elements as $element) {
            if (!$element->shouldDisplay($usersPermissionNames)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param NavigationElement[] $subNavigation
     *
     * @return NavigationElementGroup
     */
    public function withElements(array $subNavigation) : self
    {
        return new self($this->label, $this->icon, $subNavigation);
    }
}
