<?php
declare(strict_types=1);
namespace APP\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShoppingListRegisterPostRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\ShoppingList as ShoppingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CompletedShoppingList as CompletedShoppingModel;
use App\Http\Controllers\UserController;
use App\Http\Requests\UserRegisterPost;

//use Symfony\Component\HttpFoundation\StreamedResponse;

class ShoppingListController extends Controller
{
    /**
     * 一覧用の Illuminate\Database\Eloquent\Builder インスタンスの取得
     */
    protected function getListBuilder()
    {
        return ShoppingModel::where('user_id', Auth::id())
                    ->orderBy('name', 'asc')
                    ->orderBy('created_at')
                    ->orderBy('updated_at');
    }
    /**
     * 「買うもの」一覧ページ を表示する
     * 
     * @return \Illuminate\View\View
     */
    public function list()
    {
        // 1Pageあたりの表示アイテム数を設定
        $per_page = 3;
        
        // 一覧の取得
        //$list = TaskModel::where('user_id', Auth::id())
                        //->orderBy('priority', 'DESC')
                        //->orderBy('period')
                        //->orderBy('created_at');
                        //->paginate($per_page);
        $list = $this->getListBuilder()
                     ->paginate($per_page);
/*
$sql = $this->getListBuilder()
            ->toSql();
//echo "<pre>\n"; var_dump($sql, $list); exit
var_dump($sql);
*/
        //
        return view('shopping_list.list', ['list' => $list]);
    }
    
    /**
     * タスクの新規登録
     */
    public function register(ShoppingListRegisterPostRequest $request)
    {
        // validate済のデータ取得
        $datum = $request->validated();
        //
        //$user = Auth::user();
        //$id = Auth::id();
        //var_dump($datum, $user, $id); exit;
        
        // user_id の追加
        $datum['user_id'] = Auth::id();
        
        // テーブルへのINSERT
        try {
            $r = ShoppingModel::create($datum);
        } catch(\Throwable $e) {
            // XXX 本当はログに書く等の処理をする。今回は一端「出力する」だけ
            echo $e->getMessage();
            exit;
        }
    
        // 「買うもの」の登録成功
        $request->session()->flash('front.shopping_list_register_success', true);
    
        //
        return redirect('/shopping_list/list');
    }
    
    /**
     * 「単一タスク」Modelの取得
     */
    protected function getShoppingModel($shopping_list_id)
    {
        // shopping_list_idのレコードを取得する
        $shopping_list = ShoppingModel::find($shopping_list_id);
        if ($shopping_list_id === null) {
            return null;
            //return redirect('/task/list');
        }
        // 本人以外の「買うもの」ならNGとする
        if ($shopping_list->user_id !== Auth::id()) {
            return null;
            //return redirect('/task/list');
        }
        //
        return $shopping_list;
    }
    
    /**
     * 「単一のタスク」の表示
     */
    protected function singleShoppingRender($shopping_list_id, $template_name)
    {
        // shopping_list_idのレコードを取得する
        $task = $this->getShoppingModel($shopping_list_id);
        if ($task === null) {
            return redirect('/shopping_list/list');
        }
        // テンプレートに「取得したレコード」の情報を渡す
        return view($template_name, ['shoping_list' => $task]);
    }
    
    /**
     * 削除処理
     */
    public function delete(Request $request, $shopping_list_id)
    {
        // shopping_list_idのレコードを取得する
        $task = $this->getShoppingModel($shopping_list_id);
        
        // 「買うもの」を削除する
        if ($task !== null) {
            $task->delete();
            $request->session()->flash('front.shopping_list_delete_success', true);
        }
        
        // 一覧に遷移する
        return redirect('/shopping_list/list');
    }
    
    /**
     * 「買うもの」の完了
     */
    public function complete(Request $request, $shopping_list_id)
    {
        /* 「買うもの」を完了テーブルに移動させる */
        try {
            // トランザクション開始
            DB::beginTransaction();
            
            // shopping_list_idのレコードを取得する
            $shopping_list_id = $this->getShoppingModel($shopping_list_id);
            if ($shopping_list_id === null) {
                // shopping_list_idが不正なのでトランザクション終了
                throw new \Exception('');
            }
            
            // shopping_lists側を削除する
            $shopping_list_id->delete();
//var_dump($task->toArray()); exit;

            // completed_shopping_lists側にinsertする
            $dask_datum = $shopping_list_id->toArray();
            unset($dask_datum['created_at']);
            unset($dask_datum['updated_at']);
            $r = CompletedShoppingModel::create($dask_datum);
            if ($r === null) {
                // insertで失敗したのでトランザクション終了
                throw new \Exception('');
            }
//echo '処理成功'; exit;

            // トランザクション終了
            DB::commit();
            // 完了メッセージ出力
            $request->session()->flash('front.shopping_list_completed_success', true);
        } catch(\Throwable $e) {
//var_dump($e->getMessage()); exit;
            // トランザクション異常終了
            DB::rollBack();
            // 完了失敗メッセージ出力
            $request->session()->flash('front.shopping_list_completed_failure', true);
        }
                        
        // 一覧に遷移する
        return redirect('/shopping_list/list');
    }
}