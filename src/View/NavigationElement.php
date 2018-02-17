<?php declare(strict_types=1);

namespace Dms\Web\Expressive\View;

use Zend\Expressive\Router\RouterInterface;

/**
 * The navigation element
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class NavigationElement
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $routeName;

    /**
     * @var array
     */
    protected $routeParams;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string[]
     */
    protected $requiredPermissions;

    /**
     * @var bool
     */
    protected $requiresAnyFromGroups;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * NavigationElement constructor.
     *
     * @param string          $label
     * @param string          $routeName
     * @param array           $routeParams
     * @param string          $icon
     * @param RouterInterface $router
     * @param array           $requiredPermissionNames
     * @param boolean         $requiresAnyFromGroups
     */
    public function __construct(
        string $label,
        string $routeName,
        array $routeParams,
        string $icon,
        RouterInterface $router,
        array $requiredPermissionNames = [],
        bool $requiresAnyFromGroups = false
    ) {
        $this->label                 = $label;
        $this->routeName             = $routeName;
        $this->routeParams           = $routeParams;
        $this->icon                  = $icon;
        $this->requiredPermissions   = $requiredPermissionNames;
        $this->requiresAnyFromGroups = $requiresAnyFromGroups;
        $this->router = $router;
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
     * @return string
     */
    public function getUrl() : string
    {
        return $this->router->generateUri($this->routeName, $this->routeParams);
    }

    /**
     * @param array $usersPermissionNames
     *
     * @return bool
     */
    public function shouldDisplay(array $usersPermissionNames) : bool
    {
        if ($this->requiresAnyFromGroups) {
            foreach ($this->requiredPermissions as $group) {
                if (count(array_diff($group, $usersPermissionNames)) === 0) {
                    return true;
                }
            }

            return false;
        } else {
            return count(array_diff($this->requiredPermissions, $usersPermissionNames)) === 0;
        }
    }
}
