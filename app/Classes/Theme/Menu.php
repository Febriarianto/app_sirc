<?php

namespace App\Classes\Theme;

use App\Models\Permissions\MenuManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;


class Menu
{

  public static function sidebar()
  {
    $menuManager = new MenuManager();
    $roleId = isset(Auth::user()->role_id) ? Auth::user()->role_id : NULL;
    $menu_list = $menuManager->get_menu_role((isset(Auth::user()->role_id) ? Auth::user()->role_id : 0));
    $roots = [];
    foreach ($menu_list as $v) :
      $v->parent_id == 0 ? array_push($roots, $v->id) : array_push($roots, $v->parent_id);
    endforeach;
    ds($roots);
    $roots = array_unique($roots);
    $roots = MenuManager::whereIn('id', $roots)
      ->orderBy('sort', 'asc')
      ->get();
    return self::tree($roots, $menu_list, $roleId);
  }

  public static function tree($roots, $menu_list, $roleId, $parentId = 0, $endChild = 0)
  {
    $html = '';
    foreach ($roots as $v) :
      if ($v->type == 'module') {
        $html .= '<li class="nav-item">
                     <a class="nav-link ' . ($v->path_url == request()->getPathInfo() ? 'active' : '') . '" href="' . $v->path_url . '">
                        <i class="' . ($v->icon ?? '') . '">
                        </i>
                        <p>' . $v->title . '</p>
                     </a></li>
               ';
      } elseif ($v->type == 'static') {
        $list_menu = $menu_list->where('parent_id', $v->id)->sortBy('sort');

        $get_path = $menu_list->where('path_url', request()->getPathInfo())->first();

        $html .= '<li class="nav-item ' . ($get_path !== null && $v->id == $get_path->parent_id ? 'menu-open' : '') . '">
                     <a class="nav-link ' . ($get_path !== null && $v->id == $get_path->parent_id ? 'active' : '') . '" href="#">
                        <i class="' . ($v->icon ?? '') . '">
                        </i>
                        <p>' . $v->title . '
                        <i class="right fas fa-angle-left"></i>
                        </p>
                     </a>
                <ul class="nav nav-treeview">
               ';


        foreach ($list_menu as $item) :
          $icon = isset($item->icon) ? '<i class="' . $item->icon . '"></i> ' : '<i class="far fa-circle nav-icon"></i>';
          $html .= '
            <li class="nav-item">
                <a class="nav-link ' . ($item->path_url == request()->getPathInfo() ? 'active' : '') . '"
                    href="' . URL::to($item->path_url) . '">
                      - ' . $icon . '
                    <p>' . $item->title . '</p>
                </a>
            </li>
          ';
        endforeach;
        $html .= '
        </ul>
        </li>';
      } elseif ($v->type == 'header') {
        $html .= '<li class="nav-item static-item">
                            <a class="nav-link static-item disabled text-start" href="#" tabindex="-1">
                                <span class="default-icon">' . $v->title . '</span>
                                <span class="mini-icon" data-bs-toggle="tooltip" data-bs-placement="right">-</span>
                            </a>
                        </li>
               ';
      } else {
        $html .= '<li><hr class="hr-horizontal"></li>';
      }
    endforeach;
    return $html;
  }
}
