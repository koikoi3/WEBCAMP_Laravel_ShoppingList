<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\CompletedShoppingList as CompletedShoppingModel;

class CompletedShoppingListController extends Controller
{
    //
     /**
     * 一覧用の Illuminate\Database\Eloquent\Builder インスタンスの取得
     */
    protected function getListBuilder()
    {
        return CompletedShoppingModel::where('user_id', Auth::id())
                     ->orderBy('name', 'asc')
                     ->orderBy('created_at', 'desc')
                     ->orderBy('updated_at');
    }
    
    /**
     * タスク一覧ページ を表示する
     * 
     * @return \Illuminate\View\View
     */
    public function list()
    {
        // 1Pageあたりの表示アイテム数を設定
        $per_page = 3;
        
        // 一覧の取得
        $list = $this->getListBuilder()
                     ->paginate($per_page);

/*
$sql = $this->getListBuilder()
            ->toSql();
//echo "<pre>\n"; var_dump($sql, $list); exit
var_dump($sql);
*/
        //
        return view('shopping_list.completed_shopping_list', ['list' => $list]);
    }
}
