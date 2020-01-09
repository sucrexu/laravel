<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\ScoreCard;

use App\Http\Controllers\Controller;
use App\ScoreCard\ScoreProject;
use App\ScoreCard\Score;
use Illuminate\Http\Request;
use App\ScoreCard\DataToScore as Data;

/**
 * Description of ScoreController
 *
 * @author Sucre.xu
 */
class ScoreController extends Controller
{

    public function index()
    {
        try {
            $project = ScoreProject::findOrFail(1);
            $score = new Score;
            $result = $score->setProject($project);
            if (count($result->where("data_id", 201)->get()))
            {
                $record = $result->firstOrCreate(["data_id" => 20], []);
                $record->fillable(["data_id", "project_id", "col1"]);
                $record->update(["col1" => "0-2"]);
                return $record;
            }
            $result->fill(["data_id" => 201, "project_id" => 1, "col1" => "0-3"]);
            $result->save();
            return $result;
        } catch (Exception $e) {
            return json_encode($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        $project = ScoreProject::findOrFail($request->get('project'));
        $score = new Score();
        $score->setProject($project);
        $score->setData(1);
        $score->fill(["col1" => '0', "col4" => '1', 'col3' => '0-1']);
        $score->save();
        return $score->col1;
    }

    public function show($id, Request $request)
    {
        $project = ScoreProject::findOrFail($request->get('project'));
        $score = new Score;
        $result = $score->setProject($project)->where('data_id', $id)->first();
        return $result;
    }

    public function store(Request $request)
    {
        try {
            $project = ScoreProject::findOrFail($request->project_id);
            $dataInstance = new Data;
            $data = $dataInstance->setProject($project)->findOrFail($request->data_id);
            if($data->owner!=$request->user()->name){
                throw new \Exception('You can\'t update data owned by '.$data->owner);
            }
            $score = new Score;
            $result = $score->setProject($project);
            $now = date('Y-m-d H:i:s');
            if (count($result->where("data_id", $request->data_id)->get()))
            {
                /**
                 * if record exists, update it; why firstOrCreate? actually the 'Create' part will never be triggered here,
                 * we only need the 'first-' part, only this way can the fillable() work.
                 * I don't know why this works, don't ask!
                 */
                $record = $result->firstOrCreate(["data_id" => $request->data_id], []);
                $record->fillable(explode(",", $project->score_fillable));
                $record->update($request->all());
                return '{"result":"success","score":' . $record . ',"msg":"score updated at ' . $now . '"}';
            }
            $result->fill($request->all());
            $result->save();
            $data->checked=1;
            $data->save();
            return '{"result":"success","score":' . $result . ',"msg":"score added at ' . $now . '"}';
        } catch (\Exception $e) {
            return '{"result":"failed","msg":"' . $e->getMessage() . '","score":null}';
        }
    }

    public function edit()
    {
        
    }

    public function update()
    {
        
    }

}