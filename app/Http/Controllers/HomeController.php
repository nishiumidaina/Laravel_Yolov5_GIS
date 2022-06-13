<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Spot;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = \Auth::user();
        $spots = Spot::get();
        return view('home', compact('user', 'spots'));
    }
        public function create()
    {
        $user = \Auth::user();
        $spots = Spot::get();
        return view('create', compact('user','spots'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $spot_id = Spot::insertGetId([
            'name' => $data['name'],
             'longitude' => $data['longitude'], 
             'latitude' => $data['latitude'],
             'url' => $data['url'],
             'ex' => $data['ex'],
             'status' => 'None'
        ]);
        // リダイレクト処理
        return redirect()->route('home');
    }
    public function edit($id){
        // IDをデータベースから取得
        $user = \Auth::user();
        $spot = Spot::where('id', $id)->first();
        $spots = Spot::get();
        //   dd($memo);
        //取得したメモをViewに渡す
        return view('edit',compact('user','spot','spots'));
    }

    public function delete(Request $request, $id)
    {
        $inputs = $request->all();
        // dd($inputs);
         Spot::where('id', $id)->delete();
        return redirect()->route('home')->with('success', '削除が完了しました！');
    }

    public function start(Request $request, $id)
    {
        $inputs = $request->all();
        //判定
        Spot::where('id', $id)->update(['status'=>'Start']);

        //$command = 'python Python/yolov5/detect.py --source "https://www.youtube.com/watch?v=DjdUEyjx8GM"> /dev/null &';
        //直接detect.pyを送ると終了できないため一時的にこちらを使う
        $command = 'python Python/yolov5_DeepSort_Pytorch/start.py> /dev/null &';
        exec($command) ;
        return redirect()->route('home')->with('success', '実行が完了しました！');
    }
    public function stop(Request $request, $id)
    {
        $inputs = $request->all();
        $spots = Spot::where('id', $id)->get();
        $spot_lis =  json_decode($spots , true); 
        //判定
        if ($spots[0]["status"]=="Run"){
           Spot::where('id', $id)->update(['status'=>'Stop']); 
           return redirect()->route('home')->with('success', '停止が完了しました！');
        }else{
            return redirect()->route('home')->with('success', '処理が開始されていません');
        }
        
        
    }

}
