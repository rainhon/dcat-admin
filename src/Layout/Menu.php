<?php

namespace Dcat\Admin\Layout;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Facades\Lang;

class Menu
{
    protected static $helperNodes = [
        [
            'id'        => 999005,
            'title'     => '菜单',
            'icon'      => '',
            'uri'       => 'auth/menu',
            'parent_id' => 1,
        ],
        [
            'id'        => 999004,
            'title'     => '权限',
            'icon'      => '',
            'uri'       => 'auth/permissions',
            'parent_id' => 1,
        ],
        [
            'id'        => 1,
            'title'     => 'Helpers',
            'icon'      => 'fa fa-keyboard-o',
            'uri'       => 'auth/menu',
            'parent_id' => 0,
        ],
        [
            'id'        => 999001,
            'title'     => 'Extensions',
            'icon'      => '',
            'uri'       => 'auth/extensions',
            'parent_id' => 1,
        ],
        [
            'id'        => 999002,
            'title'     => 'Scaffold',
            'icon'      => '',
            'uri'       => 'helpers/scaffold',
            'parent_id' => 1,
        ],
        [
            'id'        => 999003,
            'title'     => 'Icons',
            'icon'      => '',
            'uri'       => 'helpers/icons',
            'parent_id' => 1,
        ],

    ];

    protected $view = 'admin::partials.menu';

    protected $sidebarMenuView = 'admin::partials.sidebar-menu';
    protected $navbarMenuView = 'admin::partials.navbar-menu';

    protected $sidebarMenuHtml = '';
    protected $navbarMenuHtml = '';

    public function register()
    {
        $menuModel = config('admin.database.menu_model');
        $nodes = (new $menuModel())->allNodes()->toArray();
        if (config('app.debug') && config('admin.helpers.enable', true)) {
            $nodes = array_merge($nodes, static::$helperNodes);
        }
        $this->toHtml($nodes);
        if (! admin_has_default_section(Admin::SECTION['LEFT_SIDEBAR_MENU'])) {
            admin_inject_default_section(Admin::SECTION['LEFT_SIDEBAR_MENU'], function () {
                return $this->sidebarMenuHtml;
            });
        }

        if(! admin_has_default_section(Admin::SECTION['NAVBAR_MENU'])) {
            admin_inject_default_section(Admin::SECTION['NAVBAR_MENU'], function () {
                return $this->navbarMenuHtml;
            });
        }


    }

    /**
     * 增加菜单节点.
     *
     * @param  array  $nodes
     * @param  int  $priority
     * @return void
     */
    public function add(array $nodes = [], int $priority = 10)
    {
        admin_inject_section(Admin::SECTION['LEFT_SIDEBAR_MENU'], function () use (&$nodes) {
            return $this->toHtml($nodes);
        }, true, $priority);
    }

    /**
     * 转化为HTML.
     *
     * @param  array  $nodes
     * @return string
     *
     * @throws \Throwable
     */
    // public function toHtml($nodes)
    // {
    //     $html = '';

    //     foreach (Helper::buildNestedArray($nodes) as $item) {
    //         $html .= $this->render($item);
    //     }

    //     return $html;
    // }

    public function toHtml($nodes) {
        $nodes = Helper::buildNestedArray($nodes);
        foreach ($nodes as &$item) {
            $this->navbarMenuHtml .= view($this->navbarMenuView, ['item' => $item, 'builder' => $this])->render();
            $this->sidebarMenuHtml .= view($this->sidebarMenuView, ['items' => $item['children'] ?? [], 'parentItem' => &$item, 'builder' => $this])->render();
        }
    }

    /**
     * 设置菜单视图.
     *
     * @param  string  $view
     * @return $this
     */
    public function view(string $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * 渲染视图.
     *
     * @param  array  $item
     * @return string
     */
    public function render($item)
    {
        return view($this->view, ['item' => &$item, 'builder' => $this])->render();
    }

    /**
     * 判断是否选中.
     *
     * @param  array  $item
     * @param  null|string  $path
     * @return bool
     */
    public function isActive($item, ?string $path = null)
    {
        if (empty($path)) {
            $path = trim(request()->getRequestUri(), '/');
        }   

        if (!empty($item['uri'])) {
            if($this->clearPath($item['uri']) == $path) {
                return true;
            }
        }

        if(!empty($item['children'])) {
            foreach ($item['children'] as $v) {
                if ($path == $this->clearPath($v['uri'])) {
                    return true;
                }
                if (! empty($v['children'])) {
                    if ($this->isActive($v, $path)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * 判断节点是否可见.
     *
     * @param  array  $item
     * @return bool
     */
    public function visible($item)
    {
        if (
            ! $this->checkPermission($item)
            || ! $this->checkExtension($item)
            || ! $this->userCanSeeMenu($item)
        ) {
            return false;
        }

        $show = $item['show'] ?? null;
        if ($show !== null && ! $show) {
            return false;
        }

        return true;
    }

    /**
     * 判断扩展是否启用.
     *
     * @param $item
     * @return bool
     */
    protected function checkExtension($item)
    {
        $extension = $item['extension'] ?? null;

        if (! $extension) {
            return true;
        }

        if (! $extension = Admin::extension($extension)) {
            return false;
        }

        return $extension->enabled();
    }

    /**
     * 判断用户.
     *
     * @param  array|\Dcat\Admin\Models\Menu  $item
     * @return bool
     */
    protected function userCanSeeMenu($item)
    {
        $user = Admin::user();

        if (! $user || ! method_exists($user, 'canSeeMenu')) {
            return true;
        }

        return $user->canSeeMenu($item);
    }

    /**
     * 判断权限.
     *
     * @param $item
     * @return bool
     */
    protected function checkPermission($item)
    {
        $permissionIds = $item['permission_id'] ?? null;
        $roles = array_column(Helper::array($item['roles'] ?? []), 'slug');
        $permissions = array_column(Helper::array($item['permissions'] ?? []), 'slug');

        if (! $permissionIds && ! $roles && ! $permissions) {
            return true;
        }

        $user = Admin::user();

        if (! $user || $user->visible($roles)) {
            return true;
        }

        foreach (array_merge(Helper::array($permissionIds), $permissions) as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  string  $text
     * @return string
     */
    public function translate($text)
    {
        $titleTranslation = 'menu.titles.'.trim(str_replace(' ', '_', strtolower($text)));

        if (Lang::has($titleTranslation)) {
            return __($titleTranslation);
        }

        return $text;
    }

    /**
     * @param  string  $uri
     * @return string
     */
    public function getPath($uri)
    {
        return $uri
            ? (url()->isValidUrl($uri) ? $uri : admin_base_path($uri))
            : $uri;
    }

    public function clearPath($uri) {
        // $queryIndex = strpos($uri, '?');
        // $uri = $queryIndex ? substr($uri, 0, $queryIndex) : $uri;
        return trim($this->getPath($uri), '/');
    }

    /**
     * @param  string  $uri
     * @return string
     */
    public function getUrl($uri)
    {
        return $uri ? admin_url($uri) : $uri;
    }
}
