<?php

namespace App\Http\Controllers;

use App\Models\TodoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        // DoneのTodoを表示するかどうか(Getパラメーターで判定)
        if ($request->has('done')) {
            // DoneのTodoを作成日順で表示する
            $todos = TodoItem::where(['user_id' => Auth::id(), 'is_done' => true])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // UndoneのTodoを作成日順で表示する
            $todos = TodoItem::where(['user_id' => Auth::id(), 'is_done' => false])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // compact関数を使うと、変数を配列にまとめて渡せる
       
        return view('todo.index', compact('todos'));
    }

    /**
     * Todo新規作成画面
     */
    public function create()
    {
        return view('todo.create');
    }

    /**
     * Todoを作成する
     */
    public function store(Request $request)
    {
        // バリデーション
       
        $request->validate([
            'title' => 'required|max:255',
        ]);

        // Todoを作成する
        //  DB上ではis_doneはデフォルトでfalseだが、明示的にfalseを指定する
        //  user_idは、Auth::id()でログインしているユーザーのIDを取得できる
        TodoItem::create(
            [
                'user_id' => Auth::id(),
                'title' => $request->title,
                'is_done' => false,
            ]
        );
        return redirect()->route('todo.index');
    }

    /**
     * Todoの表示
     */
    public function show($id)
    {
        $todo = TodoItem::find($id);

        // compact関数を使うと、変数を配列にまとめて渡せる
       
        return view('todo.show', compact('todo'));
    }

    /**
     * Todoの編集
     */
    public function edit($id)
    {
        $todo = TodoItem::find($id);

        // compact関数を使うと、変数を配列にまとめて渡せる
      
        return view('todo.edit', compact('todo'));
    }

    /**
     * Todoの更新
     */
    public function update($id, Request $request)
    {
        // バリデーション
       
        $request->validate([
            'title' => 'required|max:255',
        ]);

        // 必要な項目だけを書き換えて保存する
        $todo = TodoItem::find($id);
        $todo->title = $request->title;
        $todo->save();

        // route()で指定したURLにリダイレクトする
        return redirect()->route('todo.index');
    }

    public function destroy($id)
    {
        TodoItem::find($id)->delete();

        // route()で指定したURLにリダイレクトする
        return redirect()->route('todo.index');
    }

    /**
     * TodoをDoneにする
     */
    public function done($id)
    {
        // updateメソッドを使うと、指定した項目だけを更新できる
        //  今回は、is_doneだけを更新する。
        
        TodoItem::find($id)->update(['is_done' => true]);

        // route()で指定したURLにリダイレクトする
        return redirect()->route('todo.index');
    }

    /**
     * TodoをUnDoneにする
     */
    public function undone($id)
    {
        // updateメソッドを使うと、指定した項目だけを更新できる
        //  今回は、is_doneだけを更新する。
        
        TodoItem::find($id)->update(['is_done' => false]);

        // route()で指定したURLにリダイレクトする
        
        return redirect()->route('todo.index', ['done' => true]);
    }
}