<?php


namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class MenuController extends BaseController
{
    /* TODO: complete getMenuItems so that it returns a nested menu structure from the database
    Requirements
    - the eloquent expressions should result in EXACTLY one SQL query no matter the nesting level or the amount of menu items.
    - post process your results in PHP
    - it should work for infinite level of depth (children of childrens children of childrens children, ...)
    - verify your solution with `php artisan test`
    - do a `git commit && git push` after you are done or when the time limit is over

    Hints:
    - imagine a maximum of a few hundred menu items
    - partial or not working answers also get graded so make sure you commit what you have

    Sample response on GET /menu:
    ```json
    [
        {
            "id": 1,
            "name": "All events",
            "url": "/events",
            "parent_id": null,
            "created_at": "2021-04-27T15:35:15.000000Z",
            "updated_at": "2021-04-27T15:35:15.000000Z",
            "children": [
                {
                    "id": 2,
                    "name": "Laracon",
                    "url": "/events/laracon",
                    "parent_id": 1,
                    "created_at": "2021-04-27T15:35:15.000000Z",
                    "updated_at": "2021-04-27T15:35:15.000000Z",
                    "children": [
                        {
                            "id": 3,
                            "name": "Illuminate your knowledge of the laravel code base",
                            "url": "/events/laracon/workshops/illuminate",
                            "parent_id": 2,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        },
                        {
                            "id": 4,
                            "name": "The new Eloquent - load more with less",
                            "url": "/events/laracon/workshops/eloquent",
                            "parent_id": 2,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        }
                    ]
                },
                {
                    "id": 5,
                    "name": "Reactcon",
                    "url": "/events/reactcon",
                    "parent_id": 1,
                    "created_at": "2021-04-27T15:35:15.000000Z",
                    "updated_at": "2021-04-27T15:35:15.000000Z",
                    "children": [
                        {
                            "id": 6,
                            "name": "#NoClass pure functional programming",
                            "url": "/events/reactcon/workshops/noclass",
                            "parent_id": 5,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        },
                        {
                            "id": 7,
                            "name": "Navigating the function jungle",
                            "url": "/events/reactcon/workshops/jungle",
                            "parent_id": 5,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        }
                    ]
                }
            ]
        }
    ]
     */

    public function getMenuItems() {
        $MenuItem = DB::table('menu_items as parent_menu')
            ->selectRaw('parent_menu.id, parent_menu.name, parent_menu.url, parent_menu.parent_id,parent_menu.created_at,parent_menu.updated_at,child_menu.id as child_menuid, child_menu.name as child_menuname, child_menu.url as child_menuurl, child_menu.parent_id as child_menupid,child_menu.created_at as child_menucr,child_menu.updated_at as child_menuup')
            ->join('menu_items AS child_menu','child_menu.parent_id','=','parent_menu.id')
            ->get();

        $arr = [];
        foreach ($MenuItem as $key => $item) {
            if ($item->parent_id == null) {
                $arr[] = [
                    'id'         => $item->id,
                    'name'       => $item->name,
                    'parent_id'  => $item->parent_id,
                    'url'        => $item->url,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'children'   => $this->getChildren($MenuItem, $item->id)
                ];
            }
        }
        return json_encode($arr);
        throw new \Exception('implement in coding task 3');
    }

    public function getChildren($arr, $parent_id)
    {
        $child = [];
        foreach ($arr as $item) {
            if ($item->parent_id == $parent_id) {
                $child = [
                    'id'         => $item->id,
                    'name'       => $item->name,
                    'parent_id'  => $item->parent_id,
                    'url'        => $item->url,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'children'   => $this->getChildren([$item], $item->id)
                ];
            }
        }
        return $child;
    }
}
