<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sidebar
{
    use Container;

    /**
     * @var Crud
     */
    protected $_crud;

    /**
     * @var array $_menu
     */
    protected $_menu;

    public function __construct()
    {
        $this->_config = $this->config->item(strtolower(get_class($this)));
    }

    /**
     * @param Crud $crud
     */
    public function set_crud($crud)
    {
        $this->crud = $crud;
    }


    public function render()
    {
        $return_str = '';
        $roles = $this->auth->get_roles();
        foreach ($this->_config['menu'] as $menu) {
            if (empty($menu['children'])) {
                if ($menu['role'] && !in_array($menu['role'], $roles)) continue;
                $active = ($menu['role'] == $this->crud->get_module_name() ||
                    (empty($menu['role']) && strpos($menu['url'], '/' . $this->crud->get_module_name()) !== false)) ?
                    ' class="active"' : '';
                $return_str .= sprintf('
                    <li%s>
                        <a href="%s">
                            <span class="%s"></span>
                            <span class="sidebar-title">%s</span>
                        </a>
                    </li>', $active, base_url($menu['url']), $menu['icon'], $menu['name']);
            } else {
                $children = '';
                $open = false;
                foreach ($menu['children'] as $child) {
                    if ($child['role'] && !in_array($child['role'], $roles)) continue;
                    $active = $child['role'] == $this->crud->get_module_name() ? ' class="active"' : '';
                    if ($active) $open = true;
                    $children .= sprintf('<li%s>
                        <a href="%s"><span class="%s"></span>%s</a>
                    </li>', $active, $child['url'], $child['icon'], $child['name']);
                }
                if (!$children) continue;
                $return_str .= sprintf('
                <li%s>
                    <a class="accordion-toggle%s" href="%s">
                        <span class="%s"></span>
                        <span class="sidebar-title">%s</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        %s
                    </ul>
                </li>', $open ? ' class="active"' : '', $open ? ' menu-open' : '', base_url($menu['url']),
                    $menu['icon'], $menu['name'], $children);
            }
        }
        return $return_str;
    }
}